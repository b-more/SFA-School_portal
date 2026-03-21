<?php

namespace App\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PageGuideController extends Controller
{
    protected array $guides = [
        'teacher-dashboard' => [
            'title' => 'Teacher Dashboard Guide',
            'view' => 'pdf.guides.teacher-dashboard',
        ],
        'mark-attendance' => [
            'title' => 'Mark Attendance Guide',
            'view' => 'pdf.guides.mark-attendance',
        ],
        'enter-results' => [
            'title' => 'Enter Results Guide',
            'view' => 'pdf.guides.enter-results',
        ],
        'students' => [
            'title' => 'Students Management Guide',
            'view' => 'pdf.guides.students',
        ],
        'attendances' => [
            'title' => 'Attendance Records Guide',
            'view' => 'pdf.guides.attendances',
        ],
        'results' => [
            'title' => 'Results Management Guide',
            'view' => 'pdf.guides.results',
        ],
        'profile' => [
            'title' => 'My Profile Guide',
            'view' => 'pdf.guides.profile',
        ],
        'homework' => [
            'title' => 'Homework Management Guide',
            'view' => 'pdf.guides.homework',
        ],
    ];

    public function download(string $page)
    {
        if (!isset($this->guides[$page])) {
            abort(404, 'Guide not found.');
        }

        $guide = $this->guides[$page];

        $pdf = Pdf::loadView($guide['view'], [
            'title' => $guide['title'],
            'schoolName' => 'St. Francis of Assisi Private School',
        ])->setPaper('A4', 'portrait');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            str_replace(' ', '-', strtolower($guide['title'])) . '.pdf'
        );
    }
}
