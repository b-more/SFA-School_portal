<?php

namespace App\Services;

use App\Models\Student;
use App\Models\StudentFee;
use App\Models\FeeStructure;
use App\Models\AcademicYear;
use App\Models\Term;
use App\Models\Grade;
use App\Models\ParentGuardian;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;

class StudentFeeService
{
    /**
     * Create fee for a newly registered student
     */
    public static function createFeeForNewStudent(Student $student, ?int $enrollmentTermId = null): ?StudentFee
    {
        try {
            // Use the student's enrollment term if provided, otherwise fall back to active term
            if ($enrollmentTermId) {
                $term = Term::find($enrollmentTermId);
            } else {
                $term = $student->enrollmentTerm ?? Term::where('is_active', true)->first();
            }

            if (!$term) {
                Log::warning('No enrollment term found for new student fee creation', [
                    'student_id' => $student->id,
                    'enrollment_term_id' => $enrollmentTermId,
                    'student_enrollment_term_id' => $student->enrollment_term_id
                ]);
                return null;
            }

            // Get the academic year from the term
            $academicYear = $term->academicYear;

            if (!$academicYear) {
                Log::warning('No academic year found for term', [
                    'student_id' => $student->id,
                    'term_id' => $term->id
                ]);
                return null;
            }

            // Find fee structure: try section-based first, then fall back to grade-based
            $studentGrade = $student->grade;
            $feeStructure = null;

            if ($studentGrade && $studentGrade->school_section_id) {
                $feeStructure = FeeStructure::where('academic_year_id', $academicYear->id)
                    ->where('term_id', $term->id)
                    ->where('school_section_id', $studentGrade->school_section_id)
                    ->where('is_active', true)
                    ->first();
            }

            // Fall back to grade-based lookup for old records
            if (!$feeStructure) {
                $feeStructure = FeeStructure::where('academic_year_id', $academicYear->id)
                    ->where('term_id', $term->id)
                    ->where('grade_id', $student->grade_id)
                    ->where('is_active', true)
                    ->first();
            }

            if (!$feeStructure) {
                Log::warning('No fee structure found for new student', [
                    'student_id' => $student->id,
                    'grade_id' => $student->grade_id,
                    'school_section_id' => $studentGrade?->school_section_id,
                    'academic_year_id' => $academicYear->id,
                    'term_id' => $term->id,
                    'term_name' => $term->name,
                    'academic_year_name' => $academicYear->name
                ]);
                return null;
            }

            // Check if fee already exists (prevent duplicates)
            $existingFee = StudentFee::where('student_id', $student->id)
                ->where('fee_structure_id', $feeStructure->id)
                ->first();

            if ($existingFee) {
                Log::info('Fee already exists for student', [
                    'student_id' => $student->id,
                    'fee_structure_id' => $feeStructure->id
                ]);
                return $existingFee;
            }

            // Create the fee record (balance based on tuition/basic fee only)
            $studentFee = StudentFee::create([
                'student_id' => $student->id,
                'fee_structure_id' => $feeStructure->id,
                'academic_year_id' => $academicYear->id,
                'term_id' => $term->id,
                'grade_id' => $student->grade_id,
                'amount_paid' => 0.00,
                'balance' => $feeStructure->basic_fee,
                'payment_status' => 'unpaid',
                'notes' => 'Automatically created for new student registration'
            ]);

            Log::info('Fee created for new student', [
                'student_id' => $student->id,
                'fee_id' => $studentFee->id,
                'tuition_fee' => $feeStructure->basic_fee,
                'term' => $term->name,
                'academic_year' => $academicYear->name
            ]);

            // Send SMS notification to parent about fee
            self::sendFeeNotificationSMS($student, $studentFee);

            return $studentFee;

        } catch (\Exception $e) {
            Log::error('Failed to create fee for new student', [
                'student_id' => $student->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return null;
        }
    }

    /**
     * Bulk create fees for current term
     */
    public static function bulkCreateFeesForCurrentTerm(?Grade $specificGrade = null): array
    {
        $created = 0;
        $skipped = 0;
        $errors = 0;

        try {
            // Get current active academic year and term
            $currentAcademicYear = AcademicYear::where('is_active', true)->first();
            $currentTerm = Term::where('is_active', true)->first();

            if (!$currentAcademicYear || !$currentTerm) {
                return [
                    'success' => false,
                    'message' => 'No active academic year or term found',
                    'created' => 0,
                    'skipped' => 0,
                    'errors' => 1
                ];
            }

            // Get students query
            $studentsQuery = Student::where('enrollment_status', 'active');

            if ($specificGrade) {
                $studentsQuery->where('grade_id', $specificGrade->id);
            }

            // Process students in chunks to avoid memory issues
            $studentsQuery->chunk(50, function ($students) use ($currentAcademicYear, $currentTerm, &$created, &$skipped, &$errors) {
                foreach ($students as $student) {
                    try {
                        $result = self::createFeeForStudent($student, $currentAcademicYear, $currentTerm);

                        if ($result === 'created') {
                            $created++;
                        } elseif ($result === 'exists') {
                            $skipped++;
                        }
                    } catch (\Exception $e) {
                        $errors++;
                        Log::error('Error creating fee for student in bulk operation', [
                            'student_id' => $student->id,
                            'error' => $e->getMessage()
                        ]);
                    }
                }
            });

            return [
                'success' => true,
                'message' => "Fees processed: {$created} created, {$skipped} already existed, {$errors} errors",
                'created' => $created,
                'skipped' => $skipped,
                'errors' => $errors
            ];

        } catch (\Exception $e) {
            Log::error('Bulk fee creation failed', [
                'error' => $e->getMessage(),
                'grade_id' => $specificGrade?->id
            ]);

            return [
                'success' => false,
                'message' => 'Bulk fee creation failed: ' . $e->getMessage(),
                'created' => $created,
                'skipped' => $skipped,
                'errors' => $errors + 1
            ];
        }
    }

    /**
     * Preview how many fees would be created
     */
    public static function previewFeeCreation(?Grade $specificGrade = null): array
    {
        try {
            $currentAcademicYear = AcademicYear::where('is_active', true)->first();
            $currentTerm = Term::where('is_active', true)->first();

            if (!$currentAcademicYear || !$currentTerm) {
                return [
                    'success' => false,
                    'message' => 'No active academic year or term found',
                    'preview' => []
                ];
            }

            $studentsQuery = Student::where('enrollment_status', 'active');

            if ($specificGrade) {
                $studentsQuery->where('grade_id', $specificGrade->id);
            }

            $students = $studentsQuery->with('grade')->get();

            $preview = [];
            $totalStudents = 0;
            $studentsNeedingFees = 0;
            $studentsWithFees = 0;

            foreach ($students as $student) {
                $totalStudents++;

                // Check if fee already exists
                $existingFee = StudentFee::where('student_id', $student->id)
                    ->where('academic_year_id', $currentAcademicYear->id)
                    ->where('term_id', $currentTerm->id)
                    ->exists();

                if ($existingFee) {
                    $studentsWithFees++;
                } else {
                    $studentsNeedingFees++;
                }

                $gradeName = $student->grade?->name ?? 'Unknown Grade';

                if (!isset($preview[$gradeName])) {
                    $preview[$gradeName] = [
                        'total' => 0,
                        'need_fees' => 0,
                        'have_fees' => 0
                    ];
                }

                $preview[$gradeName]['total']++;
                if ($existingFee) {
                    $preview[$gradeName]['have_fees']++;
                } else {
                    $preview[$gradeName]['need_fees']++;
                }
            }

            return [
                'success' => true,
                'total_students' => $totalStudents,
                'students_needing_fees' => $studentsNeedingFees,
                'students_with_fees' => $studentsWithFees,
                'current_term' => $currentTerm->name,
                'current_academic_year' => $currentAcademicYear->name,
                'breakdown_by_grade' => $preview
            ];

        } catch (\Exception $e) {
            Log::error('Fee creation preview failed', [
                'error' => $e->getMessage(),
                'grade_id' => $specificGrade?->id
            ]);

            return [
                'success' => false,
                'message' => 'Preview failed: ' . $e->getMessage(),
                'preview' => []
            ];
        }
    }

    /**
     * Bulk create fees for a specific term with optional previous-term balance enforcement.
     * Supports dry-run mode for previewing results before committing.
     */
    public static function bulkCreateFeesForTerm(
        Term $targetTerm,
        AcademicYear $academicYear,
        bool $enforceBalanceCheck = true,
        bool $dryRun = false
    ): array {
        $created = 0;
        $willCreate = 0;
        $alreadyExists = 0;
        $blockedByBalance = 0;
        $noFeeStructure = 0;
        $errors = 0;
        $blockedStudents = [];

        try {
            // Resolve previous term once, outside the loop
            $previousTerm = null;
            if ($enforceBalanceCheck) {
                $termService = new TermService();
                $previousTerm = $termService->getPreviousTerm($targetTerm);
            }

            $studentsQuery = Student::where('enrollment_status', 'active')->with('grade');

            $studentsQuery->chunk(50, function ($students) use (
                $targetTerm, $academicYear, $previousTerm, $enforceBalanceCheck, $dryRun,
                &$created, &$willCreate, &$alreadyExists, &$blockedByBalance,
                &$noFeeStructure, &$errors, &$blockedStudents
            ) {
                foreach ($students as $student) {
                    try {
                        // Check if fee already exists for target term
                        $existingFee = StudentFee::where('student_id', $student->id)
                            ->where('academic_year_id', $academicYear->id)
                            ->where('term_id', $targetTerm->id)
                            ->exists();

                        if ($existingFee) {
                            $alreadyExists++;
                            continue;
                        }

                        // Check for fee structure (section-based, then grade fallback)
                        $studentGrade = $student->grade;
                        $feeStructure = null;

                        if ($studentGrade && $studentGrade->school_section_id) {
                            $feeStructure = FeeStructure::where('academic_year_id', $academicYear->id)
                                ->where('term_id', $targetTerm->id)
                                ->where('school_section_id', $studentGrade->school_section_id)
                                ->where('is_active', true)
                                ->first();
                        }

                        if (!$feeStructure) {
                            $feeStructure = FeeStructure::where('academic_year_id', $academicYear->id)
                                ->where('term_id', $targetTerm->id)
                                ->where('grade_id', $student->grade_id)
                                ->where('is_active', true)
                                ->first();
                        }

                        if (!$feeStructure) {
                            $noFeeStructure++;
                            continue;
                        }

                        // Balance check against previous term
                        if ($enforceBalanceCheck && $previousTerm) {
                            $prevFee = StudentFee::where('student_id', $student->id)
                                ->where('term_id', $previousTerm->id)
                                ->first();

                            if ($prevFee && $prevFee->balance > 0) {
                                $blockedByBalance++;
                                if (count($blockedStudents) < 50) {
                                    $blockedStudents[] = [
                                        'name' => $student->name,
                                        'grade' => $studentGrade?->name ?? 'Unknown',
                                        'outstanding' => (float) $prevFee->balance,
                                    ];
                                }
                                continue;
                            }
                        }

                        // Create or count
                        if ($dryRun) {
                            $willCreate++;
                        } else {
                            $result = self::createFeeForStudent($student, $academicYear, $targetTerm);
                            if ($result === 'created') {
                                $created++;
                            } elseif ($result === 'exists') {
                                $alreadyExists++;
                            } elseif ($result === 'no_fee_structure') {
                                $noFeeStructure++;
                            }
                        }
                    } catch (\Exception $e) {
                        $errors++;
                        Log::error('Error in bulkCreateFeesForTerm for student', [
                            'student_id' => $student->id,
                            'error' => $e->getMessage(),
                        ]);
                    }
                }
            });

            return [
                'success' => true,
                'target_term' => $targetTerm->name,
                'academic_year' => $academicYear->name,
                'previous_term' => $previousTerm?->name,
                'created' => $created,
                'will_create' => $willCreate,
                'already_exists' => $alreadyExists,
                'blocked_by_balance' => $blockedByBalance,
                'no_fee_structure' => $noFeeStructure,
                'errors' => $errors,
                'blocked_students' => $blockedStudents,
            ];

        } catch (\Exception $e) {
            Log::error('bulkCreateFeesForTerm failed', [
                'term_id' => $targetTerm->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'target_term' => $targetTerm->name,
                'academic_year' => $academicYear->name,
                'previous_term' => null,
                'created' => $created,
                'will_create' => $willCreate,
                'already_exists' => $alreadyExists,
                'blocked_by_balance' => $blockedByBalance,
                'no_fee_structure' => $noFeeStructure,
                'errors' => $errors + 1,
                'blocked_students' => $blockedStudents,
            ];
        }
    }

    /**
     * Create fee for a specific student
     */
    private static function createFeeForStudent(Student $student, AcademicYear $academicYear, Term $term): string
    {
        // Find fee structure: try section-based first, then fall back to grade-based
        $studentGrade = $student->grade;
        $feeStructure = null;

        if ($studentGrade && $studentGrade->school_section_id) {
            $feeStructure = FeeStructure::where('academic_year_id', $academicYear->id)
                ->where('term_id', $term->id)
                ->where('school_section_id', $studentGrade->school_section_id)
                ->where('is_active', true)
                ->first();
        }

        // Fall back to grade-based lookup for old records
        if (!$feeStructure) {
            $feeStructure = FeeStructure::where('academic_year_id', $academicYear->id)
                ->where('term_id', $term->id)
                ->where('grade_id', $student->grade_id)
                ->where('is_active', true)
                ->first();
        }

        if (!$feeStructure) {
            Log::warning('No fee structure found for student', [
                'student_id' => $student->id,
                'grade_id' => $student->grade_id,
                'school_section_id' => $studentGrade?->school_section_id
            ]);
            return 'no_fee_structure';
        }

        // Check if fee already exists
        $existingFee = StudentFee::where('student_id', $student->id)
            ->where('fee_structure_id', $feeStructure->id)
            ->first();

        if ($existingFee) {
            return 'exists';
        }

        // Create the fee record (balance based on tuition/basic fee only)
        StudentFee::create([
            'student_id' => $student->id,
            'fee_structure_id' => $feeStructure->id,
            'academic_year_id' => $academicYear->id,
            'term_id' => $term->id,
            'grade_id' => $student->grade_id,
            'amount_paid' => 0.00,
            'balance' => $feeStructure->basic_fee,
            'payment_status' => 'unpaid',
            'notes' => 'Created via bulk fee generation'
        ]);

        return 'created';
    }

    /**
     * Send SMS notification to parent about new fee
     */
    private static function sendFeeNotificationSMS(Student $student, StudentFee $studentFee): void
    {
        try {
            if (!$student->parent_guardian_id) {
                return;
            }

            $parentGuardian = ParentGuardian::find($student->parent_guardian_id);

            if (!$parentGuardian || !$parentGuardian->phone) {
                return;
            }

            $sectionName = $studentFee->feeStructure->section_name ?? 'Unknown';
            $term = $studentFee->feeStructure->term->name ?? 'Unknown';
            $academicYear = $studentFee->feeStructure->academicYear->name ?? 'Unknown';
            $amount = number_format($studentFee->feeStructure->basic_fee, 2);

            // Short SMS message under 160 characters
            $message = "Fees set for {$student->name}, {$sectionName}: K{$amount}. ";
            $message .= "{$term} {$academicYear}. Visit school to pay. St Francis";

            $formattedPhone = self::formatPhoneNumber($parentGuardian->phone);

            // Log SMS in database
            $smsLogId = DB::table('sms_logs')->insertGetId([
                'recipient' => $formattedPhone,
                'message' => str_replace('@', '(at)', $message),
                'status' => 'pending',
                'message_type' => 'general',
                'reference_id' => $studentFee->id,
                'cost' => ceil(strlen($message) / 160) * 0.50,
                'sent_by' => auth()->id() ?? 1,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            // Send SMS
            $success = self::sendMessage($message, $formattedPhone);

            // Update SMS log status
            DB::table('sms_logs')
                ->where('id', $smsLogId)
                ->update([
                    'status' => $success ? 'sent' : 'failed',
                    'updated_at' => now()
                ]);

            Log::info('Fee notification SMS sent', [
                'student_id' => $student->id,
                'parent_id' => $parentGuardian->id,
                'success' => $success
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send fee notification SMS', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Format phone number for Zambia
     */
    private static function formatPhoneNumber(string $phoneNumber): string
    {
        $phoneNumber = preg_replace('/[^0-9]/', '', $phoneNumber);

        if (substr($phoneNumber, 0, 3) === '260') {
            return $phoneNumber;
        }

        if (substr($phoneNumber, 0, 1) === '0') {
            return '260' . substr($phoneNumber, 1);
        }

        if (strlen($phoneNumber) === 9) {
            return '260' . $phoneNumber;
        }

        return $phoneNumber;
    }

    /**
     * Send SMS message
     */
    private static function sendMessage(string $message, string $phoneNumber): bool
    {
        try {
            $urlEncodedMessage = urlencode(str_replace('@', '(at)', $message));

            $response = Http::withoutVerifying()
                ->timeout(20)
                ->post(env('SMS_API_URL'), [
                    'username' => env('SMS_USERNAME'),
                    'password' => env('SMS_PASSWORD'),
                    'msg' => $urlEncodedMessage,
                    'shortcode' => env('SMS_SHORTCODE'),
                    'sender_id' => env('SMS_SENDER_ID'),
                    'phone' => $phoneNumber,
                    'api_key' => env('SMS_API_KEY')
                ]);

            $isSuccessful = $response->successful() &&
                           (strtolower($response->body()) === 'success' ||
                            strpos(strtolower($response->body()), 'success') !== false);

            return $isSuccessful;

        } catch (\Exception $e) {
            Log::error('SMS sending failed', [
                'error' => $e->getMessage(),
                'phone' => $phoneNumber
            ]);
            return false;
        }
    }
}
