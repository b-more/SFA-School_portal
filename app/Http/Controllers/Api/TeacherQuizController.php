<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClassSection;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TeacherQuizController extends Controller
{
    private function getTeacher(): ?Teacher
    {
        return Teacher::where('user_id', Auth::id())->where('is_active', true)->first();
    }

    private function ownedQuiz(int $quizId, Teacher $teacher): Quiz
    {
        $quiz = Quiz::where('id', $quizId)->where('assigned_by', $teacher->id)->first();
        if (!$quiz) {
            abort(404, 'Quiz not found.');
        }
        return $quiz;
    }

    public function index()
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $quizzes = Quiz::where('assigned_by', $teacher->id)
            ->with(['classSection', 'subject'])
            ->withCount(['questions', 'attempts'])
            ->latest()
            ->get()
            ->map(function ($q) {
                // best attempt per student, then average those percentages
                $best = QuizAttempt::where('quiz_id', $q->id)
                    ->where('status', 'submitted')
                    ->selectRaw('student_id, MAX(percentage) as best_pct')
                    ->groupBy('student_id')
                    ->pluck('best_pct');
                return [
                    'id' => $q->id,
                    'title' => $q->title,
                    'class_section' => $q->classSection?->name,
                    'subject' => $q->subject?->name,
                    'status' => $q->status,
                    'time_limit_minutes' => $q->time_limit_minutes,
                    'due_at' => $q->due_at?->format('d M Y H:i'),
                    'total_points' => $q->total_points,
                    'num_questions' => $q->questions_count,
                    'students_attempted' => $best->count(),
                    'average_percentage' => $best->count() ? round($best->avg(), 1) : null,
                    'created_at' => $q->created_at?->format('d M Y'),
                ];
            });

        return response()->json($quizzes);
    }

    public function store(Request $request)
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        $data = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:5000',
            'class_section_id' => 'required|integer',
            'subject_id' => 'nullable|integer',
            'grade_id' => 'nullable|integer',
            'time_limit_minutes' => 'nullable|integer|min:1|max:600',
            'due_at' => 'nullable|date',
            'shuffle_questions' => 'boolean',
            'questions' => 'required|array|min:1',
            'questions.*.question_text' => 'required|string|max:2000',
            'questions.*.type' => 'required|in:mcq,true_false',
            'questions.*.points' => 'nullable|integer|min:1|max:100',
            'questions.*.options' => 'required|array|min:2|max:6',
            'questions.*.options.*.option_text' => 'required|string|max:1000',
            'questions.*.options.*.is_correct' => 'boolean',
        ]);

        // Each question must have exactly one correct option.
        foreach ($data['questions'] as $i => $q) {
            $correct = collect($q['options'])->filter(fn ($o) => !empty($o['is_correct']))->count();
            if ($correct !== 1) {
                throw ValidationException::withMessages([
                    "questions.$i" => "Question " . ($i + 1) . " must have exactly one correct answer.",
                ]);
            }
        }

        $gradeId = $data['grade_id'] ?? ClassSection::find($data['class_section_id'])?->grade_id;
        $totalPoints = collect($data['questions'])->sum(fn ($q) => $q['points'] ?? 1);

        $quiz = DB::transaction(function () use ($data, $teacher, $gradeId, $totalPoints) {
            $quiz = Quiz::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'assigned_by' => $teacher->id,
                'class_section_id' => $data['class_section_id'],
                'subject_id' => $data['subject_id'] ?? null,
                'grade_id' => $gradeId,
                'time_limit_minutes' => $data['time_limit_minutes'] ?? null,
                'total_points' => $totalPoints,
                'shuffle_questions' => $data['shuffle_questions'] ?? false,
                'status' => 'published',
                'due_at' => $data['due_at'] ?? null,
            ]);

            foreach ($data['questions'] as $qi => $q) {
                $question = $quiz->questions()->create([
                    'question_text' => $q['question_text'],
                    'type' => $q['type'],
                    'points' => $q['points'] ?? 1,
                    'position' => $qi,
                ]);
                foreach ($q['options'] as $oi => $o) {
                    $question->options()->create([
                        'option_text' => $o['option_text'],
                        'is_correct' => !empty($o['is_correct']),
                        'position' => $oi,
                    ]);
                }
            }

            return $quiz;
        });

        return response()->json(['message' => 'Quiz created.', 'id' => $quiz->id], 201);
    }

    public function show(int $quiz)
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $quiz = $this->ownedQuiz($quiz, $teacher);
        $quiz->load(['questions.options', 'classSection', 'subject']);

        return response()->json([
            'id' => $quiz->id,
            'title' => $quiz->title,
            'description' => $quiz->description,
            'class_section' => $quiz->classSection?->name,
            'subject' => $quiz->subject?->name,
            'status' => $quiz->status,
            'time_limit_minutes' => $quiz->time_limit_minutes,
            'due_at' => $quiz->due_at?->toIso8601String(),
            'total_points' => $quiz->total_points,
            'questions' => $quiz->questions->map(fn ($q) => [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'type' => $q->type,
                'points' => $q->points,
                'options' => $q->options->map(fn ($o) => [
                    'id' => $o->id,
                    'option_text' => $o->option_text,
                    'is_correct' => $o->is_correct,
                ]),
            ]),
        ]);
    }

    public function results(int $quiz)
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $quiz = $this->ownedQuiz($quiz, $teacher);

        $students = Student::where('class_section_id', $quiz->class_section_id)
            ->where('enrollment_status', 'active')
            ->orderBy('name')
            ->get(['id', 'name']);

        $attempts = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('status', 'submitted')
            ->get();
        $byStudent = $attempts->groupBy('student_id');

        $rows = $students->map(function ($s) use ($byStudent) {
            $list = $byStudent->get($s->id);
            if (!$list) {
                return ['student_id' => $s->id, 'name' => $s->name, 'attempts' => 0, 'best_score' => null, 'best_percentage' => null, 'last_attempt' => null];
            }
            $best = $list->sortByDesc('percentage')->first();
            return [
                'student_id' => $s->id,
                'name' => $s->name,
                'attempts' => $list->count(),
                'best_score' => $best->score,
                'best_percentage' => $best->percentage,
                'last_attempt' => $list->sortByDesc('submitted_at')->first()?->submitted_at?->format('d M Y H:i'),
            ];
        });

        $attemptedPct = $rows->whereNotNull('best_percentage')->pluck('best_percentage');

        return response()->json([
            'title' => $quiz->title,
            'total_points' => $quiz->total_points,
            'class_size' => $students->count(),
            'students_attempted' => $attemptedPct->count(),
            'average_percentage' => $attemptedPct->count() ? round($attemptedPct->avg(), 1) : null,
            'results' => $rows->values(),
        ]);
    }

    public function close(int $quiz)
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $quiz = $this->ownedQuiz($quiz, $teacher);
        $quiz->update(['status' => 'closed']);
        return response()->json(['message' => 'Quiz closed.']);
    }

    public function destroy(int $quiz)
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $quiz = $this->ownedQuiz($quiz, $teacher);
        $quiz->delete();
        return response()->json(['message' => 'Quiz deleted.']);
    }
}
