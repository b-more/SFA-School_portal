<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AcademicYear;
use App\Models\QuestionBankItem;
use App\Models\SubjectTeaching;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class TeacherQuestionBankController extends Controller
{
    private function getTeacher(): ?Teacher
    {
        return Teacher::where('user_id', Auth::id())->where('is_active', true)->first();
    }

    /** Subject ids the teacher teaches in the active academic year. */
    private function taughtSubjectIds(Teacher $teacher): array
    {
        $yearId = AcademicYear::where('is_active', true)->first()?->id;
        return SubjectTeaching::where('teacher_id', $teacher->id)
            ->when($yearId, fn ($q) => $q->where('academic_year_id', $yearId))
            ->pluck('subject_id')->filter()->unique()->values()->all();
    }

    private function ownedItem(int $id, Teacher $teacher): QuestionBankItem
    {
        $item = QuestionBankItem::where('id', $id)->where('created_by', $teacher->id)->first();
        if (!$item) {
            abort(404, 'Question not found.');
        }
        return $item;
    }

    public function meta()
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $yearId = AcademicYear::where('is_active', true)->first()?->id;
        $teachings = SubjectTeaching::where('teacher_id', $teacher->id)
            ->when($yearId, fn ($q) => $q->where('academic_year_id', $yearId))
            ->with(['subject', 'classSection.grade'])
            ->get();

        $subjects = $teachings->map(fn ($t) => ['id' => $t->subject_id, 'name' => $t->subject?->name])
            ->filter(fn ($s) => $s['id'])->unique('id')->values();
        $grades = $teachings->map(fn ($t) => ['id' => $t->classSection?->grade_id, 'name' => $t->classSection?->grade?->name])
            ->filter(fn ($g) => $g['id'])->unique('id')->values();

        return response()->json([
            'subjects' => $subjects,
            'grades' => $grades,
            'curricula' => ['ordinary', 'cbc'],
            'components' => ['theory', 'sba'],
            'types' => ['mcq', 'true_false', 'structured', 'scenario'],
            'difficulties' => ['easy', 'medium', 'hard'],
        ]);
    }

    public function index(Request $request)
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $subjectIds = $this->taughtSubjectIds($teacher);

        $items = QuestionBankItem::query()
            ->where(function ($w) use ($teacher, $subjectIds) {
                $w->where('created_by', $teacher->id)
                  ->orWhere(function ($s) use ($subjectIds) {
                      $s->where('is_shared', true)->whereIn('subject_id', $subjectIds);
                  });
            })
            ->when($request->filled('subject_id'), fn ($q) => $q->where('subject_id', $request->integer('subject_id')))
            ->when($request->filled('grade_id'), fn ($q) => $q->where('grade_id', $request->integer('grade_id')))
            ->when($request->filled('curriculum'), fn ($q) => $q->where('curriculum', $request->input('curriculum')))
            ->when($request->filled('component'), fn ($q) => $q->where('component', $request->input('component')))
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->input('type')))
            ->when($request->filled('topic'), fn ($q) => $q->where('topic', 'like', '%' . $request->input('topic') . '%'))
            ->when($request->filled('q'), fn ($q) => $q->where(function ($s) use ($request) {
                $term = '%' . $request->input('q') . '%';
                $s->where('question_text', 'like', $term)->orWhere('topic', 'like', $term);
            }))
            ->with(['subject', 'grade'])
            ->withCount(['options', 'rubricCriteria'])
            ->latest()
            ->limit(200)
            ->get()
            ->map(fn ($i) => [
                'id' => $i->id,
                'mine' => $i->created_by === $teacher->id,
                'subject' => $i->subject?->name,
                'grade' => $i->grade?->name,
                'topic' => $i->topic,
                'curriculum' => $i->curriculum,
                'component' => $i->component,
                'type' => $i->type,
                'question_text' => $i->question_text,
                'max_marks' => $i->max_marks,
                'difficulty' => $i->difficulty,
                'is_shared' => $i->is_shared,
                'parts' => $i->isObjective() ? $i->options_count : $i->rubric_criteria_count,
            ]);

        return response()->json($items);
    }

    public function store(Request $request)
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $data = $this->validatePayload($request);
        $item = DB::transaction(fn () => $this->persist(new QuestionBankItem(['created_by' => $teacher->id]), $data));
        return response()->json(['message' => 'Question saved to bank.', 'id' => $item->id], 201);
    }

    public function show(int $item)
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $subjectIds = $this->taughtSubjectIds($teacher);
        $q = QuestionBankItem::where('id', $item)
            ->where(function ($w) use ($teacher, $subjectIds) {
                $w->where('created_by', $teacher->id)
                  ->orWhere(fn ($s) => $s->where('is_shared', true)->whereIn('subject_id', $subjectIds));
            })
            ->with(['options', 'rubricCriteria', 'subject', 'grade'])
            ->first();
        if (!$q) {
            return response()->json(['message' => 'Question not found.'], 404);
        }
        return response()->json([
            'id' => $q->id,
            'mine' => $q->created_by === $teacher->id,
            'subject_id' => $q->subject_id,
            'grade_id' => $q->grade_id,
            'subject' => $q->subject?->name,
            'grade' => $q->grade?->name,
            'topic' => $q->topic,
            'curriculum' => $q->curriculum,
            'component' => $q->component,
            'type' => $q->type,
            'question_text' => $q->question_text,
            'max_marks' => $q->max_marks,
            'difficulty' => $q->difficulty,
            'model_answer' => $q->model_answer,
            'is_shared' => $q->is_shared,
            'options' => $q->options->map(fn ($o) => ['option_text' => $o->option_text, 'is_correct' => $o->is_correct]),
            'rubric' => $q->rubricCriteria->map(fn ($c) => ['criterion' => $c->criterion, 'max_marks' => $c->max_marks]),
        ]);
    }

    public function update(Request $request, int $item)
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $model = $this->ownedItem($item, $teacher);
        $data = $this->validatePayload($request);
        DB::transaction(function () use ($model, $data) {
            $model->options()->delete();
            $model->rubricCriteria()->delete();
            $this->persist($model, $data);
        });
        return response()->json(['message' => 'Question updated.']);
    }

    public function destroy(int $item)
    {
        $teacher = $this->getTeacher();
        if (!$teacher) {
            return response()->json(['message' => 'Not found.'], 404);
        }
        $this->ownedItem($item, $teacher)->delete();
        return response()->json(['message' => 'Question deleted.']);
    }

    private function validatePayload(Request $request): array
    {
        $data = $request->validate([
            'subject_id' => 'nullable|integer',
            'grade_id' => 'nullable|integer',
            'topic' => 'nullable|string|max:255',
            'curriculum' => 'required|in:ordinary,cbc',
            'component' => 'nullable|in:theory,sba',
            'type' => 'required|in:mcq,true_false,structured,scenario',
            'question_text' => 'required|string',
            'max_marks' => 'nullable|integer|min:1|max:200',
            'difficulty' => 'nullable|in:easy,medium,hard',
            'model_answer' => 'nullable|string',
            'is_shared' => 'boolean',
            'options' => 'array',
            'options.*.option_text' => 'required|string|max:1000',
            'options.*.is_correct' => 'boolean',
            'rubric' => 'array',
            'rubric.*.criterion' => 'required|string|max:1000',
            'rubric.*.max_marks' => 'required|integer|min:1|max:100',
        ]);

        // ECZ new curriculum has no MCQ / structured recall — CBC items are scenario-based.
        if ($data['curriculum'] === 'cbc' && $data['type'] !== 'scenario') {
            throw ValidationException::withMessages(['type' => 'New (CBC) curriculum questions must be scenario-based.']);
        }

        $objective = in_array($data['type'], ['mcq', 'true_false']);
        if ($objective) {
            $opts = $data['options'] ?? [];
            if (count($opts) < 2) {
                throw ValidationException::withMessages(['options' => 'Provide at least two options.']);
            }
            if (collect($opts)->filter(fn ($o) => !empty($o['is_correct']))->count() !== 1) {
                throw ValidationException::withMessages(['options' => 'Mark exactly one correct answer.']);
            }
        } else {
            if (count($data['rubric'] ?? []) < 1) {
                throw ValidationException::withMessages(['rubric' => 'Add at least one marking criterion.']);
            }
        }

        return $data;
    }

    private function persist(QuestionBankItem $item, array $data): QuestionBankItem
    {
        $objective = in_array($data['type'], ['mcq', 'true_false']);
        $maxMarks = $objective
            ? ($data['max_marks'] ?? 1)
            : collect($data['rubric'])->sum('max_marks');

        $item->fill([
            'subject_id' => $data['subject_id'] ?? null,
            'grade_id' => $data['grade_id'] ?? null,
            'topic' => $data['topic'] ?? null,
            'curriculum' => $data['curriculum'],
            'component' => $data['component'] ?? null,
            'type' => $data['type'],
            'question_text' => $data['question_text'],
            'max_marks' => $maxMarks,
            'difficulty' => $data['difficulty'] ?? null,
            'model_answer' => $data['model_answer'] ?? null,
            'is_shared' => $data['is_shared'] ?? false,
        ])->save();

        if ($objective) {
            foreach (array_values($data['options']) as $i => $o) {
                $item->options()->create([
                    'option_text' => $o['option_text'],
                    'is_correct' => !empty($o['is_correct']),
                    'position' => $i,
                ]);
            }
        } else {
            foreach (array_values($data['rubric']) as $i => $c) {
                $item->rubricCriteria()->create([
                    'criterion' => $c['criterion'],
                    'max_marks' => $c['max_marks'],
                    'position' => $i,
                ]);
            }
        }

        return $item;
    }
}
