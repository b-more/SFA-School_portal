<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Assessment;
use App\Models\AssessmentSubmission;
use App\Models\ParentGuardian;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ParentAssessmentController extends Controller
{
    private function getParent(): ?ParentGuardian
    {
        return ParentGuardian::where('user_id', Auth::id())->first();
    }

    private function validateChild(Student $student): void
    {
        $parent = $this->getParent();
        $owns = $parent && $parent->students()
            ->where('students.id', $student->id)
            ->where('enrollment_status', 'active')->exists();
        if (!$owns) {
            abort(403, 'Access denied.');
        }
    }

    private function assessmentForChild(Assessment $assessment, Student $student): void
    {
        if ((int) $assessment->class_section_id !== (int) $student->class_section_id) {
            abort(403, "This assessment is not for your child's class.");
        }
    }

    public function index(Student $student)
    {
        $this->validateChild($student);
        $assessments = Assessment::where('class_section_id', $student->class_section_id)
            ->where('status', 'published')
            ->with('subject')->withCount('questions')
            ->latest()->get();

        $subs = AssessmentSubmission::whereIn('assessment_id', $assessments->pluck('id'))
            ->where('student_id', $student->id)->get()->keyBy('assessment_id');

        return response()->json($assessments->map(function ($a) use ($subs) {
            $sub = $subs->get($a->id);
            return [
                'id' => $a->id,
                'title' => $a->title,
                'subject' => $a->subject?->name,
                'num_questions' => $a->questions_count,
                'total_marks' => $a->total_marks,
                'due_at' => $a->due_at?->toIso8601String(),
                'closed' => $a->status === 'closed' || ($a->due_at && $a->due_at->isPast()),
                'submission_status' => $sub?->status,         // null | submitted | marked
                'percentage' => $sub && $sub->status === 'marked' ? $sub->percentage : null,
            ];
        }));
    }

    public function show(Student $student, Assessment $assessment)
    {
        $this->validateChild($student);
        $this->assessmentForChild($assessment, $student);
        $assessment->load('questions');

        $sub = AssessmentSubmission::where('assessment_id', $assessment->id)
            ->where('student_id', $student->id)->with('answers')->first();
        $answersByQ = $sub ? $sub->answers->keyBy('assessment_question_id') : collect();

        $marksByCrit = collect();
        if ($sub && $sub->status === 'marked') {
            $assessment->load('questions.criteria');
            $marksByCrit = $sub->criterionMarks()->get()->keyBy('assessment_question_criterion_id');
        }

        return response()->json([
            'id' => $assessment->id,
            'title' => $assessment->title,
            'description' => $assessment->description,
            'subject' => $assessment->subject?->name,
            'total_marks' => $assessment->total_marks,
            'time_limit_minutes' => $assessment->time_limit_minutes,
            'closed' => $assessment->status === 'closed' || ($assessment->due_at && $assessment->due_at->isPast()),
            'submission_status' => $sub?->status,
            'percentage' => $sub && $sub->status === 'marked' ? $sub->percentage : null,
            'score' => $sub && $sub->status === 'marked' ? $sub->total_score : null,
            'questions' => $assessment->questions->map(function ($q) use ($answersByQ, $sub, $marksByCrit) {
                $row = [
                    'id' => $q->id,
                    'question_text' => $q->question_text,
                    'points' => $q->points,
                    'response_text' => $answersByQ->get($q->id)?->response_text,
                ];
                // Reveal the rubric breakdown only after the teacher has marked it.
                if ($sub && $sub->status === 'marked') {
                    $row['criteria'] = $q->criteria->map(fn ($c) => [
                        'criterion' => $c->criterion,
                        'max_marks' => $c->max_marks,
                        'awarded' => $marksByCrit->get($c->id)?->marks_awarded,
                    ]);
                }
                return $row;
            }),
        ]);
    }

    public function submit(Request $request, Student $student, Assessment $assessment)
    {
        $this->validateChild($student);
        $this->assessmentForChild($assessment, $student);

        if ($assessment->status === 'closed' || ($assessment->due_at && $assessment->due_at->isPast())) {
            return response()->json(['message' => 'This assessment is closed.'], 422);
        }
        $existing = AssessmentSubmission::where('assessment_id', $assessment->id)
            ->where('student_id', $student->id)->first();
        if ($existing) {
            return response()->json(['message' => 'You have already submitted this assessment.'], 422);
        }

        $data = $request->validate([
            'answers' => 'present|array',
            'answers.*.question_id' => 'required|integer',
            'answers.*.response_text' => 'nullable|string|max:20000',
        ]);

        $questionIds = $assessment->questions()->pluck('id');
        $responses = collect($data['answers'])->pluck('response_text', 'question_id');

        DB::transaction(function () use ($assessment, $student, $questionIds, $responses) {
            $sub = AssessmentSubmission::create([
                'assessment_id' => $assessment->id,
                'student_id' => $student->id,
                'submitted_at' => now(),
                'status' => 'submitted',
                'total_marks' => $assessment->total_marks,
            ]);
            foreach ($questionIds as $qid) {
                $sub->answers()->create([
                    'assessment_question_id' => $qid,
                    'response_text' => $responses->get($qid),
                ]);
            }
        });

        return response()->json(['message' => 'Submitted. Your teacher will mark it.']);
    }
}
