<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ParentGuardian;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParentQuizController extends Controller
{
    private const GRACE_SECONDS = 15;

    private function getParent(): ?ParentGuardian
    {
        return ParentGuardian::where('user_id', Auth::id())->first();
    }

    private function validateChild(Student $student): void
    {
        $parent = $this->getParent();
        $ownsChild = $parent && $parent->students()
            ->where('students.id', $student->id)
            ->where('enrollment_status', 'active')
            ->exists();
        if (!$ownsChild) {
            abort(403, 'Access denied.');
        }
    }

    private function quizForChild(Quiz $quiz, Student $student): void
    {
        if ((int) $quiz->class_section_id !== (int) $student->class_section_id) {
            abort(403, 'This quiz is not assigned to your child\'s class.');
        }
    }

    public function index(Student $student)
    {
        $this->validateChild($student);

        $quizzes = Quiz::where('class_section_id', $student->class_section_id)
            ->where('status', 'published')
            ->with('subject')
            ->withCount('questions')
            ->latest()
            ->get();

        $items = $quizzes->map(function ($q) use ($student) {
            $attempts = QuizAttempt::where('quiz_id', $q->id)
                ->where('student_id', $student->id)
                ->get();
            $bestSubmitted = $attempts->where('status', 'submitted')->sortByDesc('percentage')->first();
            $inProgress = $attempts->firstWhere('status', 'in_progress');
            return [
                'id' => $q->id,
                'title' => $q->title,
                'subject' => $q->subject?->name,
                'num_questions' => $q->questions_count,
                'total_points' => $q->total_points,
                'time_limit_minutes' => $q->time_limit_minutes,
                'due_at' => $q->due_at?->toIso8601String(),
                'closed' => $q->due_at ? $q->due_at->isPast() : false,
                'attempts' => $attempts->where('status', 'submitted')->count(),
                'best_score' => $bestSubmitted?->score,
                'best_percentage' => $bestSubmitted?->percentage,
                'in_progress' => (bool) $inProgress,
            ];
        });

        return response()->json($items);
    }

    public function show(Student $student, Quiz $quiz)
    {
        $this->validateChild($student);
        $this->quizForChild($quiz, $student);

        $quiz->load('questions.options');
        $questions = $quiz->questions;
        if ($quiz->shuffle_questions) {
            $questions = $questions->shuffle();
        }

        $inProgress = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->first();

        return response()->json([
            'id' => $quiz->id,
            'title' => $quiz->title,
            'description' => $quiz->description,
            'subject' => $quiz->subject?->name,
            'total_points' => $quiz->total_points,
            'time_limit_minutes' => $quiz->time_limit_minutes,
            'due_at' => $quiz->due_at?->toIso8601String(),
            'closed' => $quiz->status === 'closed' || ($quiz->due_at && $quiz->due_at->isPast()),
            'server_time' => now()->toIso8601String(),
            'in_progress_attempt' => $inProgress ? [
                'attempt_id' => $inProgress->id,
                'started_at' => $inProgress->started_at?->toIso8601String(),
                'deadline' => $this->deadline($quiz, $inProgress)?->toIso8601String(),
            ] : null,
            'questions' => $questions->map(fn ($q) => [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'type' => $q->type,
                'points' => $q->points,
                // NOTE: is_correct deliberately omitted — answers never leave the server before submit.
                'options' => $q->options->map(fn ($o) => [
                    'id' => $o->id,
                    'option_text' => $o->option_text,
                ])->values(),
            ])->values(),
        ]);
    }

    public function start(Student $student, Quiz $quiz)
    {
        $this->validateChild($student);
        $this->quizForChild($quiz, $student);

        if ($quiz->status === 'closed' || ($quiz->due_at && $quiz->due_at->isPast())) {
            return response()->json(['message' => 'This quiz is closed.'], 422);
        }

        $attempt = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->first();

        if (!$attempt) {
            $attempt = QuizAttempt::create([
                'quiz_id' => $quiz->id,
                'student_id' => $student->id,
                'started_at' => now(),
                'total_points' => $quiz->total_points,
                'status' => 'in_progress',
            ]);
        }

        return response()->json([
            'attempt_id' => $attempt->id,
            'started_at' => $attempt->started_at->toIso8601String(),
            'server_time' => now()->toIso8601String(),
            'deadline' => $this->deadline($quiz, $attempt)?->toIso8601String(),
        ]);
    }

    public function submit(Request $request, Student $student, Quiz $quiz)
    {
        $this->validateChild($student);
        $this->quizForChild($quiz, $student);

        $data = $request->validate([
            'attempt_id' => 'required|integer',
            'answers' => 'present|array',
            'answers.*.question_id' => 'required|integer',
            'answers.*.option_id' => 'nullable|integer',
        ]);

        $attempt = QuizAttempt::where('id', $data['attempt_id'])
            ->where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->first();

        if (!$attempt) {
            return response()->json(['message' => 'Attempt not found.'], 404);
        }
        if ($attempt->status !== 'in_progress') {
            return response()->json(['message' => 'This attempt was already submitted.'], 422);
        }

        // Server-side timing: a timed quiz can't be beaten by tampering with the client clock.
        $autoSubmitted = false;
        $deadline = $this->deadline($quiz, $attempt);
        if ($deadline && now()->gt($deadline->copy()->addSeconds(self::GRACE_SECONDS))) {
            $autoSubmitted = true;
        }

        $quiz->load('questions.options');
        // Map the pupil's chosen option per question.
        $chosen = collect($data['answers'])->pluck('option_id', 'question_id');

        $score = 0;
        $review = [];

        DB::transaction(function () use ($quiz, $attempt, $chosen, $autoSubmitted, &$score, &$review) {
            $attempt->answers()->delete();
            foreach ($quiz->questions as $question) {
                $correct = $question->options->firstWhere('is_correct', true);
                $selectedId = $chosen->get($question->id);
                // Only accept an option that actually belongs to this question.
                $validSelection = $selectedId && $question->options->contains('id', (int) $selectedId)
                    ? (int) $selectedId : null;
                $isCorrect = $validSelection !== null && $correct && $validSelection === $correct->id;
                $awarded = $isCorrect ? $question->points : 0;
                $score += $awarded;

                $attempt->answers()->create([
                    'quiz_question_id' => $question->id,
                    'selected_option_id' => $validSelection,
                    'is_correct' => $isCorrect,
                    'points_awarded' => $awarded,
                ]);

                $review[] = [
                    'question_id' => $question->id,
                    'selected_option_id' => $validSelection,
                    'correct_option_id' => $correct?->id,
                    'is_correct' => $isCorrect,
                    'points' => $question->points,
                    'points_awarded' => $awarded,
                ];
            }

            $total = $quiz->total_points;
            $attempt->update([
                'score' => $score,
                'percentage' => $total > 0 ? round($score / $total * 100, 2) : 0,
                'submitted_at' => now(),
                'status' => 'submitted',
                'auto_submitted' => $autoSubmitted,
            ]);
        });

        $bestPct = QuizAttempt::where('quiz_id', $quiz->id)
            ->where('student_id', $student->id)
            ->where('status', 'submitted')
            ->max('percentage');

        return response()->json([
            'score' => $score,
            'total_points' => $quiz->total_points,
            'percentage' => $quiz->total_points > 0 ? round($score / $quiz->total_points * 100, 2) : 0,
            'auto_submitted' => $autoSubmitted,
            'best_percentage' => $bestPct,
            'review' => $review,
        ]);
    }

    private function deadline(Quiz $quiz, QuizAttempt $attempt): ?Carbon
    {
        if (!$quiz->time_limit_minutes || !$attempt->started_at) {
            return null;
        }
        return $attempt->started_at->copy()->addMinutes($quiz->time_limit_minutes);
    }
}
