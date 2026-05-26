<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Constants\RoleConstants;

class HeadTeacherDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-building-office-2';
    protected static string $view = 'filament.pages.head-teacher-dashboard';
    protected static ?string $navigationLabel = 'Head Teacher Dashboard';
    protected static ?string $slug = 'head-teacher-dashboard';
    protected static ?int $navigationSort = 2;

    public function getHeadDashboardData(): array
    {
        $td = new TeacherDashboard();
        return $td->getHeadDashboardData();
    }

    public static function canAccess(): bool
    {
        $user = auth()->user();
        if (!$user) return false;
        return in_array($user->role_id, [
            RoleConstants::HEAD_TEACHER_PRIMARY,
            RoleConstants::HEAD_TEACHER_SECONDARY,
            RoleConstants::DEPUTY_HEAD_PRIMARY,
            RoleConstants::DEPUTY_HEAD_SECONDARY,
        ]);
    }

    public static function shouldRegisterNavigation(): bool
    {
        return static::canAccess();
    }
}
