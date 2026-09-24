<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BotConversation;
use App\Models\ParentGuardian;
use App\Models\PaymentTransaction;
use App\Models\Student;
use App\Models\StudentFee;
use App\Services\BotReceiptService;
use App\Services\PaymentInitiationService;
use App\Support\PhoneNormalizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Internal, shared-secret-gated API used by the WhatsApp bot.
 *
 * Read-only + payment-initiation only. Every response is JSON. All info-level
 * log lines mask the phone number and never include the shared secret or a
 * fee balance. Debug-level log lines may include full details (call sites
 * shipping to prod should keep LOG_LEVEL >= info).
 */
class BotApiController extends Controller
{
    /**
     * POST /api/bot/identify  { phone }
     *
     * Match a WhatsApp sender against a ParentGuardian. Since Filament stores
     * raw text and the migration path produced `+260…`, we look up on both
     * `+260XXXXXXXXX` and `260XXXXXXXXX`, and also compare `users.username`
     * (which is always `260…` after the identity migration).
     */
    public function identify(Request $request)
    {
        $data = $request->validate(['phone' => 'required|string|min:6|max:20']);

        $plus = PhoneNormalizer::normalize($data['phone']);     // +260XXXXXXXXX
        $digits = PhoneNormalizer::digits($data['phone']);      // 260XXXXXXXXX

        $masked = $this->maskPhone($plus ?? $data['phone']);
        Log::channel('bot_api')->info('identify', ['phone' => $masked]);

        if (! $plus || ! $digits) {
            return response()->json(['matched' => false, 'reason' => 'invalid_format']);
        }

        $guardian = ParentGuardian::query()
            ->where(function ($q) use ($plus, $digits) {
                $q->where('phone', $plus)
                    ->orWhere('phone', $digits)
                    ->orWhere('alternate_phone', $plus)
                    ->orWhere('alternate_phone', $digits);
            })
            ->orWhereHas('user', function ($q) use ($digits, $plus) {
                $q->where('phone', $plus)
                    ->orWhere('phone', $digits)
                    ->orWhere('username', $digits);
            })
            ->with(['students' => function ($q) {
                $q->where('enrollment_status', 'active')
                    ->with(['classSection.grade', 'grade']);
            }])
            ->first();

        if (! $guardian) {
            return response()->json(['matched' => false]);
        }

        return response()->json([
            'matched' => true,
            'guardian_id' => $guardian->id,
            'guardian_name' => trim(($guardian->first_name ?? '') . ' ' . ($guardian->last_name ?? '')) ?: ($guardian->name ?? 'Guardian'),
            'students' => $guardian->students->map(function (Student $s) {
                $className = optional($s->classSection)->name
                    ?? optional($s->classSection?->grade)->name
                    ?? optional($s->grade)->name
                    ?? '';
                return [
                    'id' => $s->id,
                    'name' => $s->name,
                    'class' => $className,
                    'grade' => optional($s->grade)->name ?? optional($s->classSection?->grade)->name ?? '',
                ];
            })->values(),
        ]);
    }

    /**
     * POST /api/bot/balance  { guardian_id, student_id }
     */
    public function balance(Request $request)
    {
        $data = $request->validate([
            'guardian_id' => 'required|integer',
            'student_id' => 'required|integer',
        ]);

        $student = $this->authorisedStudent((int) $data['guardian_id'], (int) $data['student_id']);
        if (! $student) {
            Log::channel('bot_api')->warning('balance denied — student/guardian mismatch', [
                'guardian_id' => $data['guardian_id'],
                'student_id' => $data['student_id'],
            ]);
            return response()->json(['message' => 'Not authorised for this student.'], 403);
        }

        $fees = StudentFee::with('feeCategory')
            ->where('student_id', $student->id)
            ->orderBy('created_at')
            ->get();

        $byCategory = $fees->groupBy('fee_category_id');
        $categories = [];
        foreach ($byCategory as $catId => $rows) {
            $cat = $rows->first()->feeCategory;
            if (! $cat) {
                continue;
            }
            $categories[] = [
                'code' => $cat->code,
                'name' => $cat->name,
                'balance' => (float) $rows->sum('balance'),
                'paid' => (float) $rows->sum('amount_paid'),
                'items' => $rows->filter(fn ($f) => (float) $f->balance > 0)->map(fn ($f) => [
                    'period' => $f->period_label ?: '—',
                    'balance' => (float) $f->balance,
                ])->values(),
            ];
        }

        return response()->json([
            'student_id' => $student->id,
            'student_name' => $student->name,
            'total_balance' => (float) $fees->sum('balance'),
            'total_paid' => (float) $fees->sum('amount_paid'),
            'is_locked' => $student->hasArrears(),
            'categories' => $categories,
        ]);
    }

    /**
     * POST /api/bot/pay  { guardian_id, student_id, amount, payer_msisdn }
     */
    public function pay(Request $request, PaymentInitiationService $initiator)
    {
        $data = $request->validate([
            'guardian_id' => 'required|integer',
            'student_id' => 'required|integer',
            'amount' => 'required|numeric|min:1',
            'payer_msisdn' => 'required|string|min:6|max:20',
        ]);

        $student = $this->authorisedStudent((int) $data['guardian_id'], (int) $data['student_id']);
        if (! $student) {
            return response()->json(['success' => false, 'message' => 'Not authorised for this student.'], 403);
        }

        $msisdn = PhoneNormalizer::digits($data['payer_msisdn']);
        if (! $msisdn) {
            return response()->json(['success' => false, 'message' => 'Please provide a valid Zambian mobile number.'], 422);
        }

        $amount = round((float) $data['amount'], 2);
        if ($amount < 1) {
            return response()->json(['success' => false, 'message' => 'Minimum payment is K1.00.'], 422);
        }

        Log::channel('bot_api')->info('pay initiate', [
            'student_id' => $student->id,
            'guardian_id' => $data['guardian_id'],
        ]);

        $result = $initiator->initiate(
            student: $student,
            amount: $amount,
            payerMsisdn: $msisdn,
            channel: 'whatsapp_bot',
            referencePrefix: 'WA'
        );

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'payment_reference' => $result['payment_reference'] ?? null,
            'amount' => $result['amount'] ?? null,
            'is_delayed' => $result['is_delayed'] ?? false,
        ], $result['success'] ? 200 : 422);
    }

    /**
     * POST /api/bot/receipt  { guardian_id, transaction_id }
     * Returns base64-encoded PDF bytes.
     */
    public function receipt(Request $request, BotReceiptService $receipts)
    {
        $data = $request->validate([
            'guardian_id' => 'required|integer',
            'transaction_id' => 'required|integer',
        ]);

        $tx = PaymentTransaction::with('studentFee.student')->find($data['transaction_id']);
        if (! $tx || ! $tx->studentFee || ! $tx->studentFee->student) {
            return response()->json(['message' => 'Transaction not found.'], 404);
        }
        if ((int) $tx->studentFee->student->parent_guardian_id !== (int) $data['guardian_id']) {
            return response()->json(['message' => 'Not authorised for this transaction.'], 403);
        }

        try {
            $pdf = $receipts->generatePdf($tx->id);
        } catch (\Throwable $e) {
            Log::channel('bot_api')->error('receipt generate failed', [
                'transaction_id' => $tx->id,
                'error' => $e->getMessage(),
            ]);
            return response()->json(['message' => 'Could not generate receipt.'], 500);
        }

        return response()->json([
            'filename' => $pdf['filename'],
            'mime' => $pdf['mime'],
            'base64' => base64_encode($pdf['bytes']),
        ]);
    }

    /**
     * The additional per-term / per-year charges every family pays on top of
     * tuition. Amounts here should match the school's published prospectus —
     * edit the numbers in this array to change what the bot shows and what
     * the PDF renders.
     */
    private function feeExtras(): array
    {
        return [
            ['name' => 'PTA',             'amount' => 100.00, 'frequency' => 'per term'],
            ['name' => 'Computer Fee',    'amount' => 100.00, 'frequency' => 'per term'],
            ['name' => 'Maintenance Fee', 'amount' => 100.00, 'frequency' => 'per term'],
        ];
    }

    /**
     * Bus fare policy — same value for every route.
     */
    private function busFarePolicy(): array
    {
        return [
            'flat_rate' => 500.00,
            'frequency' => 'per month',
            'note'      => 'Applies to every route we serve within Chililabombwe.',
        ];
    }

    /**
     * POST /api/bot/log
     * Fire-and-forget log ingestion. The bot posts one call per inbound or
     * outbound message; we drop it into `bot_conversations` for the Filament
     * viewer. Deduplicated by (message_id, direction) when message_id present.
     */
    public function log(Request $request)
    {
        $data = $request->validate([
            'phone'       => 'required|string|min:6|max:20',
            'direction'   => 'required|in:in,out',
            'kind'        => 'required|string|max:32',
            'content'     => 'nullable|string|max:8000',
            'meta'        => 'nullable|array',
            'flow_state'  => 'nullable|string|max:32',
            'message_id'  => 'nullable|string|max:128',
        ]);

        $phone = BotConversation::normalisePhone($data['phone']);
        if (! $phone) {
            return response()->json(['ok' => false, 'reason' => 'invalid phone'], 422);
        }

        $plus = '+' . $phone;
        $guardianId = null;
        $g = ParentGuardian::query()
            ->where(function ($q) use ($plus, $phone) {
                $q->where('phone', $plus)
                    ->orWhere('phone', $phone)
                    ->orWhere('alternate_phone', $plus)
                    ->orWhere('alternate_phone', $phone);
            })
            ->orWhereHas('user', function ($q) use ($phone, $plus) {
                $q->where('phone', $plus)
                    ->orWhere('phone', $phone)
                    ->orWhere('username', $phone);
            })
            ->first(['id']);
        if ($g) {
            $guardianId = $g->id;
        }

        if (! empty($data['message_id'])) {
            $exists = BotConversation::where('message_id', $data['message_id'])
                ->where('direction', $data['direction'])
                ->exists();
            if ($exists) {
                return response()->json(['ok' => true, 'deduped' => true]);
            }
        }

        BotConversation::create([
            'phone'       => $phone,
            'direction'   => $data['direction'],
            'kind'        => $data['kind'],
            'content'     => $data['content'] ?? null,
            'meta'        => $data['meta'] ?? null,
            'flow_state'  => $data['flow_state'] ?? null,
            'guardian_id' => $guardianId,
            'message_id'  => $data['message_id'] ?? null,
        ]);

        return response()->json(['ok' => true]);
    }

    /**
     * GET /api/bot/fee-schedule
     * Public-ish "prices at a glance" for the current academic year + term.
     * No ownership check — this is marketing data (what the school charges).
     */
    public function feeSchedule()
    {
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        $activeTerm = \App\Models\Term::where('is_active', true)->first();

        if (! $activeYear || ! $activeTerm) {
            return response()->json([
                'academic_year' => null,
                'term' => null,
                'sections' => [],
                'grades' => [],
                'extras' => $this->feeExtras(),
                'bus' => $this->busFarePolicy(),
            ]);
        }

        $rows = \App\Models\FeeStructure::with(['grade', 'schoolSection'])
            ->where('academic_year_id', $activeYear->id)
            ->where('term_id', $activeTerm->id)
            ->where('is_active', true)
            ->orderBy('school_section_id')
            ->orderBy('grade_id')
            ->get();

        $sections = [];
        $grades = [];
        foreach ($rows as $r) {
            // Marketing displays the base tuition; the total_fee column includes
            // per-structure additional charges that vary and don't belong here.
            $amount = (float) $r->basic_fee;
            if (! $r->grade_id && $r->schoolSection) {
                $sections[] = [
                    'section' => $r->schoolSection->name,
                    'basic' => $amount,
                ];
            } elseif ($r->grade) {
                $grades[] = [
                    'grade' => $r->grade->name,
                    'section' => optional($r->schoolSection)->name,
                    'basic' => $amount,
                ];
            }
        }

        Log::channel('bot_api')->info('fee-schedule', [
            'sections' => count($sections),
            'grades' => count($grades),
        ]);

        return response()->json([
            'academic_year' => $activeYear->name ?? (string) $activeYear->id,
            'term' => $activeTerm->name ?? (string) $activeTerm->id,
            'currency' => 'K',
            'sections' => $sections,
            'grades' => $grades,
            'extras' => $this->feeExtras(),
            'bus' => $this->busFarePolicy(),
        ]);
    }

    /**
     * GET /api/bot/fee-schedule-pdf
     * Returns a letterheaded PDF of the same schedule, base64-encoded.
     */
    public function feeSchedulePdf()
    {
        $data = $this->feeSchedule()->getData(true);
        $settings = \App\Models\SchoolSettings::getInstance();

        $logoSrc = null;
        $rel = $settings->school_logo ?? null;
        if ($rel && file_exists(storage_path('app/public/' . ltrim($rel, '/')))) {
            $logoSrc = storage_path('app/public/' . ltrim($rel, '/'));
        } elseif (file_exists(public_path('images/logo.png'))) {
            $logoSrc = public_path('images/logo.png');
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('fee-schedule', [
            'data' => $data,
            'settings' => $settings,
            'logoSrc' => $logoSrc,
            'generatedAt' => now()->format('d M Y'),
        ]);
        $pdf->setPaper('a4', 'portrait');
        $pdf->setOption('margin-top', 15);
        $pdf->setOption('margin-right', 15);
        $pdf->setOption('margin-bottom', 15);
        $pdf->setOption('margin-left', 15);
        $pdf->setOption('dpi', 150);
        $pdf->setOption('isRemoteEnabled', true);

        $bytes = $pdf->output();

        Log::channel('bot_api')->info('fee-schedule-pdf generated', ['bytes' => strlen($bytes)]);

        return response()->json([
            'filename' => 'St-Francis-Fee-Schedule.pdf',
            'mime' => 'application/pdf',
            'base64' => base64_encode($bytes),
        ]);
    }

    /**
     * GET /api/bot/gallery
     * List of published photo albums with counts. Public info — no ownership check.
     */
    public function gallery()
    {
        $albums = \App\Models\Album::query()
            ->where('status', 'published')
            ->withCount('photos')
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        Log::channel('bot_api')->info('gallery listed', ['albums' => $albums->count()]);

        return response()->json([
            'albums' => $albums->map(fn ($a) => [
                'id'          => $a->id,
                'title'       => $a->title,
                'description' => $a->description ? mb_substr(preg_replace('/\s+/', ' ', $a->description), 0, 72) : null,
                'photo_count' => (int) $a->photos_count,
            ])->values(),
        ]);
    }

    /**
     * POST /api/bot/album-photos  { album_id, offset?, limit? }
     * Returns a batch of photos as base64 so the bot can upload them to
     * Meta media and send them individually as scroll-able image messages.
     * Skips any photo > 4.5 MB (Meta's cap is 5 MB).
     */
    public function albumPhotos(Request $request)
    {
        $data = $request->validate([
            'album_id' => 'required|integer',
            'offset'   => 'nullable|integer|min:0',
            'limit'    => 'nullable|integer|min:1|max:6',
        ]);
        $offset = (int) ($data['offset'] ?? 0);
        $limit  = (int) ($data['limit']  ?? 4);

        $album = \App\Models\Album::where('status', 'published')->find($data['album_id']);
        if (! $album) {
            return response()->json(['message' => 'Album not found.'], 404);
        }

        $total = $album->photos()->count();
        $rows = $album->photos()->orderBy('order')->orderBy('id')
            ->skip($offset)->take($limit)->get();

        $out = [];
        foreach ($rows as $p) {
            // Photos are stored either in public/ (legacy seeded images) or in
            // storage/app/public/ (Filament uploads). Check both.
            $rel = ltrim($p->image_path, '/');
            $candidates = [
                public_path($rel),
                storage_path('app/public/' . $rel),
            ];
            $path = null;
            foreach ($candidates as $c) {
                if (is_file($c)) { $path = $c; break; }
            }
            if (! $path) continue;

            $size = filesize($path);
            if ($size > (int) (4.5 * 1024 * 1024)) {
                continue;
            }
            $mime = mime_content_type($path) ?: 'image/jpeg';
            $out[] = [
                'id'      => $p->id,
                'caption' => $p->caption ? mb_substr($p->caption, 0, 1024) : null,
                'mime'    => $mime,
                'base64'  => base64_encode(file_get_contents($path)),
            ];
        }

        Log::channel('bot_api')->info('album photos sent', [
            'album_id' => $album->id, 'offset' => $offset, 'sent' => count($out), 'total' => $total,
        ]);

        return response()->json([
            'album_id'    => $album->id,
            'album_title' => $album->title,
            'offset'      => $offset,
            'limit'       => $limit,
            'total'       => $total,
            'photos'      => $out,
            'has_more'    => ($offset + $limit) < $total,
            'next_offset' => ($offset + $limit) < $total ? $offset + $limit : null,
        ]);
    }

    /**
     * POST /api/bot/attendance  { guardian_id, student_id }
     * Active-term attendance summary + recent absences.
     */
    public function attendance(Request $request)
    {
        $data = $request->validate([
            'guardian_id' => 'required|integer',
            'student_id'  => 'required|integer',
        ]);
        $student = $this->authorisedStudent((int) $data['guardian_id'], (int) $data['student_id']);
        if (! $student) {
            return response()->json(['message' => 'Not authorised for this student.'], 403);
        }

        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        $activeTerm = \App\Models\Term::where('is_active', true)->first();

        $q = \App\Models\Attendance::where('student_id', $student->id);
        if ($activeYear) $q->where('academic_year_id', $activeYear->id);
        if ($activeTerm) $q->where('term_id', $activeTerm->id);
        $records = $q->orderBy('attendance_date', 'desc')->get();

        $total   = $records->count();
        $present = $records->where('status', 'present')->count();
        $late    = $records->where('status', 'late')->count();
        $absent  = $records->where('status', 'absent')->count();
        $sick    = $records->where('status', 'sick')->count();
        $excused = $records->where('status', 'excused')->count();
        $rate    = $total > 0 ? round((($present + $late) / $total) * 100, 1) : 0;

        $recentAbsences = $records
            ->whereIn('status', ['absent', 'sick', 'excused'])
            ->take(5)
            ->map(fn ($r) => [
                'date'   => $r->attendance_date?->format('D, d M Y'),
                'status' => $r->status,
            ])
            ->values();

        return response()->json([
            'student_name'    => $student->name,
            'term'            => trim(($activeTerm?->name ?? '') . ' ' . ($activeYear?->name ?? '')),
            'rate_percent'    => $rate,
            'total_days'      => $total,
            'present'         => $present,
            'late'            => $late,
            'absent'          => $absent,
            'sick'            => $sick,
            'excused'         => $excused,
            'recent_absences' => $recentAbsences,
        ]);
    }

    /**
     * POST /api/bot/report-cards  { guardian_id, student_id }
     * Which terms have generated report cards + arrears lock state.
     */
    public function reportCards(Request $request)
    {
        $data = $request->validate([
            'guardian_id' => 'required|integer',
            'student_id'  => 'required|integer',
        ]);
        $student = $this->authorisedStudent((int) $data['guardian_id'], (int) $data['student_id']);
        if (! $student) {
            return response()->json(['message' => 'Not authorised for this student.'], 403);
        }

        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        if (! $activeYear) {
            return response()->json([
                'student_name' => $student->name,
                'cards' => [],
                'is_locked' => false,
                'arrears_amount' => 0,
            ]);
        }

        $isLocked = $student->hasArrears();
        $arrears  = (float) $student->arrearsAmount();

        $terms = \App\Models\Term::where('academic_year_id', $activeYear->id)->orderBy('id')->get();
        $cards = $terms->map(function ($term) use ($student, $activeYear, $isLocked) {
            $comment = \App\Models\ReportCardComment::where('student_id', $student->id)
                ->where('term_id', $term->id)
                ->where('academic_year_id', $activeYear->id)
                ->first();
            $isGenerated = $comment && $comment->generated_at;
            return [
                'term_id'      => $term->id,
                'term'         => $term->name,
                'year'         => $activeYear->name ?? (string) $activeYear->id,
                'generated'    => (bool) $isGenerated,
                'downloadable' => (bool) $isGenerated && ! $isLocked,
            ];
        })->values();

        return response()->json([
            'student_name'   => $student->name,
            'cards'          => $cards,
            'is_locked'      => $isLocked,
            'arrears_amount' => $arrears,
        ]);
    }

    /**
     * POST /api/bot/report-card-pdf  { guardian_id, student_id, term_id }
     * Delegates to the standard portal PDF flow; returns base64.
     */
    public function reportCardPdf(Request $request)
    {
        $data = $request->validate([
            'guardian_id' => 'required|integer',
            'student_id'  => 'required|integer',
            'term_id'     => 'required|integer',
        ]);
        $student = $this->authorisedStudent((int) $data['guardian_id'], (int) $data['student_id']);
        if (! $student) {
            return response()->json(['message' => 'Not authorised.'], 403);
        }
        if ($student->hasArrears()) {
            return response()->json([
                'message' => 'Report card is on hold pending outstanding fees.',
                'arrears_amount' => (float) $student->arrearsAmount(),
            ], 423); // locked
        }
        $term = \App\Models\Term::find($data['term_id']);
        if (! $term) {
            return response()->json(['message' => 'Term not found.'], 404);
        }
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        $year = $activeYear?->name ?? now()->year;

        // Instantiate the existing controller so we reuse the exact same
        // Blade view + DomPDF config parents get from the portal.
        $req = \Illuminate\Http\Request::create('/portal/report-cards/' . $student->id . '/' . $term->id, 'GET', ['year' => $year]);
        $controller = app(\App\Http\Controllers\ReportCardController::class);
        try {
            $reflection = new \ReflectionMethod($controller, 'prepareReportCardData');
            $reflection->setAccessible(true);
            $reportData = $reflection->invoke($controller, $student, $term, $year);

            $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('pdf.report-card', $reportData);
            $pdf->setPaper('A4', 'portrait');
            $bytes = $pdf->output();

            $sanitizedId = str_replace('/', '-', $student->student_id_number ?? (string) $student->id);
            $filename = "report-card-{$sanitizedId}-term{$term->id}-{$year}.pdf";

            return response()->json([
                'filename' => $filename,
                'mime'     => 'application/pdf',
                'base64'   => base64_encode($bytes),
            ]);
        } catch (\Throwable $e) {
            Log::channel('bot_api')->error('report-card-pdf failed', ['student_id' => $student->id, 'term_id' => $term->id, 'error' => $e->getMessage()]);
            return response()->json(['message' => 'Could not generate the report card.'], 500);
        }
    }

    /**
     * POST /api/bot/timetable  { guardian_id, student_id }
     */
    public function timetable(Request $request)
    {
        $data = $request->validate([
            'guardian_id' => 'required|integer',
            'student_id'  => 'required|integer',
        ]);
        $student = $this->authorisedStudent((int) $data['guardian_id'], (int) $data['student_id']);
        if (! $student) {
            return response()->json(['message' => 'Not authorised for this student.'], 403);
        }
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        if (! $activeYear || ! $student->class_section_id) {
            return response()->json([
                'student_name' => $student->name,
                'class' => null,
                'days' => new \stdClass(),
            ]);
        }

        $timetable = \App\Models\TimetableEntry::getClassTimetable($student->class_section_id, $activeYear->id);
        $days = ['Monday' => [], 'Tuesday' => [], 'Wednesday' => [], 'Thursday' => [], 'Friday' => []];
        foreach ($timetable as $row) {
            $period = $row['period'];
            $start = $period->start_time?->format('H:i');
            foreach (array_keys($days) as $day) {
                $e = $row['days'][$day] ?? null;
                if ($e && $e->subject) {
                    $days[$day][] = [
                        'time'    => $start,
                        'subject' => $e->subject->name,
                        'teacher' => $e->teacher?->name,
                    ];
                }
            }
        }

        $className = trim(optional($student->classSection)->name ?? '') ?: '';
        $gradeName = optional(optional($student->classSection)->grade)->name ?? optional($student->grade)->name ?? '';
        $classLabel = trim(($gradeName ? $gradeName . ' ' : '') . $className);

        return response()->json([
            'student_name' => $student->name,
            'class'        => $classLabel,
            'days'         => $days,
        ]);
    }

    /**
     * POST /api/bot/homework  { guardian_id, student_id }
     * Active-term homework for the student's grade, oldest-due-first.
     */
    public function homework(Request $request)
    {
        $data = $request->validate([
            'guardian_id' => 'required|integer',
            'student_id'  => 'required|integer',
        ]);
        $student = $this->authorisedStudent((int) $data['guardian_id'], (int) $data['student_id']);
        if (! $student) {
            return response()->json(['message' => 'Not authorised for this student.'], 403);
        }

        $activeTerm = \App\Models\Term::where('is_active', true)->first();
        $items = \App\Models\Homework::where('grade_id', $student->grade_id)
            ->where('status', 'active')
            ->when($activeTerm, fn ($q) => $q->where(function ($q2) use ($activeTerm) {
                $q2->where('term_id', $activeTerm->id)->orWhereNull('term_id');
            }))
            ->with(['subject', 'assignedBy'])
            ->orderBy('due_date', 'desc')
            ->limit(10)
            ->get()
            ->map(function ($hw) use ($student) {
                $submission = \App\Models\HomeworkSubmission::where('homework_id', $hw->id)
                    ->where('student_id', $student->id)
                    ->first();
                return [
                    'id'            => $hw->id,
                    'title'         => $hw->title,
                    'subject'       => optional($hw->subject)->name ?? '',
                    'teacher'       => optional($hw->assignedBy)->name ?? '',
                    'due_date'      => $hw->due_date?->format('D, d M Y'),
                    'is_overdue'    => $hw->due_date && $hw->due_date->isPast() && ! $submission,
                    'submitted'     => $submission !== null,
                    'has_attachment' => (bool) ($hw->file_attachment ?: $hw->homework_file),
                ];
            })
            ->values();

        return response()->json([
            'student_name' => $student->name,
            'homework'     => $items,
        ]);
    }

    /**
     * POST /api/bot/homework-detail  { guardian_id, homework_id }
     * Full description + attachment bytes (base64) if one is attached.
     */
    public function homeworkDetail(Request $request)
    {
        $data = $request->validate([
            'guardian_id'  => 'required|integer',
            'homework_id'  => 'required|integer',
        ]);

        $hw = \App\Models\Homework::with(['subject', 'assignedBy'])->find($data['homework_id']);
        if (! $hw) {
            return response()->json(['message' => 'Not found.'], 404);
        }

        // Ownership: check this guardian has an active student in the homework's grade.
        $ok = \App\Models\Student::where('parent_guardian_id', $data['guardian_id'])
            ->where('grade_id', $hw->grade_id)
            ->where('enrollment_status', 'active')
            ->exists();
        if (! $ok) {
            return response()->json(['message' => 'Not authorised.'], 403);
        }

        $attachment = null;
        $rel = $hw->file_attachment ?: $hw->homework_file;
        if ($rel) {
            $path = storage_path('app/public/' . ltrim($rel, '/'));
            if (is_file($path) && filesize($path) < 10 * 1024 * 1024) {
                $mime = mime_content_type($path) ?: 'application/octet-stream';
                $attachment = [
                    'filename' => basename($path),
                    'mime'     => $mime,
                    'base64'   => base64_encode(file_get_contents($path)),
                ];
            }
        }

        return response()->json([
            'id'           => $hw->id,
            'title'        => $hw->title,
            'subject'      => optional($hw->subject)->name ?? '',
            'teacher'      => optional($hw->assignedBy)->name ?? '',
            'due_date'     => $hw->due_date?->format('D, d M Y'),
            'description'  => $hw->description ?? '',
            'submission_instructions' => $hw->submission_instructions ?? '',
            'attachment'   => $attachment,
        ]);
    }

    /**
     * POST /api/bot/quizzes  { guardian_id, student_id }
     */
    public function quizzes(Request $request)
    {
        $data = $request->validate([
            'guardian_id' => 'required|integer',
            'student_id'  => 'required|integer',
        ]);
        $student = $this->authorisedStudent((int) $data['guardian_id'], (int) $data['student_id']);
        if (! $student) {
            return response()->json(['message' => 'Not authorised for this student.'], 403);
        }

        $quizzes = \App\Models\Quiz::where('class_section_id', $student->class_section_id)
            ->where('status', 'published')
            ->with('subject')
            ->withCount('questions')
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($q) use ($student) {
                $attempts = \App\Models\QuizAttempt::where('quiz_id', $q->id)
                    ->where('student_id', $student->id)
                    ->get();
                $best = $attempts->where('status', 'submitted')->sortByDesc('percentage')->first();
                return [
                    'id'             => $q->id,
                    'title'          => $q->title,
                    'subject'        => optional($q->subject)->name ?? '',
                    'question_count' => (int) $q->questions_count,
                    'time_limit'     => (int) ($q->time_limit_minutes ?? 0),
                    'total_points'   => (int) $q->total_points,
                    'due'            => $q->due_at ? $q->due_at->format('D, d M Y H:i') : null,
                    'best_percentage' => $best ? (float) $best->percentage : null,
                    'attempts'       => $attempts->count(),
                ];
            })
            ->values();

        return response()->json([
            'student_name' => $student->name,
            'quizzes'      => $quizzes,
        ]);
    }

    /**
     * GET /api/bot/transactions?guardian_id=&student_id=
     * Last 5 completed transactions for the student.
     */
    public function transactions(Request $request)
    {
        $data = $request->validate([
            'guardian_id' => 'required|integer',
            'student_id' => 'required|integer',
        ]);

        $student = $this->authorisedStudent((int) $data['guardian_id'], (int) $data['student_id']);
        if (! $student) {
            return response()->json(['message' => 'Not authorised for this student.'], 403);
        }

        $studentFeeIds = StudentFee::where('student_id', $student->id)->pluck('id');

        $rows = PaymentTransaction::whereIn('student_fee_id', $studentFeeIds)
            ->orderByDesc('transaction_date')
            ->orderByDesc('id')
            ->limit(5)
            ->get(['id', 'amount', 'external_reference', 'reference_number', 'status', 'transaction_date', 'payment_method']);

        return response()->json([
            'transactions' => $rows->map(fn ($t) => [
                'transaction_id' => $t->id,
                'date' => optional($t->transaction_date)->format('Y-m-d'),
                'amount' => (float) $t->amount,
                'reference' => $t->reference_number ?: $t->external_reference,
                'status' => $t->status,
                'method' => $t->payment_method,
            ])->values(),
        ]);
    }

    private function authorisedStudent(int $guardianId, int $studentId): ?Student
    {
        return Student::where('id', $studentId)
            ->where('parent_guardian_id', $guardianId)
            ->with('classSection.grade')
            ->first();
    }

    private function maskPhone(?string $p): string
    {
        if (! $p) {
            return '****';
        }
        $len = strlen($p);
        if ($len <= 8) {
            return substr($p, 0, 2) . '****';
        }
        return substr($p, 0, 5) . '****' . substr($p, -4);
    }
}
