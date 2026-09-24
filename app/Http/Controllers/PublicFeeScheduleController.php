<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\FeeStructure;
use App\Models\Grade;
use App\Models\SchoolSection;
use App\Models\Term;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

/**
 * Public-facing endpoint that lets parents (and anyone) download the
 * school's fee schedule as a PDF, without logging in. The heavy lifting
 * stays in the same pdf.fee-structure blade used by /admin/fee-structures,
 * so a future design change flows through both places.
 */
class PublicFeeScheduleController extends Controller
{
    /**
     * GET /fee-schedule — landing page with the picker.
     */
    public function index()
    {
        $sections = SchoolSection::orderBy('id')
            ->get(['id', 'code', 'name'])
            ->reject(fn ($s) => $this->feeStructuresForSection($s->id)->isEmpty())
            ->values();

        $terms = Term::with('academicYear')
            ->orderByDesc('academic_year_id')
            ->orderBy('id')
            ->get(['id', 'name', 'academic_year_id']);

        $currentTerm = Term::where('is_current', true)->first()
            ?? Term::orderByDesc('id')->first();

        return view('public.fee-schedule', [
            'sections'    => $sections,
            'terms'       => $terms,
            'currentTerm' => $currentTerm,
        ]);
    }

    /**
     * GET /fee-schedule/download?section_id=&term_id= — streams the PDF.
     */
    public function download(Request $request)
    {
        $data = $request->validate([
            'section_id' => 'required|integer',
            'term_id'    => 'required|integer',
        ]);

        $section = SchoolSection::find($data['section_id']);
        $term    = Term::with('academicYear')->find($data['term_id']);

        if (! $section || ! $term) {
            return back()->withErrors(['section_id' => 'Please pick a valid section and term.']);
        }

        // Find the fee structure that matches this section + term. If the
        // fee_structures row is keyed by grade_id instead of school_section_id
        // we walk the grades in the section and take the first match.
        $fs = FeeStructure::query()
            ->where('academic_year_id', $term->academic_year_id)
            ->where('term_id', $term->id)
            ->where('school_section_id', $section->id)
            ->first();

        if (! $fs) {
            $gradeIds = Grade::where('school_section_id', $section->id)->pluck('id');
            $fs = FeeStructure::query()
                ->where('academic_year_id', $term->academic_year_id)
                ->where('term_id', $term->id)
                ->whereIn('grade_id', $gradeIds)
                ->first();
        }

        if (! $fs) {
            return back()->withErrors([
                'term_id' => 'No fee schedule has been published for ' . $section->name . ' — ' . $term->name . '.',
            ])->withInput();
        }

        $pdf = Pdf::loadView('pdf.fee-structure', [
            'feeStructure'  => $fs,
            'academicYear'  => optional($term->academicYear)->name ?? '',
            'term'          => $term->name,
            'grade'         => $section->name,
            'schoolName'    => 'St. Francis Of Assisi Private School',
            'schoolLogo'    => public_path('images/logo.png'),
            'schoolAddress' => 'Plot No 1310/4 East Kamenza, Chililabombwe, Zambia',
            'schoolContact' => 'Phone: +260 972 266 217, Email: stfrancisofassisi.sfa@gmail.com',
        ]);

        $filename = sprintf(
            'sfa-fee-schedule-%s-%s.pdf',
            \Illuminate\Support\Str::slug($section->name),
            \Illuminate\Support\Str::slug($term->name . '-' . (optional($term->academicYear)->name ?? ''))
        );

        return $pdf->download($filename);
    }

    private function feeStructuresForSection(int $sectionId)
    {
        return FeeStructure::query()
            ->where(function ($q) use ($sectionId) {
                $q->where('school_section_id', $sectionId)
                  ->orWhereIn('grade_id', Grade::where('school_section_id', $sectionId)->pluck('id'));
            })
            ->pluck('id');
    }
}
