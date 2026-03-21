<?php

namespace App\Filament\Resources\EventResource\Widgets;

use App\Models\Event;
use Filament\Widgets\Widget;

class UpcomingEventsWidget extends Widget
{
    protected static string $view = 'filament.widgets.upcoming-events';

    protected int | string | array $columnSpan = 'full';

    public function getEvents(): array
    {
        return Event::query()
            ->whereIn('status', ['upcoming', 'ongoing'])
            ->where('start_date', '>=', now()->startOfDay())
            ->orderBy('start_date')
            ->limit(6)
            ->get()
            ->map(fn (Event $event) => [
                'id' => $event->id,
                'title' => $event->title,
                'start_date' => $event->start_date,
                'end_date' => $event->end_date,
                'location' => $event->location,
                'category' => $event->category,
                'status' => $event->status,
                'day' => $event->start_date->format('d'),
                'month' => $event->start_date->format('M'),
                'weekday' => $event->start_date->format('D'),
                'time' => $event->start_date->format('h:i A'),
                'is_today' => $event->start_date->isToday(),
                'is_this_week' => $event->start_date->isBetween(now()->startOfWeek(), now()->endOfWeek()),
                'days_away' => (int) now()->startOfDay()->diffInDays($event->start_date->startOfDay(), false),
            ])
            ->toArray();
    }

    public function getTotalUpcoming(): int
    {
        return Event::where('status', 'upcoming')
            ->where('start_date', '>=', now())
            ->count();
    }

    public function getTotalOngoing(): int
    {
        return Event::where('status', 'ongoing')->count();
    }

    public function getTotalThisMonth(): int
    {
        return Event::whereIn('status', ['upcoming', 'ongoing'])
            ->whereMonth('start_date', now()->month)
            ->whereYear('start_date', now()->year)
            ->count();
    }
}
