<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpSpreadsheet\IOFactory;
use App\Models\ParentGuardian;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use App\Models\Grade;
use App\Models\ClassSection;
use App\Models\Role;
use App\Models\AcademicYear;
use App\Constants\RoleConstants;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class BulkImport extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-up-tray';
    protected static string $view = 'filament.pages.bulk-import';
    protected static ?string $navigationGroup = 'System Management';
    protected static ?string $navigationLabel = 'Bulk Import';
    protected static ?int $navigationSort = 99;

    public ?array $data = [];
    public array $previewData = [];
    public array $errors = [];
    public int $successCount = 0;
    public int $errorCount = 0;
    public bool $showResults = false;

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role_id === RoleConstants::ADMIN;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->role_id === RoleConstants::ADMIN;
    }

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Bulk Data Import')
                    ->description('Import multiple records from Excel templates. Download the appropriate template, fill it with data, and upload it here.')
                    ->schema([
                        Forms\Components\Select::make('importType')
                            ->label('Import Type')
                            ->options([
                                'parents' => 'Parent/Guardian Information',
                                'students' => 'Student Information',
                                'teachers' => 'Teacher Information',
                            ])
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(fn () => $this->resetImport()),

                        Forms\Components\FileUpload::make('file')
                            ->label('Excel File')
                            ->acceptedFileTypes([
                                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
                                'application/vnd.ms-excel'
                            ])
                            ->disk('local')
                            ->directory('temp-imports')
                            ->required()
                            ->reactive()
                            ->helperText(fn ($get) => $this->getHelperText($get('importType'))),

                        Forms\Components\Placeholder::make('template_download')
                            ->label('Download Template')
                            ->content(fn ($get) => $this->getDownloadLinks($get('importType'))),
                    ])
                    ->columns(1),
            ])
            ->statePath('data');
    }

    protected function getHelperText(?string $importType): string
    {
        if (!$importType) {
            return 'Select an import type first.';
        }

        $templates = [
            'parents' => 'Download and use the Parent Information Template.',
            'students' => 'Download and use the Student Information Template.',
            'teachers' => 'Download and use the Teacher Information Template.',
        ];

        return $templates[$importType] ?? '';
    }

    protected function getDownloadLinks(?string $importType): \Illuminate\Support\HtmlString
    {
        if (!$importType) {
            return new \Illuminate\Support\HtmlString('<p class="text-sm text-gray-500">Select an import type to see download link.</p>');
        }

        $links = [
            'parents' => [
                'url' => asset('templates/Parent_Information_Template.xlsx'),
                'label' => 'Download Parent Template'
            ],
            'students' => [
                'url' => asset('templates/Student_Information_Template.xlsx'),
                'label' => 'Download Student Template'
            ],
            'teachers' => [
                'url' => asset('templates/Teacher_Information_Template.xlsx'),
                'label' => 'Download Teacher Template'
            ],
        ];

        $link = $links[$importType];
        return new \Illuminate\Support\HtmlString(
            '<a href="' . $link['url'] . '" class="text-primary-600 hover:text-primary-500 font-medium" download>' .
            '<span class="flex items-center gap-2">' .
            '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>' .
            $link['label'] .
            '</span>' .
            '</a>'
        );
    }

    protected function resetImport(): void
    {
        $this->previewData = [];
        $this->errors = [];
        $this->showResults = false;
        $this->successCount = 0;
        $this->errorCount = 0;
    }

    public function preview(): void
    {
        $formData = $this->form->getState();

        if (empty($formData['importType']) || empty($formData['file'])) {
            Notification::make()
                ->title('Missing Information')
                ->danger()
                ->body('Please select an import type and upload a file.')
                ->send();
            return;
        }

        try {
            $filePath = Storage::disk('local')->path($formData['file']);

            if (!file_exists($filePath)) {
                throw new \Exception('File not found. Please upload the file again.');
            }

            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            // Remove header and instruction rows
            array_shift($data); // Remove header
            array_shift($data); // Remove instructions

            // Preview first 5 rows
            $this->previewData = array_slice(array_filter($data, function($row) {
                return !empty(array_filter($row));
            }), 0, 5);

            Notification::make()
                ->title('Preview Ready')
                ->success()
                ->body('Showing preview of first ' . count($this->previewData) . ' rows. Click "Import Data" to proceed.')
                ->send();

        } catch (\Exception $e) {
            Notification::make()
                ->title('Preview Failed')
                ->danger()
                ->body('Error reading file: ' . $e->getMessage())
                ->send();
        }
    }

    public function import(): void
    {
        $formData = $this->form->getState();

        if (empty($formData['importType']) || empty($formData['file'])) {
            Notification::make()
                ->title('Missing Information')
                ->danger()
                ->body('Please select an import type and upload a file.')
                ->send();
            return;
        }

        try {
            $filePath = Storage::disk('local')->path($formData['file']);

            if (!file_exists($filePath)) {
                throw new \Exception('File not found. Please upload the file again.');
            }

            $spreadsheet = IOFactory::load($filePath);
            $sheet = $spreadsheet->getActiveSheet();
            $data = $sheet->toArray();

            // Remove header and instruction rows
            array_shift($data);
            array_shift($data);

            // Filter out empty rows
            $data = array_filter($data, function($row) {
                return !empty(array_filter($row));
            });

            $this->errors = [];
            $this->successCount = 0;
            $this->errorCount = 0;

            DB::beginTransaction();

            try {
                switch ($formData['importType']) {
                    case 'parents':
                        $this->importParents($data);
                        break;
                    case 'students':
                        $this->importStudents($data);
                        break;
                    case 'teachers':
                        $this->importTeachers($data);
                        break;
                }

                DB::commit();

                $this->showResults = true;

                Notification::make()
                    ->title('Import Completed')
                    ->success()
                    ->body("Successfully imported {$this->successCount} records. {$this->errorCount} errors encountered.")
                    ->send();

            } catch (\Exception $e) {
                DB::rollBack();
                throw $e;
            }

        } catch (\Exception $e) {
            Notification::make()
                ->title('Import Failed')
                ->danger()
                ->body('Error: ' . $e->getMessage())
                ->send();
        }
    }

    protected function importParents(array $data): void
    {
        $parentRole = Role::where('id', RoleConstants::PARENT)->first();

        foreach ($data as $index => $row) {
            $rowNumber = $index + 3; // Account for header and instruction rows

            try {
                // Validate required fields
                if (empty($row[1]) || empty($row[3])) {
                    throw new \Exception("Missing required fields (Name or Phone)");
                }

                // Check if parent already exists by phone
                $existingParent = ParentGuardian::where('phone', $row[3])->first();
                if ($existingParent) {
                    throw new \Exception("Parent with phone {$row[3]} already exists");
                }

                // Create user account
                $user = User::create([
                    'name' => $row[1],
                    'email' => $row[2] ?: null,
                    'password' => Hash::make('password123'), // Default password
                    'role_id' => RoleConstants::PARENT,
                ]);

                // Create parent record
                ParentGuardian::create([
                    'user_id' => $user->id,
                    'name' => $row[1],
                    'email' => $row[2] ?: null,
                    'phone' => $row[3],
                    'alternate_phone' => $row[4] ?: null,
                    'nrc' => $row[5] ?: null,
                    'nationality' => $row[6] ?: 'Zambian',
                    'relationship' => $row[7] ?: 'guardian',
                    'occupation' => $row[8] ?: null,
                    'address' => $row[9] ?: null,
                    'role_id' => RoleConstants::PARENT,
                ]);

                $this->successCount++;

            } catch (\Exception $e) {
                $this->errors[] = "Row {$rowNumber}: " . $e->getMessage();
                $this->errorCount++;
            }
        }
    }

    protected function importStudents(array $data): void
    {
        $studentRole = Role::where('id', RoleConstants::STUDENT)->first();
        $currentAcademicYear = AcademicYear::where('is_active', true)->first();

        foreach ($data as $index => $row) {
            $rowNumber = $index + 3;

            try {
                // Validate required fields
                if (empty($row[1]) || empty($row[2]) || empty($row[3]) || empty($row[6]) || empty($row[7]) || empty($row[8]) || empty($row[9]) || empty($row[10])) {
                    throw new \Exception("Missing required fields");
                }

                // Find grade
                $grade = Grade::where('name', $row[6])->first();
                if (!$grade) {
                    throw new \Exception("Grade '{$row[6]}' not found");
                }

                // Find class section
                $classSection = ClassSection::where('name', $row[7])
                    ->where('grade_id', $grade->id)
                    ->first();
                if (!$classSection) {
                    throw new \Exception("Class section '{$row[7]}' not found for {$row[6]}");
                }

                // Find parent by phone
                $parent = ParentGuardian::where('phone', $row[10])->first();
                if (!$parent) {
                    // Create parent if not exists
                    $parentUser = User::create([
                        'name' => $row[9],
                        'email' => null,
                        'password' => Hash::make('password123'),
                        'role_id' => RoleConstants::PARENT,
                    ]);

                    $parent = ParentGuardian::create([
                        'user_id' => $parentUser->id,
                        'name' => $row[9],
                        'phone' => $row[10],
                        'relationship' => 'guardian',
                        'role_id' => RoleConstants::PARENT,
                    ]);
                }

                // Check if student already exists
                $existingStudent = Student::where('name', $row[1])
                    ->where('date_of_birth', $row[3])
                    ->first();
                if ($existingStudent) {
                    throw new \Exception("Student already exists");
                }

                // Create student user
                $email = !empty($row[5]) ? $row[5] : Str::slug($row[1]) . '@student.stfrancis.edu.zm';
                $user = User::create([
                    'name' => $row[1],
                    'email' => $email,
                    'password' => Hash::make('password123'),
                    'role_id' => RoleConstants::STUDENT,
                ]);

                // Create student record
                Student::create([
                    'user_id' => $user->id,
                    'name' => $row[1],
                    'gender' => $row[2],
                    'date_of_birth' => Carbon::parse($row[3]),
                    'place_of_birth' => $row[4] ?: null,
                    'student_id_number' => $row[5] ?: 'STU' . str_pad(Student::count() + 1, 5, '0', STR_PAD_LEFT),
                    'grade_id' => $grade->id,
                    'class_section_id' => $classSection->id,
                    'admission_date' => Carbon::parse($row[8]),
                    'parent_guardian_id' => $parent->id,
                    'religious_denomination' => $row[11] ?: null,
                    'previous_school' => $row[12] ?: null,
                    'smallpox_vaccination' => $row[13] ?: null,
                    'medical_information' => $row[14] ?: null,
                    'address' => $row[15] ?: null,
                    'enrollment_status' => 'active',
                ]);

                $this->successCount++;

            } catch (\Exception $e) {
                $this->errors[] = "Row {$rowNumber}: " . $e->getMessage();
                $this->errorCount++;
            }
        }
    }

    protected function importTeachers(array $data): void
    {
        $teacherRole = Role::where('id', RoleConstants::TEACHER)->first();

        foreach ($data as $index => $row) {
            $rowNumber = $index + 3;

            try {
                // Validate required fields
                if (empty($row[1]) || empty($row[2]) || empty($row[3]) || empty($row[6]) || empty($row[8])) {
                    throw new \Exception("Missing required fields (Name, Email, Phone, Qualification, or Join Date)");
                }

                // Check if teacher already exists by email
                $existingTeacher = Teacher::where('email', $row[2])->first();
                if ($existingTeacher) {
                    throw new \Exception("Teacher with email {$row[2]} already exists");
                }

                // Create user account
                $user = User::create([
                    'name' => $row[1],
                    'email' => $row[2],
                    'password' => Hash::make('password123'),
                    'role_id' => RoleConstants::TEACHER,
                ]);

                // Find grade and class section if specified
                $gradeId = null;
                $classSectionId = null;
                $isClassTeacher = false;

                if (!empty($row[12])) {
                    $grade = Grade::where('name', $row[12])->first();
                    if ($grade) {
                        $gradeId = $grade->id;

                        if (!empty($row[13])) {
                            $classSection = ClassSection::where('name', $row[13])
                                ->where('grade_id', $grade->id)
                                ->first();
                            if ($classSection) {
                                $classSectionId = $classSection->id;
                                $isClassTeacher = true;
                            }
                        }
                    }
                }

                // Create teacher record
                Teacher::create([
                    'user_id' => $user->id,
                    'name' => $row[1],
                    'email' => $row[2],
                    'phone' => $row[3],
                    'nrc' => $row[4] ?: null,
                    'tpin' => $row[5] ?: null,
                    'qualification' => $row[6],
                    'specialization' => $row[7] ?: null,
                    'join_date' => Carbon::parse($row[8]),
                    'bank_name' => $row[9] ?: null,
                    'account_number' => $row[10] ?: null,
                    'bank_branch' => $row[11] ?: null,
                    'grade_id' => $gradeId,
                    'class_section_id' => $classSectionId,
                    'is_class_teacher' => $isClassTeacher,
                    'address' => $row[14] ?: null,
                    'is_active' => true,
                    'role_id' => RoleConstants::TEACHER,
                ]);

                $this->successCount++;

            } catch (\Exception $e) {
                $this->errors[] = "Row {$rowNumber}: " . $e->getMessage();
                $this->errorCount++;
            }
        }
    }

    public function getTitle(): string
    {
        return 'Bulk Data Import';
    }
}
