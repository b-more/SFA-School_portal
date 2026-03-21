<?php

namespace App\Console\Commands;

use App\Models\AcademicYear;
use App\Models\TimetablePeriod;
use Illuminate\Console\Command;

class SeedTimetablePeriods extends Command
{
    protected $signature = 'timetable:seed-periods {--year= : Academic year ID (defaults to current)}';
    protected $description = 'Seed timetable periods for the school day schedule';

    public function handle(): int
    {
        $yearId = $this->option('year') ?? AcademicYear::current()?->id;

        if (!$yearId) {
            $this->error('No academic year found. Please create one first.');
            return 1;
        }

        $year = AcademicYear::find($yearId);
        $this->info("Seeding periods for academic year: {$year->name}");

        $existing = TimetablePeriod::where('academic_year_id', $yearId)->count();
        if ($existing > 0) {
            if (!$this->confirm("There are already {$existing} periods for this year. Delete and re-seed?")) {
                $this->info('Aborted.');
                return 0;
            }
            TimetablePeriod::where('academic_year_id', $yearId)->delete();
        }

        $periods = [
            ['name' => 'Assembly',   'short_name' => 'ASM',  'type' => 'assembly',    'start_time' => '07:15', 'end_time' => '07:30', 'order' => 1],
            ['name' => 'Period 1',   'short_name' => 'P1',   'type' => 'lesson',      'start_time' => '07:30', 'end_time' => '08:10', 'order' => 2],
            ['name' => 'Period 2',   'short_name' => 'P2',   'type' => 'lesson',      'start_time' => '08:10', 'end_time' => '08:50', 'order' => 3],
            ['name' => 'Period 3',   'short_name' => 'P3',   'type' => 'lesson',      'start_time' => '08:50', 'end_time' => '09:30', 'order' => 4],
            ['name' => 'Period 4',   'short_name' => 'P4',   'type' => 'lesson',      'start_time' => '09:30', 'end_time' => '10:10', 'order' => 5],
            ['name' => 'Break',      'short_name' => 'BRK',  'type' => 'tea_break',   'start_time' => '10:10', 'end_time' => '10:30', 'order' => 6],
            ['name' => 'Period 5',   'short_name' => 'P5',   'type' => 'lesson',      'start_time' => '10:30', 'end_time' => '11:10', 'order' => 7],
            ['name' => 'Period 6',   'short_name' => 'P6',   'type' => 'lesson',      'start_time' => '11:10', 'end_time' => '11:50', 'order' => 8],
            ['name' => 'Period 7',   'short_name' => 'P7',   'type' => 'lesson',      'start_time' => '11:50', 'end_time' => '12:30', 'order' => 9],
            ['name' => 'Period 8',   'short_name' => 'P8',   'type' => 'lesson',      'start_time' => '12:30', 'end_time' => '13:10', 'order' => 10],
            ['name' => 'Lunch',      'short_name' => 'LCH',  'type' => 'lunch_break', 'start_time' => '13:10', 'end_time' => '13:45', 'order' => 11],
            ['name' => 'Period 9',   'short_name' => 'P9',   'type' => 'lesson',      'start_time' => '13:45', 'end_time' => '14:25', 'order' => 12],
            ['name' => 'Period 10',  'short_name' => 'P10',  'type' => 'lesson',      'start_time' => '14:25', 'end_time' => '15:05', 'order' => 13],
            ['name' => 'Period 11',  'short_name' => 'P11',  'type' => 'lesson',      'start_time' => '15:05', 'end_time' => '16:00', 'order' => 14],
        ];

        foreach ($periods as $period) {
            TimetablePeriod::create(array_merge($period, [
                'academic_year_id' => $yearId,
                'is_active' => true,
            ]));
        }

        $this->info('Created ' . count($periods) . ' timetable periods successfully.');
        return 0;
    }
}
