<?php

namespace App\Http\Middleware;

use App\Models\Homework;
use App\Models\HomeworkSubmission;
use App\Models\ParentGuardian;
use App\Models\Student;
use App\Models\StudentFee;
use Closure;
use Illuminate\Http\Request;

/**
 * Resource-level guard for parent token / session downloads.
 * Staff users keep full access. A parent can only read records that belong
 * to one of their own children — preventing IDOR via hand-edited URLs.
 *
 * Pair with TokenFromQuery so $request->user() is resolved either via the
 * web session (staff) or the ?token= query (parent app).
 */
class EnsureStudentResourceAccess
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (! $user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        // Anyone who is not a Parent (admin / teacher / accountant / etc.)
        // keeps the full access they had before this guard.
        if (! $user->isParent()) {
            return $next($request);
        }

        $childIds = $this->childIdsOf($user);

        // Resource is keyed by a specific student → strict id match.
        $studentId = $this->resolveStudentId($request);
        if ($studentId !== null) {
            return in_array($studentId, $childIds, true)
                ? $next($request)
                : $this->forbid($request);
        }

        // Resource is class-wide (homework assignment) → allow if any child
        // is in that grade. Doesn't expose other students' work.
        $gradeId = $this->resolveHomeworkGradeId($request);
        if ($gradeId !== null) {
            return in_array($gradeId, $this->childGradeIdsOf($childIds), true)
                ? $next($request)
                : $this->forbid($request);
        }

        // No resolvable parent-relevant resource (e.g. a bulk staff-only
        // endpoint) — deny.
        return $this->forbid($request);
    }

    private function resolveStudentId(Request $request): ?int
    {
        $route = $request->route();
        if (! $route) {
            return null;
        }

        // StudentFee-style params (route names used in this app: studentFee, fee).
        foreach (['studentFee', 'fee'] as $name) {
            $bound = $route->parameter($name);
            if ($bound instanceof StudentFee) {
                return (int) $bound->student_id;
            }
            if (is_numeric($bound)) {
                $sf = StudentFee::find($bound);
                if ($sf) {
                    return (int) $sf->student_id;
                }
            }
        }

        // Student-style params.
        $student = $route->parameter('student');
        if ($student instanceof Student) {
            return (int) $student->id;
        }
        if (is_numeric($student)) {
            return (int) $student;
        }

        // Homework submission params — own child's submission only.
        foreach (['submission', 'record'] as $name) {
            $bound = $route->parameter($name);
            if ($bound instanceof HomeworkSubmission) {
                return (int) $bound->student_id;
            }
            if (is_numeric($bound)) {
                $sub = HomeworkSubmission::find($bound);
                if ($sub) {
                    return (int) $sub->student_id;
                }
            }
        }

        return null;
    }

    private function resolveHomeworkGradeId(Request $request): ?int
    {
        $h = $request->route()?->parameter('homework');
        if ($h instanceof Homework) {
            return (int) $h->grade_id;
        }
        if (is_numeric($h)) {
            $row = Homework::find($h);
            return $row ? (int) $row->grade_id : null;
        }

        return null;
    }

    private function childIdsOf($user): array
    {
        $pg = ParentGuardian::where('user_id', $user->id)->first();
        if (! $pg) {
            return [];
        }

        return $pg->students()->pluck('students.id')->all();
    }

    private function childGradeIdsOf(array $childIds): array
    {
        if (! $childIds) {
            return [];
        }

        return Student::whereIn('id', $childIds)->pluck('grade_id')->filter()->unique()->values()->all();
    }

    private function forbid(Request $request)
    {
        if ($request->expectsJson() || $request->ajax() || $request->wantsJson()) {
            return response()->json([
                'message' => 'You do not have access to this record.',
            ], 403);
        }

        return response('Access denied. You can only view records for your own children.', 403);
    }
}
