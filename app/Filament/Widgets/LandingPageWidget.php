<?php

namespace App\Filament\Widgets;

use App\Constants\RoleConstants;
use App\Models\Event;
use App\Models\News;
use App\Models\SchoolSettings;
use App\Models\Testimonial;
use Filament\Widgets\Widget;

class LandingPageWidget extends Widget
{
    protected static string $view = 'filament.widgets.landing-page-widget';

    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    public static function canView(): bool
    {
        $user = auth()->user();
        return $user && $user->role_id === RoleConstants::ADMIN;
    }

    protected function getViewData(): array
    {
        $settings = SchoolSettings::first();
        $custom   = (array) ($settings->custom_settings ?? []);
        $landing  = (array) ($custom['landing'] ?? []);

        return [
            'testimonialCount'     => Testimonial::count(),
            'activeTestimonials'   => Testimonial::active()->count(),
            'publishedNews'        => News::where('status', 'published')->count(),
            'upcomingEvents'       => Event::where('start_date', '>=', now()->startOfDay())->count(),
            'lastUpdated'          => optional($settings)->settings_last_updated_at,
            'announcementActive'   => filled($landing['announcement_text'] ?? null),
            'manageUrl'            => '/admin/manage-landing-page',
            'testimonialsUrl'      => '/admin/testimonials',
            'newsUrl'              => '/admin/news',
            'eventsUrl'            => '/admin/events',
            'previewUrl'           => url('/'),
        ];
    }
}
