<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Teacher;
use App\Constants\RoleConstants;
use Illuminate\Support\Facades\Auth;

class MyPayslips extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-banknotes';
    protected static string $view = 'filament.pages.my-payslips';
    protected static ?string $navigationLabel = 'My Payslips';
    protected static ?string $title = 'My Payslips';
    protected static ?string $navigationGroup = 'Staff Management';
    protected static ?int $navigationSort = 50;

    public function getPayslips()
    {
        $user = Auth::user();

        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if ($teacher && $teacher->employee_id) {
                $employee = Employee::find($teacher->employee_id);
            }
        }

        if (!$employee) {
            return collect();
        }

        return Payroll::where('employee_id', $employee->id)
            ->with('academicYear')
            ->orderByDesc('year')
            ->orderByRaw("FIELD(month, 'December','November','October','September','August','July','June','May','April','March','February','January')")
            ->get();
    }

    public function getEmployee()
    {
        $user = Auth::user();

        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if ($teacher && $teacher->employee_id) {
                $employee = Employee::find($teacher->employee_id);
            }
        }

        return $employee;
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) {
            return false;
        }
        // Allow teachers and all teaching roles
        return in_array($user->role_id, RoleConstants::teaching());
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
