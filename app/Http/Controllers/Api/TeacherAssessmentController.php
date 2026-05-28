<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentSubmission;
use App\Models\ClassSection;
use App\Models\EczAssessmentSetting;
use App\Models\QuestionBankItem;
use App\Models\SbaMark;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Term;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TeacherAssessmentController extends Controller
{
    private function getTeacher(): ?Teacher
    {
        return Teacher::where('user_id', Auth::id())->where('is_active', true)->first();
    }

    private function ownedAssessment(int $id, Teacher $teacher): Assessment
    {
        $a = Assessment::where('id', $id)->where('created_by', $teacher->id)->first();
        if (!$a) {
            abort(404, 'Assessment not found.');
        }
        return $a;
    }

    // ---- Assessments (scenario / Theory) ----

    public function index()
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $items = Assessment::where('created_by', $teacher->id)
            ->with(['classSection', 'subject'])
            ->withCount(['questions', 'submissions'])
            ->latest()->get()
            ->map(function ($a) {
                $marked = $a->submissions()->where('status', 'marked')->count();
                return [
                    'id' => $a->id,
                    'title' => $a->title,
                    'class_section' => $a->classSection?->name,
                    'subject' => $a->subject?->name,
                    'component' => $a->component,
                    'status' => $a->status,
                    'total_marks' => $a->total_marks,
                    'num_questions' => $a->questions_count,
                    'submissions' => $a->submissions_count,
                    'to_mark' => $a->submissions_count - $marked,
                    'created_at' => $a->created_at?->format('d M Y'),
                ];
            });
        return response()->json($items);
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
            'questions' => 'array',
            'questions.*.question_text' => 'required|string',
            'questions.*.criteria' => 'required|array|min:1',
            'questions.*.criteria.*.criterion' => 'required|string|max:1000',
            'questions.*.criteria.*.max_marks' => 'required|integer|min:1|max:100',
            'bank_item_ids' => 'array',
            'bank_item_ids.*' => 'integer',
        ]);

        // Expand any chosen scenario bank items into question definitions.
        $questions = $data['questions'] ?? [];
        if (!empty($data['bank_item_ids'])) {
            $bankItems = QuestionBankItem::whereIn('id', $data['bank_item_ids'])
                ->where('type', 'scenario')->with('rubricCriteria')->get();
            foreach ($bankItems as $bi) {
                $questions[] = [
                    'question_text' => $bi->question_text,
                    'source_bank_item_id' => $bi->id,
                    'criteria' => $bi->rubricCriteria->map(fn ($c) => ['criterion' => $c->criterion, 'max_marks' => $c->max_marks])->all(),
                ];
            }
        }
        if (empty($questions)) {
            throw ValidationException::withMessages(['questions' => 'Add at least one scenario question (inline or from the bank).']);
        }

        $totalMarks = collect($questions)->sum(fn ($q) => collect($q['criteria'])->sum('max_marks'));

        $assessment = DB::transaction(function () use ($data, $teacher, $questions, $totalMarks) {
            $assessment = Assessment::create([
                'created_by' => $teacher->id,
                'class_section_id' => $data['class_section_id'],
                'subject_id' => $data['subject_id'] ?? null,
                'grade_id' => $data['grade_id'] ?? ClassSection::find($data['class_section_id'])?->grade_id,
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'component' => 'theory',
                'time_limit_minutes' => $data['time_limit_minutes'] ?? null,
                'due_at' => $data['due_at'] ?? null,
                'status' => 'published',
                'total_marks' => $totalMarks,
            ]);
            foreach (array_values($questions) as $qi => $q) {
                $question = $assessment->questions()->create([
                    'question_text' => $q['question_text'],
                    'points' => collect($q['criteria'])->sum('max_marks'),
                    'position' => $qi,
                    'source_bank_item_id' => $q['source_bank_item_id'] ?? null,
                ]);
                foreach (array_values($q['criteria']) as $ci => $c) {
                    $question->criteria()->create([
                        'criterion' => $c['criterion'],
                        'max_marks' => $c['max_marks'],
                        'position' => $ci,
                    ]);
                }
            }
            return $assessment;
        });

        return response()->json(['message' => 'Assessment created.', 'id' => $assessment->id], 201);
    }

    public function show(int $assessment)
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $a = $this->ownedAssessment($assessment, $teacher);
        $a->load(['questions.criteria', 'classSection', 'subject']);
        return response()->json([
            'id' => $a->id,
            'title' => $a->title,
            'description' => $a->description,
            'class_section' => $a->classSection?->name,
            'subject' => $a->subject?->name,
            'status' => $a->status,
            'total_marks' => $a->total_marks,
            'questions' => $a->questions->map(fn ($q) => [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'points' => $q->points,
                'criteria' => $q->criteria->map(fn ($c) => ['criterion' => $c->criterion, 'max_marks' => $c->max_marks]),
            ]),
        ]);
    }

    public function submissions(int $assessment)
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $a = $this->ownedAssessment($assessment, $teacher);
        $subs = $a->submissions()->with('student:id,name')->latest('submitted_at')->get();
        return response()->json([
            'title' => $a->title,
            'total_marks' => $a->total_marks,
            'submissions' => $subs->map(fn ($s) => [
                'id' => $s->id,
                'student' => $s->student?->name,
                'submitted_at' => $s->submitted_at?->format('d M Y H:i'),
                'status' => $s->status,
                'score' => $s->total_score,
                'percentage' => $s->percentage,
            ]),
        ]);
    }

    public function submissionDetail(int $submission)
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $sub = AssessmentSubmission::with(['assessment', 'answers', 'criterionMarks'])->find($submission);
        if (!$sub || $sub->assessment->created_by !== $teacher->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $sub->assessment->load('questions.criteria');
        $answersByQ = $sub->answers->keyBy('assessment_question_id');
        $marksByCrit = $sub->criterionMarks->keyBy('assessment_question_criterion_id');

        return response()->json([
            'submission_id' => $sub->id,
            'status' => $sub->status,
            'total_marks' => $sub->assessment->total_marks,
            'questions' => $sub->assessment->questions->map(fn ($q) => [
                'id' => $q->id,
                'question_text' => $q->question_text,
                'response_text' => $answersByQ->get($q->id)?->response_text,
                'criteria' => $q->criteria->map(fn ($c) => [
                    'id' => $c->id,
                    'criterion' => $c->criterion,
                    'max_marks' => $c->max_marks,
                    'awarded' => $marksByCrit->get($c->id)?->marks_awarded,
                ]),
            ]),
        ]);
    }

    public function markSubmission(Request $request, int $submission)
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $sub = AssessmentSubmission::with('assessment.questions.criteria')->find($submission);
        if (!$sub || $sub->assessment->created_by !== $teacher->id) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $data = $request->validate([
            'marks' => 'required|array',
            'marks.*.criterion_id' => 'required|integer',
            'marks.*.awarded' => 'required|numeric|min:0',
        ]);

        // Valid criteria + their caps for this assessment.
        $criteria = $sub->assessment->questions->flatMap->criteria->keyBy('id');
        $given = collect($data['marks'])->keyBy('criterion_id');

        $total = DB::transaction(function () use ($sub, $criteria, $given) {
            $sub->criterionMarks()->delete();
            $total = 0;
            foreach ($criteria as $c) {
                $awarded = (float) ($given->get($c->id)['awarded'] ?? 0);
                $awarded = max(0, min($awarded, $c->max_marks)); // clamp to the criterion cap
                $total += $awarded;
                $sub->criterionMarks()->create([
                    'assessment_question_criterion_id' => $c->id,
                    'marks_awarded' => $awarded,
                ]);
            }
            $max = $sub->assessment->total_marks;
            $sub->update([
                'total_score' => $total,
                'total_marks' => $max,
                'percentage' => $max > 0 ? round($total / $max * 100, 2) : 0,
                'status' => 'marked',
                'marked_by' => null,
                'marked_at' => now(),
            ]);
            return $total;
        });

        return response()->json(['message' => 'Marked.', 'score' => $total, 'total_marks' => $sub->assessment->total_marks]);
    }

    public function close(int $assessment)
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $this->ownedAssessment($assessment, $teacher)->update(['status' => 'closed']);
        return response()->json(['message' => 'Assessment closed.']);
    }

    public function destroy(int $assessment)
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $this->ownedAssessment($assessment, $teacher)->delete();
        return response()->json(['message' => 'Assessment deleted.']);
    }

    // ---- SBA gradebook (teacher-entered) ----

    public function sbaGradebook(Request $request)
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $request->validate(['class_section_id' => 'required|integer', 'subject_id' => 'required|integer']);
        $termId = Term::where('is_active', true)->first()?->id;
        $students = Student::where('class_section_id', $request->integer('class_section_id'))
            ->where('enrollment_status', 'active')->orderBy('name')->get(['id', 'name']);
        $marks = SbaMark::where('subject_id', $request->integer('subject_id'))
            ->where('term_id', $termId)
            ->whereIn('student_id', $students->pluck('id'))
            ->get()->keyBy('student_id');
        return response()->json([
            'students' => $students->map(fn ($s) => [
                'student_id' => $s->id,
                'name' => $s->name,
                'score' => $marks->get($s->id)?->score,
                'max_score' => $marks->get($s->id)?->max_score ?? 100,
            ]),
        ]);
    }

    public function saveSba(Request $request)
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $data = $request->validate([
            'class_section_id' => 'required|integer',
            'subject_id' => 'required|integer',
            'marks' => 'required|array',
            'marks.*.student_id' => 'required|integer',
            'marks.*.score' => 'nullable|numeric|min:0',
            'marks.*.max_score' => 'nullable|numeric|min:1',
        ]);
        $termId = Term::where('is_active', true)->first()?->id;
        $yearId = \App\Models\AcademicYear::where('is_active', true)->first()?->id;
        $saved = 0;
        foreach ($data['marks'] as $m) {
            if (!isset($m['score']) || $m['score'] === null || $m['score'] === '') {
                continue;
            }
            SbaMark::updateOrCreate(
                ['student_id' => $m['student_id'], 'subject_id' => $data['subject_id'], 'term_id' => $termId],
                [
                    'class_section_id' => $data['class_section_id'],
                    'recorded_by' => $teacher->id,
                    'score' => $m['score'],
                    'max_score' => $m['max_score'] ?? 100,
                    'academic_year_id' => $yearId,
                ]
            );
            $saved++;
        }
        return response()->json(['message' => "Saved {$saved} SBA mark(s)."]);
    }

    // ---- ECZ weighting settings ----

    public function getSettings()
    {
        $s = EczAssessmentSetting::current();
        return response()->json(['theory_weight' => $s->theory_weight, 'sba_weight' => $s->sba_weight]);
    }

    public function saveSettings(Request $request)
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $data = $request->validate([
            'theory_weight' => 'required|integer|min:0|max:100',
            'sba_weight' => 'required|integer|min:0|max:100',
        ]);
        if ($data['theory_weight'] + $data['sba_weight'] !== 100) {
            throw ValidationException::withMessages(['sba_weight' => 'Theory and SBA weights must add up to 100.']);
        }
        $s = EczAssessmentSetting::current();
        $s->update(['theory_weight' => $data['theory_weight'], 'sba_weight' => $data['sba_weight'], 'updated_by' => $teacher->id]);
        return response()->json(['message' => 'Weighting updated.']);
    }

    // ---- 70/30 subject summary ----

    public function subjectSummary(Request $request)
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $request->validate(['class_section_id' => 'required|integer', 'subject_id' => 'required|integer']);
        $classId = $request->integer('class_section_id');
        $subjectId = $request->integer('subject_id');
        $termId = Term::where('is_active', true)->first()?->id;
        $settings = EczAssessmentSetting::current();

        $students = Student::where('class_section_id', $classId)
            ->where('enrollment_status', 'active')->orderBy('name')->get(['id', 'name']);

        // Theory: average of the pupil's marked theory-assessment percentages for this class+subject.
        $assessmentIds = Assessment::where('class_section_id', $classId)
            ->where('subject_id', $subjectId)->where('component', 'theory')->pluck('id');
        $theory = AssessmentSubmission::whereIn('assessment_id', $assessmentIds)
            ->where('status', 'marked')
            ->selectRaw('student_id, AVG(percentage) as theory_pct')
            ->groupBy('student_id')->pluck('theory_pct', 'student_id');

        $sba = SbaMark::where('subject_id', $subjectId)->where('term_id', $termId)
            ->whereIn('student_id', $students->pluck('id'))->get()->keyBy('student_id');

        $rows = $students->map(function ($s) use ($theory, $sba, $settings) {
            $theoryPct = $theory->has($s->id) ? round((float) $theory->get($s->id), 2) : null;
            $sbaMark = $sba->get($s->id);
            $sbaPct = $sbaMark ? $sbaMark->percentage() : null;
            $final = null;
            if ($theoryPct !== null && $sbaPct !== null) {
                $final = round(($theoryPct * $settings->theory_weight + $sbaPct * $settings->sba_weight) / 100, 2);
            }
            return [
                'student_id' => $s->id,
                'name' => $s->name,
                'theory_pct' => $theoryPct,
                'sba_pct' => $sbaPct,
                'final_pct' => $final,
            ];
        });

        return response()->json([
            'theory_weight' => $settings->theory_weight,
            'sba_weight' => $settings->sba_weight,
            'results' => $rows->values(),
        ]);
    }
}
