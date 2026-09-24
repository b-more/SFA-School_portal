<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\FeeStructure;
use App\Models\Notice;
use App\Models\Result;
use App\Models\SchoolSettings;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\Term;
use App\Models\UssdSession;
use Illuminate\Support\Facades\Log;

/**
 * State-machine dispatcher for the school USSD service (*388*100#).
 *
 * Flow: the caller enters a STUDENT NUMBER (not their phone) — we look it
 * up in `students.student_id_number` and drive a per-student menu.
 *
 * Anyone knowing a valid student number can see that student's fees /
 * attendance / results / announcements and initiate a payment. That matches
 * standard Zambian school USSD conventions; the student number appears on
 * report cards, badges and school letters, so it isn't a secret.
 *
 * "0. School info" is a side-door for people without a student number
 * (prospective parents, community) — same public menu as the WhatsApp bot.
 */
class UssdFlowService
{
    // ---- STUDENT-KEYED (main) states ----
    private const STATE_ASK_STUDENT     = 'ASK_STUDENT';
    private const STATE_STUDENT_MENU    = 'STUDENT_MENU';
    private const STATE_FEES_SHOWN      = 'FEES_SHOWN';
    private const STATE_PAY_AMOUNT      = 'PAY_AMOUNT';
    private const STATE_PAY_CONFIRM     = 'PAY_CONFIRM';
    private const STATE_ANNOUNCEMENTS   = 'ANNOUNCEMENTS';
    private const STATE_RESULTS_SHOWN   = 'RESULTS_SHOWN';

    // ---- PUBLIC side-door states ----
    private const STATE_PUB_MAIN        = 'PUB_MAIN';
    private const STATE_PUB_INFO        = 'PUB_INFO';

    private const MAX_STUDENT_RETRIES = 2;

    public function handle(UssdSession $session, string $input, bool $isNew): array
    {
        if ($isNew) {
            return $this->showStudentPrompt($session);
        }

        $input = trim($input);

        if ($input === '00') {
            return $this->goMain($session);
        }

        return match ($session->state) {
            self::STATE_ASK_STUDENT     => $this->handleStudentPrompt($session, $input),
            self::STATE_STUDENT_MENU    => $this->handleStudentMenu($session, $input),
            self::STATE_FEES_SHOWN      => $this->handleFeesShown($session, $input),
            self::STATE_PAY_AMOUNT      => $this->handlePayAmount($session, $input),
            self::STATE_PAY_CONFIRM     => $this->handlePayConfirm($session, $input),
            self::STATE_ANNOUNCEMENTS   => $this->handleTailBack($session, $input),
            self::STATE_RESULTS_SHOWN   => $this->handleTailBack($session, $input),
            self::STATE_PUB_MAIN        => $this->handlePubMain($session, $input),
            self::STATE_PUB_INFO        => $this->handlePubTail($session, $input),
            default                     => $this->showStudentPrompt($session),
        };
    }

    // ============================================================
    // START — prompt for student number
    // ============================================================

    private function showStudentPrompt(UssdSession $session, ?string $errorLine = null): array
    {
        $session->state = self::STATE_ASK_STUDENT;
        $prompt = "St. Francis of Assisi\n";
        if ($errorLine) {
            $prompt .= $errorLine . "\n";
        }
        $prompt .= "Enter your child's student number:\n"
                . "9. School info\n"
                . "0. Exit";
        return $this->cont($session, $prompt);
    }

    private function handleStudentPrompt(UssdSession $session, string $input): array
    {
        if ($input === '0')  return $this->end('Goodbye. Thank you for calling.');
        if ($input === '9')  return $this->showPubMain($session);

        $sid = preg_replace('/\s+/', '', $input);
        if ($sid === '') {
            return $this->reprompt($session, "Please enter a student number.");
        }

        $student = Student::query()
            ->whereRaw('LOWER(student_id_number) = ?', [strtolower($sid)])
            ->where('enrollment_status', 'active')
            ->with(['classSection.grade', 'grade', 'parentGuardian'])
            ->first();

        if (! $student) {
            return $this->reprompt($session, "Student number not found.");
        }

        // Cache the resolved student on the session so subsequent screens
        // don't need another DB round-trip just to render the header.
        $data = [
            'selected' => [
                'id'       => $student->id,
                'name'     => strtoupper((string) $student->name),
                'header'   => $this->buildHeader($student),
                'grade_id' => $student->grade_id,
                'class_id' => $student->class_section_id,
            ],
        ];
        $session->setAttribute('data', $data);
        if ($student->parent_guardian_id) {
            $session->guardian_id = $student->parent_guardian_id;
        }
        return $this->showStudentMenu($session);
    }

    private function reprompt(UssdSession $session, string $errorLine): array
    {
        $data = $session->data ?? [];
        $tries = (int) ($data['tries'] ?? 0) + 1;
        if ($tries > self::MAX_STUDENT_RETRIES) {
            return $this->end("Too many attempts. Please check the student number on your child's report card and try again.");
        }
        $data['tries'] = $tries;
        $session->setAttribute('data', $data);
        return $this->showStudentPrompt($session, $errorLine);
    }

    private function buildHeader(Student $s): string
    {
        $name  = strtoupper((string) $s->name);
        // Prefer "Gr X{section}" — matches how staff talk about classes.
        $grade = optional($s->grade)->name ?? optional($s->classSection?->grade)->name ?? '';
        $sect  = optional($s->classSection)->name ?? '';
        $classLabel = trim(preg_replace('/^\s*grade\s*/i', 'Gr ', $grade) . $sect);
        return trim($name . ($classLabel ? ' - ' . $classLabel : ''));
    }

    // ============================================================
    // Student main menu
    // ============================================================

    private function showStudentMenu(UssdSession $session): array
    {
        $sel = $session->data['selected'] ?? null;
        if (! $sel) return $this->showStudentPrompt($session);
        $header = $this->trim($sel['header'] ?? $sel['name'] ?? 'Student', 40);
        $body = $header . "\n"
              . "1. Fee Balance\n"
              . "2. Results\n"
              . "3. Attendance\n"
              . "4. Announcements\n"
              . "5. Pay Fees\n"
              . "6. Contact School\n"
              . "0. Exit";
        $session->state = self::STATE_STUDENT_MENU;
        return $this->cont($session, $body);
    }

    private function handleStudentMenu(UssdSession $session, string $input): array
    {
        return match ($input) {
            '1' => $this->showBalance($session),
            '2' => $this->showResults($session),
            '3' => $this->showAttendance($session),
            '4' => $this->showAnnouncements($session),
            '5' => $this->askPayAmount($session),
            '6' => $this->end($this->contactBlock()),
            '0' => $this->end('Thank you. Goodbye.'),
            default => $this->cont($session, "Invalid choice. Try 1-6 or 0."),
        };
    }

    // ---- 1. Fee Balance ----

    private function showBalance(UssdSession $session): array
    {
        $studentId = $session->data['selected']['id'] ?? null;
        $rows = StudentFee::with('feeCategory')->where('student_id', $studentId)->get();
        $total = (float) $rows->sum('balance');
        $paid  = (float) $rows->sum('amount_paid');
        $name  = $this->trim($session->data['selected']['name'] ?? '', 18);

        if ($total <= 0) {
            return $this->end("{$name}\nNo outstanding fees.\nTotal paid: K" . number_format($paid, 2) . ".\nThank you.");
        }

        $byCat = $rows->groupBy('fee_category_id')
            ->map(fn ($g) => (float) $g->sum('balance'))
            ->filter(fn ($v) => $v > 0)
            ->sortDesc()
            ->take(3);

        $lines = [$name, "Owed: K" . number_format($total, 2)];
        foreach ($byCat as $catId => $bal) {
            $cat = $rows->firstWhere('fee_category_id', $catId)?->feeCategory;
            $lines[] = '  ' . $this->trim(optional($cat)->name ?? 'Fees', 18) . ': K' . number_format($bal, 2);
        }
        $lines[] = "\n1. Pay now\n0. Back";
        $session->state = self::STATE_FEES_SHOWN;
        return $this->cont($session, implode("\n", $lines));
    }

    private function handleFeesShown(UssdSession $session, string $input): array
    {
        if ($input === '1') return $this->askPayAmount($session);
        if ($input === '0') return $this->showStudentMenu($session);
        return $this->cont($session, "1. Pay now\n0. Back");
    }

    // ---- 2. Results ----

    private function showResults(UssdSession $session): array
    {
        $studentId = $session->data['selected']['id'] ?? null;
        // Latest term the student has results for (regardless of active_term
        // — parents want to see the most-recent completed exams).
        $latest = Result::where('student_id', $studentId)
            ->orderByDesc('academic_year_id')
            ->orderByDesc('term_id')
            ->first();
        if (! $latest) {
            $session->state = self::STATE_RESULTS_SHOWN;
            return $this->cont($session, "No results published yet for this student.\n\n0. Back");
        }
        $rows = Result::with('subject')
            ->where('student_id', $studentId)
            ->where('academic_year_id', $latest->academic_year_id)
            ->where('term_id', $latest->term_id)
            ->orderBy('id')
            ->get();

        $termName = optional(Term::find($latest->term_id))->name ?? ('Term ' . $latest->term_id);
        $name = $this->trim($session->data['selected']['name'] ?? '', 18);
        $lines = [$name . ' - ' . $termName];
        $shown = 0;
        foreach ($rows as $r) {
            if ($shown >= 6) break;
            $subj = $this->trim(optional($r->subject)->name ?? '?', 14);
            $mark = number_format((float) $r->marks, 0);
            $grade = trim((string) $r->grade);
            $lines[] = '  ' . $subj . ': ' . $mark . ($grade !== '' ? ' (' . $grade . ')' : '');
            $shown++;
        }
        if ($rows->count() > 6) {
            $lines[] = '  ...+' . ($rows->count() - 6) . ' more';
        }
        $lines[] = "\n0. Back";
        $session->state = self::STATE_RESULTS_SHOWN;
        return $this->cont($session, implode("\n", $lines));
    }

    // ---- 3. Attendance ----

    private function showAttendance(UssdSession $session): array
    {
        $studentId = $session->data['selected']['id'] ?? null;
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        $activeTerm = Term::where('is_active', true)->first();

        $q = Attendance::where('student_id', $studentId);
        if ($activeYear) $q->where('academic_year_id', $activeYear->id);
        if ($activeTerm) $q->where('term_id', $activeTerm->id);
        $rec = $q->get();

        if ($rec->isEmpty()) {
            return $this->end('No attendance marked yet this term.');
        }
        $total   = $rec->count();
        $present = $rec->where('status', 'present')->count();
        $late    = $rec->where('status', 'late')->count();
        $absent  = $rec->where('status', 'absent')->count();
        $rate    = round((($present + $late) / $total) * 100, 1);
        $name    = $this->trim($session->data['selected']['name'] ?? '', 18);

        return $this->end(
            "{$name}\nAttendance: {$rate}%\n"
            . "Days marked: {$total}\n"
            . "Present: {$present}, Late: {$late}, Absent: {$absent}"
        );
    }

    // ---- 4. Announcements ----

    private function showAnnouncements(UssdSession $session): array
    {
        $studentId = $session->data['selected']['id'] ?? null;
        $gradeId   = $session->data['selected']['grade_id'] ?? null;
        $classId   = $session->data['selected']['class_id'] ?? null;

        // Pull notices this student would care about: school-wide, or targeting
        // this student's grade/class/self. Most-recent first, max 5.
        $items = Notice::query()
            ->where(function ($q) use ($studentId, $gradeId, $classId) {
                $q->where('target_type', 'school')
                  ->orWhere(function ($q2) use ($gradeId) {
                      $q2->where('target_type', 'grade')->where('target_grade_id', $gradeId);
                  })
                  ->orWhere(function ($q2) use ($classId) {
                      $q2->where('target_type', 'class')->where('target_class_id', $classId);
                  })
                  ->orWhere(function ($q2) use ($studentId) {
                      $q2->where('target_type', 'student')->where('target_student_id', $studentId);
                  });
            })
            ->orderByDesc('id')
            ->limit(5)
            ->get(['id', 'title', 'body', 'priority', 'created_at']);

        if ($items->isEmpty()) {
            $session->state = self::STATE_ANNOUNCEMENTS;
            return $this->cont($session, "No announcements right now.\n\n0. Back");
        }
        $lines = ["Announcements:"];
        foreach ($items as $n) {
            $tag = $n->priority === 'urgent' ? '! ' : ($n->priority === 'important' ? '* ' : '');
            $when = optional($n->created_at)->format('d/m') ?? '';
            $lines[] = '  ' . $tag . $this->trim($n->title, 32) . ' (' . $when . ')';
        }
        // Also include the first line of the most-recent one's body — a taster
        $topBody = trim(preg_replace('/\s+/', ' ', strip_tags((string) $items->first()->body)));
        if ($topBody !== '') {
            $lines[] = "\n" . $this->trim($topBody, 60);
        }
        $lines[] = "\n0. Back";
        $session->state = self::STATE_ANNOUNCEMENTS;
        return $this->cont($session, implode("\n", $lines));
    }

    // ---- 5. Pay Fees ----

    private function askPayAmount(UssdSession $session): array
    {
        $studentId = $session->data['selected']['id'] ?? null;
        $balance = (float) StudentFee::where('student_id', $studentId)->sum('balance');
        if ($balance <= 0) {
            return $this->end('No outstanding fees to pay. Thank you.');
        }
        $data = $session->data;
        $data['balance'] = $balance;
        $session->setAttribute('data', $data);
        $session->state = self::STATE_PAY_AMOUNT;
        $name = $this->trim($session->data['selected']['name'] ?? '', 18);
        return $this->cont($session,
            "Pay for {$name}\nBalance: K" . number_format($balance, 2)
            . "\n\nEnter amount in Kwacha:\n0. Cancel"
        );
    }

    private function handlePayAmount(UssdSession $session, string $input): array
    {
        if ($input === '0') return $this->end('Payment cancelled.');
        $amount = (float) preg_replace('/[^0-9.]/', '', $input);
        $balance = (float) ($session->data['balance'] ?? 0);
        if ($amount < 1) {
            return $this->cont($session, "Minimum is K1.\nEnter amount in Kwacha:\n0. Cancel");
        }
        if ($amount > $balance) {
            return $this->cont($session, "Above balance of K" . number_format($balance, 2) . ".\nEnter a smaller amount:\n0. Cancel");
        }
        $data = $session->data;
        $data['pending_amount'] = round($amount, 2);
        $session->setAttribute('data', $data);
        $session->state = self::STATE_PAY_CONFIRM;
        $name = $this->trim($session->data['selected']['name'] ?? '', 16);
        return $this->cont($session,
            "Confirm payment\n{$name}\nAmount: K" . number_format($amount, 2)
            . "\nFrom: " . $session->msisdn
            . "\n\n1. Confirm\n0. Cancel"
        );
    }

    private function handlePayConfirm(UssdSession $session, string $input): array
    {
        if ($input === '0') return $this->end('Payment cancelled.');
        if ($input !== '1') return $this->cont($session, "Please reply:\n1. Confirm\n0. Cancel");

        $studentId = $session->data['selected']['id'] ?? null;
        $amount = (float) ($session->data['pending_amount'] ?? 0);
        $student = Student::find($studentId);
        if (! $student || $amount <= 0) {
            return $this->end('Payment could not be started. Please try again.');
        }

        // Dispatched in a shutdown function so the USSD reply is returned
        // within Ontech's 5s soft budget even when CGrate is slow.
        $msisdn = $session->msisdn;
        register_shutdown_function(function () use ($student, $amount, $msisdn) {
            try {
                app(PaymentInitiationService::class)->initiate(
                    student: $student, amount: $amount, payerMsisdn: $msisdn,
                    channel: 'ussd', referencePrefix: 'US'
                );
            } catch (\Throwable $e) {
                Log::channel('bot_api')->error('USSD payment initiate failed', [
                    'student_id' => $student->id, 'amount' => $amount, 'error' => $e->getMessage(),
                ]);
            }
        });

        return $this->end(
            'Payment request sent. Approve the prompt on your phone. '
            . 'You will receive an SMS confirmation.'
        );
    }

    // ============================================================
    // PUBLIC side-door (9 from prompt) — for callers without a student number
    // ============================================================

    private function showPubMain(UssdSession $session): array
    {
        $session->state = self::STATE_PUB_MAIN;
        return $this->cont($session,
            "St. Francis of Assisi\n"
            . "1. About the school\n"
            . "2. Admissions\n"
            . "3. Our fees\n"
            . "4. Contact us\n"
            . "0. Exit"
        );
    }

    private function handlePubMain(UssdSession $session, string $input): array
    {
        $session->state = self::STATE_PUB_INFO;
        return match ($input) {
            '1' => $this->cont($session,
                "St. Francis of Assisi Private School\n"
                . "Chililabombwe, Zambia. Catholic values-based, ECE to Grade 12. "
                . "Qualified teachers, ECZ curriculum.\n\n0. Back"
            ),
            '2' => $this->cont($session,
                "Admissions open — ECE to Grade 12.\n"
                . "To apply: call " . $this->officePhone()
                . " or email " . $this->officeEmail()
                . " for the application form.\n\n0. Back"
            ),
            '3' => $this->publicFeesScreen($session),
            '4' => $this->cont($session, $this->contactBlock() . "\n\n0. Back"),
            '0' => $this->end('Goodbye.'),
            default => $this->cont($session, 'Reply 1-4 or 0 to exit.'),
        };
    }

    private function publicFeesScreen(UssdSession $session): array
    {
        $activeYear = \App\Models\AcademicYear::where('is_active', true)->first();
        $activeTerm = Term::where('is_active', true)->first();
        if (! $activeYear || ! $activeTerm) {
            return $this->cont($session, "Fee schedule not published yet. Call " . $this->officePhone() . ".\n\n0. Back");
        }
        $rows = FeeStructure::with('schoolSection')
            ->where('academic_year_id', $activeYear->id)
            ->where('term_id', $activeTerm->id)
            ->where('is_active', true)
            ->orderBy('school_section_id')
            ->get();
        if ($rows->isEmpty()) {
            return $this->cont($session, "Fee schedule not published for this term. Call " . $this->officePhone() . ".\n\n0. Back");
        }
        $lines = ["Tuition per term ({$activeTerm->name} {$activeYear->name}):"];
        foreach ($rows as $r) {
            $sec = strtolower(optional($r->schoolSection)->name ?? '');
            $short = str_contains($sec, 'early')   ? 'ECE'
                 : (str_contains($sec, 'primary') ? 'Primary'
                 : (str_contains($sec, 'second')  ? 'Secondary' : optional($r->schoolSection)->name));
            $lines[] = "  {$short}: K" . number_format((float) $r->basic_fee, 0);
        }
        $lines[] = "Plus PTA/Computer/Maintenance K100 each per term.";
        $lines[] = "\n0. Back";
        return $this->cont($session, implode("\n", $lines));
    }

    private function handlePubTail(UssdSession $session, string $input): array
    {
        if ($input === '0') return $this->showPubMain($session);
        return $this->cont($session, 'Reply 0 to go back to the main menu.');
    }

    // ============================================================
    // helpers
    // ============================================================

    /** Return to the student menu (if a student is selected) or the initial prompt. */
    private function goMain(UssdSession $session): array
    {
        if (! empty($session->data['selected'])) return $this->showStudentMenu($session);
        if ($session->state && str_starts_with($session->state, 'PUB_')) return $this->showPubMain($session);
        return $this->showStudentPrompt($session);
    }

    /** Handler for "tail" info screens (Results, Announcements) — only 0 = back. */
    private function handleTailBack(UssdSession $session, string $input): array
    {
        if ($input === '0') return $this->showStudentMenu($session);
        return $this->cont($session, 'Reply 0 to go back to the menu.');
    }

    private function contactBlock(): string
    {
        return "St. Francis of Assisi\nChililabombwe, Zambia\n"
             . "Tel: " . $this->officePhone() . "\n"
             . "Email: " . $this->officeEmail() . "\n"
             . "Mon-Fri 07:30-16:30";
    }

    private function cont(UssdSession $session, string $text): array
    {
        $session->appendTranscript('out', $text);
        return ['text' => $this->clamp($text), 'continue' => true];
    }

    private function end(string $text): array
    {
        return ['text' => $this->clamp($text), 'continue' => false];
    }

    private function clamp(string $text): string
    {
        if (mb_strlen($text) <= 160) return $text;
        return mb_substr($text, 0, 157) . '...';
    }

    private function trim(?string $s, int $max): string
    {
        $s = trim((string) $s);
        return mb_strlen($s) <= $max ? $s : mb_substr($s, 0, $max - 1) . '.';
    }

    private function officePhone(): string
    {
        return SchoolSettings::getInstance()->phone ?? '+260 972 266 217';
    }

    private function officeEmail(): string
    {
        return SchoolSettings::getInstance()->email ?? 'info@stfrancisofassisizm.com';
    }
}
