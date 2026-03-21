<?php

namespace App\Filament\Pages;

use App\Constants\RoleConstants;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\SchoolSettings;
use App\Models\Teacher;
use App\Traits\HasPageGuide;
use Barryvdh\DomPDF\Facade\Pdf;
use Filament\Actions\Action;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class EditProfile extends Page implements HasForms
{
    use InteractsWithForms, HasPageGuide;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $title = 'My Profile';
    protected static ?string $slug = 'profile';
    protected static bool $shouldRegisterNavigation = false;

    protected static string $view = 'filament.pages.edit-profile';

    public ?array $editData = [];

    public function mount(): void
    {
        $user = Auth::user();

        // Parent profile
        if ($this->isParent()) {
            $parent = $this->getParentGuardian();
            if ($parent) {
                $this->editForm->fill([
                    'name' => $parent->name,
                    'email' => $parent->email ?? $user->email,
                    'phone' => $parent->phone,
                    'alternate_phone' => $parent->alternate_phone,
                    'address' => $parent->address,
                    'occupation' => $parent->occupation,
                    'nrc' => $parent->nrc,
                    'nationality' => $parent->nationality,
                    'relationship' => $parent->relationship,
                ]);
            }
            return;
        }

        $employee = $this->getEmployee();
        $teacher = $this->getTeacher();

        $fillData = [];

        if ($employee) {
            $fillData = [
                'email' => $employee->email ?? $user->email,
                'phone' => $employee->phone,
                'address' => $employee->address,
                'city' => $employee->city,
                'province' => $employee->province,
                'emergency_contact_name' => $employee->emergency_contact_name,
                'emergency_contact_phone' => $employee->emergency_contact_phone,
                'emergency_contact_relationship' => $employee->emergency_contact_relationship,
                'next_of_kin_name' => $employee->next_of_kin_name,
                'next_of_kin_phone' => $employee->next_of_kin_phone,
                'next_of_kin_relationship' => $employee->next_of_kin_relationship,
                'next_of_kin_address' => $employee->next_of_kin_address,
                'profile_photo' => $employee->profile_photo,
            ];
        } elseif ($teacher) {
            $fillData = [
                'email' => $teacher->email ?? $user->email,
                'phone' => $teacher->phone,
                'address' => $teacher->address,
                'city' => null,
                'province' => null,
                'emergency_contact_name' => null,
                'emergency_contact_phone' => null,
                'emergency_contact_relationship' => null,
                'next_of_kin_name' => null,
                'next_of_kin_phone' => null,
                'next_of_kin_relationship' => null,
                'next_of_kin_address' => null,
                'profile_photo' => $teacher->profile_photo,
            ];
        }

        if ($teacher && $this->isTeachingStaff()) {
            $fillData = array_merge($fillData, [
                'nrc' => $teacher->nrc,
                'tpin' => $teacher->tpin,
                'account_number' => $teacher->account_number,
                'bank_name' => $teacher->bank_name,
                'bank_branch' => $teacher->bank_branch,
                'biography' => $teacher->biography,
                'cv_document' => $teacher->cv_document,
                'police_clearance' => $teacher->police_clearance,
                'teaching_license' => $teacher->teaching_license,
                'nrc_copy' => $teacher->nrc_copy,
            ]);
        }

        if (!empty($fillData)) {
            $this->editForm->fill($fillData);
        }
    }

    protected function getForms(): array
    {
        return [
            'editForm',
        ];
    }

    public function isTeachingStaff(): bool
    {
        return in_array(Auth::user()?->role_id, RoleConstants::teaching());
    }

    public function isParent(): bool
    {
        return Auth::user()?->role_id === RoleConstants::PARENT;
    }

    public function getParentGuardian(): ?\App\Models\ParentGuardian
    {
        return \App\Models\ParentGuardian::where('user_id', Auth::id())->first();
    }

    public function getChildren()
    {
        $parent = $this->getParentGuardian();
        if (!$parent) return collect();
        return $parent->students()->with(['grade', 'classSection'])->where('enrollment_status', 'active')->get();
    }

    public function editForm(Form $form): Form
    {
        // Parent-specific form
        if ($this->isParent()) {
            return $form->schema([
                Section::make('Personal Information')
                    ->description('Update your contact details')
                    ->icon('heroicon-o-user')
                    ->schema([
                        TextInput::make('name')
                            ->label('Full Name')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('email')
                            ->label('Email Address')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('phone')
                            ->label('Phone Number')
                            ->tel()
                            ->required()
                            ->maxLength(20),

                        TextInput::make('alternate_phone')
                            ->label('Alternate Phone')
                            ->tel()
                            ->maxLength(20),
                    ])
                    ->columns(2),

                Section::make('Additional Details')
                    ->icon('heroicon-o-identification')
                    ->schema([
                        TextInput::make('occupation')
                            ->label('Occupation')
                            ->maxLength(255),

                        TextInput::make('nrc')
                            ->label('NRC Number')
                            ->maxLength(255),

                        TextInput::make('nationality')
                            ->label('Nationality')
                            ->maxLength(100),

                        Forms\Components\Select::make('relationship')
                            ->label('Relationship to Student')
                            ->options([
                                'father' => 'Father',
                                'mother' => 'Mother',
                                'guardian' => 'Guardian',
                                'uncle' => 'Uncle',
                                'aunt' => 'Aunt',
                                'grandparent' => 'Grandparent',
                                'sibling' => 'Sibling',
                                'other' => 'Other',
                            ])
                            ->native(false),

                        Textarea::make('address')
                            ->label('Residential Address')
                            ->rows(2)
                            ->maxLength(500)
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ])->statePath('editData');
        }

        $schema = [
            Section::make('Profile Photo')
                ->schema([
                    FileUpload::make('profile_photo')
                        ->label('Profile Photo')
                        ->image()
                        ->disk('public')
                        ->directory('employee-photos')
                        ->visibility('public')
                        ->imageResizeMode('cover')
                        ->imageCropAspectRatio('1:1')
                        ->imageResizeTargetWidth('400')
                        ->imageResizeTargetHeight('400')
                        ->openable()
                        ->downloadable()
                        ->columnSpanFull(),
                ]),

            Section::make('Contact Information')
                ->description('Update your contact details.')
                ->icon('heroicon-o-phone')
                ->schema([
                    TextInput::make('email')
                        ->label('Email Address')
                        ->email()
                        ->required()
                        ->maxLength(255),

                    TextInput::make('phone')
                        ->label('Phone Number')
                        ->tel()
                        ->maxLength(20),

                    Textarea::make('address')
                        ->label('Address')
                        ->rows(2)
                        ->maxLength(500),

                    TextInput::make('city')
                        ->maxLength(100),

                    TextInput::make('province')
                        ->maxLength(100),
                ])
                ->columns(2),

            Section::make('Emergency Contact')
                ->description('Keep your emergency contacts up to date.')
                ->icon('heroicon-o-exclamation-triangle')
                ->schema([
                    TextInput::make('emergency_contact_name')
                        ->label('Contact Name')
                        ->maxLength(255),

                    TextInput::make('emergency_contact_phone')
                        ->label('Contact Phone')
                        ->tel()
                        ->maxLength(20),

                    TextInput::make('emergency_contact_relationship')
                        ->label('Relationship')
                        ->maxLength(100),
                ])
                ->columns(3),

            Section::make('Next of Kin')
                ->icon('heroicon-o-users')
                ->schema([
                    TextInput::make('next_of_kin_name')
                        ->label('Name')
                        ->maxLength(255),

                    TextInput::make('next_of_kin_phone')
                        ->label('Phone')
                        ->tel()
                        ->maxLength(20),

                    TextInput::make('next_of_kin_relationship')
                        ->label('Relationship')
                        ->maxLength(100),

                    Textarea::make('next_of_kin_address')
                        ->label('Address')
                        ->rows(2)
                        ->maxLength(500)
                        ->columnSpanFull(),
                ])
                ->columns(3),
        ];

        // Add teacher-specific sections for teaching staff
        if ($this->isTeachingStaff()) {
            $schema[] = Section::make('Identification & Banking')
                ->description('Your identification and banking details')
                ->icon('heroicon-o-banknotes')
                ->schema([
                    TextInput::make('nrc')
                        ->label('NRC Number')
                        ->maxLength(255)
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('tpin')
                        ->label('TPIN')
                        ->maxLength(255)
                        ->disabled()
                        ->dehydrated(false),

                    TextInput::make('account_number')
                        ->label('Account Number')
                        ->maxLength(255),

                    TextInput::make('bank_name')
                        ->label('Bank Name')
                        ->maxLength(255),

                    TextInput::make('bank_branch')
                        ->label('Bank Branch')
                        ->maxLength(255),
                ])
                ->columns(2);

            $schema[] = Section::make('Biography')
                ->icon('heroicon-o-document-text')
                ->schema([
                    Textarea::make('biography')
                        ->label('Biography / Notes')
                        ->rows(4)
                        ->maxLength(1000)
                        ->columnSpanFull(),
                ]);

            $schema[] = Section::make('Required Documents')
                ->description('Upload your professional documents')
                ->icon('heroicon-o-folder-open')
                ->schema([
                    FileUpload::make('cv_document')
                        ->label('Curriculum Vitae (CV)')
                        ->disk('public')
                        ->directory('teacher-documents/cv')
                        ->visibility('public')
                        ->preserveFilenames()
                        ->openable()
                        ->downloadable()
                        ->previewable()
                        ->acceptedFileTypes(['application/pdf', 'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'])
                        ->maxSize(10240)
                        ->helperText('Upload your CV (PDF or Word, max 10MB)'),

                    FileUpload::make('police_clearance')
                        ->label('Police Clearance Certificate')
                        ->disk('public')
                        ->directory('teacher-documents/police-clearance')
                        ->visibility('public')
                        ->preserveFilenames()
                        ->openable()
                        ->downloadable()
                        ->previewable()
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                        ->maxSize(10240)
                        ->helperText('Upload your police clearance certificate (PDF or Image, max 10MB)'),

                    FileUpload::make('teaching_license')
                        ->label('Teaching License')
                        ->disk('public')
                        ->directory('teacher-documents/teaching-license')
                        ->visibility('public')
                        ->preserveFilenames()
                        ->openable()
                        ->downloadable()
                        ->previewable()
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                        ->maxSize(10240)
                        ->helperText('Upload your teaching license/certificate (PDF or Image, max 10MB)'),

                    FileUpload::make('nrc_copy')
                        ->label('NRC Scanned Copy')
                        ->disk('public')
                        ->directory('teacher-documents/nrc')
                        ->visibility('public')
                        ->preserveFilenames()
                        ->openable()
                        ->downloadable()
                        ->previewable()
                        ->acceptedFileTypes(['application/pdf', 'image/jpeg', 'image/png'])
                        ->maxSize(10240)
                        ->helperText('Upload scanned copy of your NRC (PDF or Image, max 10MB)'),
                ])
                ->columns(2);
        }

        return $form
            ->schema($schema)
            ->statePath('editData');
    }

    public function saveProfile(): void
    {
        $data = $this->editForm->getState();

        // Parent save
        if ($this->isParent()) {
            $parent = $this->getParentGuardian();
            if ($parent) {
                $parent->update([
                    'name' => $data['name'],
                    'email' => $data['email'],
                    'phone' => $data['phone'],
                    'alternate_phone' => $data['alternate_phone'],
                    'address' => $data['address'],
                    'occupation' => $data['occupation'],
                    'nrc' => $data['nrc'],
                    'nationality' => $data['nationality'],
                    'relationship' => $data['relationship'],
                ]);

                // Sync email and name to user account
                $user = Auth::user();
                $updates = [];
                if ($data['email'] && $user->email !== $data['email']) $updates['email'] = $data['email'];
                if ($data['name'] && $user->name !== $data['name']) $updates['name'] = $data['name'];
                if (!empty($updates)) $user->update($updates);

                Notification::make()
                    ->title('Profile Updated')
                    ->body('Your details have been saved successfully.')
                    ->success()
                    ->send();
            }
            return;
        }

        $employee = $this->getEmployee();
        $teacher = $this->getTeacher();

        if ($employee) {
            $employee->update([
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'city' => $data['city'],
                'province' => $data['province'],
                'emergency_contact_name' => $data['emergency_contact_name'],
                'emergency_contact_phone' => $data['emergency_contact_phone'],
                'emergency_contact_relationship' => $data['emergency_contact_relationship'],
                'next_of_kin_name' => $data['next_of_kin_name'],
                'next_of_kin_phone' => $data['next_of_kin_phone'],
                'next_of_kin_relationship' => $data['next_of_kin_relationship'],
                'next_of_kin_address' => $data['next_of_kin_address'],
                'profile_photo' => $data['profile_photo'],
            ]);

            // Sync email to user account
            if ($employee->user && $data['email'] && $employee->user->email !== $data['email']) {
                $employee->user->update(['email' => $data['email']]);
            }
        } elseif ($teacher) {
            $teacher->update([
                'email' => $data['email'],
                'phone' => $data['phone'],
                'address' => $data['address'],
                'profile_photo' => $data['profile_photo'],
            ]);

            // Sync email to user account
            $user = Auth::user();
            if ($data['email'] && $user->email !== $data['email']) {
                $user->update(['email' => $data['email']]);
            }
        } else {
            Notification::make()
                ->title('Error')
                ->body('Staff profile not found.')
                ->danger()
                ->send();
            return;
        }

        // Save teacher-specific fields if applicable
        if ($teacher && $this->isTeachingStaff()) {
            $teacherData = [];
            if (isset($data['account_number'])) $teacherData['account_number'] = $data['account_number'];
            if (isset($data['bank_name'])) $teacherData['bank_name'] = $data['bank_name'];
            if (isset($data['bank_branch'])) $teacherData['bank_branch'] = $data['bank_branch'];
            if (isset($data['biography'])) $teacherData['biography'] = $data['biography'];
            if (array_key_exists('cv_document', $data)) $teacherData['cv_document'] = $data['cv_document'];
            if (array_key_exists('police_clearance', $data)) $teacherData['police_clearance'] = $data['police_clearance'];
            if (array_key_exists('teaching_license', $data)) $teacherData['teaching_license'] = $data['teaching_license'];
            if (array_key_exists('nrc_copy', $data)) $teacherData['nrc_copy'] = $data['nrc_copy'];

            if (!empty($teacherData)) {
                $teacher->update($teacherData);
            }
        }

        Notification::make()
            ->title('Profile Updated')
            ->body('Your details have been saved successfully.')
            ->success()
            ->send();
    }

    public function getEmployee(): ?Employee
    {
        $user = Auth::user();

        $employee = Employee::where('user_id', $user->id)->first();

        if (!$employee) {
            $teacher = Teacher::where('user_id', $user->id)->first();
            if ($teacher && $teacher->employee_id) {
                $employee = Employee::find($teacher->employee_id);
            }
        }

        return $employee;
    }

    public function getTeacher(): ?Teacher
    {
        return Teacher::where('user_id', Auth::id())->first();
    }

    /**
     * Build a staff data object from Teacher for use in Employee PDF templates.
     */
    protected function buildStaffFromTeacher(Teacher $teacher): object
    {
        return (object) [
            'id' => $teacher->id,
            'name' => $teacher->name,
            'email' => $teacher->email,
            'phone' => $teacher->phone,
            'address' => $teacher->address,
            'city' => null,
            'province' => null,
            'profile_photo' => $teacher->profile_photo,
            'employee_id' => $teacher->employee_id,
            'employee_number' => $teacher->employee_id,
            'position' => 'Teacher',
            'department' => $teacher->department,
            'status' => $teacher->is_active ? 'active' : 'inactive',
            'joining_date' => $teacher->join_date,
            'gender' => null,
            'date_of_birth' => null,
            'marital_status' => null,
            'nationality' => 'Zambian',
            'full_address' => $teacher->address,
            'nrc_number' => $teacher->nrc,
            'napsa_number' => null,
            'tpin_number' => $teacher->tpin,
            'nhima_number' => null,
            'employment_type' => 'full_time',
            'contract_start_date' => null,
            'contract_end_date' => null,
            'years_of_service' => $teacher->join_date ? (int) $teacher->join_date->diffInYears(now()) : 0,
            'bank_name' => $teacher->bank_name,
            'bank_branch' => $teacher->bank_branch,
            'bank_account_name' => $teacher->name,
            'bank_account_number' => $teacher->account_number,
            'emergency_contact_name' => null,
            'emergency_contact_phone' => null,
            'emergency_contact_relationship' => null,
            'next_of_kin_name' => null,
            'next_of_kin_phone' => null,
            'next_of_kin_address' => null,
            'highest_qualification' => $teacher->qualification,
            'qualification_institution' => null,
            'qualification_year' => null,
            'professional_certifications' => $teacher->specialization,
        ];
    }

    public function getDocuments()
    {
        $employee = $this->getEmployee();
        if (!$employee) {
            return collect();
        }

        return $employee->documents()->orderByDesc('created_at')->get();
    }

    public function getPayslips()
    {
        $employee = $this->getEmployee();
        if (!$employee) {
            return collect();
        }

        return Payroll::where('employee_id', $employee->id)
            ->with('academicYear')
            ->orderByDesc('year')
            ->orderByRaw("FIELD(month, 'December','November','October','September','August','July','June','May','April','March','February','January')")
            ->get();
    }

    public function downloadProfilePdf()
    {
        $employee = $this->getEmployee();

        if (!$employee) {
            $teacher = $this->getTeacher();
            if (!$teacher) {
                Notification::make()
                    ->title('Error')
                    ->body('Staff profile not found.')
                    ->danger()
                    ->send();
                return;
            }
            $employee = $this->buildStaffFromTeacher($teacher);
        }

        $settings = SchoolSettings::first();

        $pdf = Pdf::loadView('pdf.employee-profile', [
            'employee' => $employee,
            'user' => Auth::user(),
            'settings' => $settings,
        ]);

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'profile-' . ($employee->employee_number ?? $employee->id) . '.pdf'
        );
    }

    public function downloadBusinessCard()
    {
        $employee = $this->getEmployee();
        $isTeacherFallback = false;

        if (!$employee) {
            $teacher = $this->getTeacher();
            if (!$teacher) {
                Notification::make()
                    ->title('Error')
                    ->body('Staff profile not found.')
                    ->danger()
                    ->send();
                return;
            }
            $employee = $this->buildStaffFromTeacher($teacher);
            $isTeacherFallback = true;
        }

        $settings = SchoolSettings::first();

        // Use school website URL for teachers without Employee records
        if ($isTeacherFallback) {
            $profileUrl = $settings->website ?? url('/');
        } else {
            $profileUrl = route('staff.profile', $employee);
        }

        $qrCode = base64_encode(QrCode::format('svg')->size(80)->errorCorrection('M')->generate($profileUrl));

        $pdf = Pdf::loadView('pdf.employee-business-card', [
            'employee' => $employee,
            'settings' => $settings,
            'qrCode' => $qrCode,
            'profileUrl' => $profileUrl,
        ])->setPaper('A4', 'portrait');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'business-card-' . ($employee->employee_number ?? $employee->id) . '.pdf'
        );
    }

    public function downloadEmployeeId()
    {
        $employee = $this->getEmployee();
        $isTeacherFallback = false;

        if (!$employee) {
            $teacher = $this->getTeacher();
            if (!$teacher) {
                Notification::make()
                    ->title('Error')
                    ->body('Staff profile not found.')
                    ->danger()
                    ->send();
                return;
            }
            $employee = $this->buildStaffFromTeacher($teacher);
            $isTeacherFallback = true;
        }

        $settings = SchoolSettings::first();

        if ($isTeacherFallback) {
            $profileUrl = $settings->website ?? url('/');
        } else {
            $profileUrl = route('staff.profile', $employee);
        }

        $qrCode = base64_encode(QrCode::format('svg')->size(80)->errorCorrection('M')->generate($profileUrl));

        $pdf = Pdf::loadView('pdf.employee-id-card', [
            'employee' => $employee,
            'settings' => $settings,
            'qrCode' => $qrCode,
            'profileUrl' => $profileUrl,
        ])->setPaper('A4', 'portrait');

        return response()->streamDownload(
            fn () => print($pdf->output()),
            'id-card-' . ($employee->employee_number ?? $employee->id) . '.pdf'
        );
    }

    protected function getGuideSlug(): string
    {
        return 'profile';
    }

    protected function getHeaderActions(): array
    {
        $isParent = $this->isParent();

        $actions = [];

        if (!$isParent) {
            $actions[] = $this->getPageGuideAction();

            $actions[] = Action::make('downloadProfile')
                ->label('Profile PDF')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('success')
                ->action('downloadProfilePdf');

            $actions[] = Action::make('downloadBusinessCard')
                ->label('Business Card')
                ->icon('heroicon-o-identification')
                ->color('primary')
                ->action('downloadBusinessCard');

            $actions[] = Action::make('downloadEmployeeId')
                ->label('Employee ID')
                ->icon('heroicon-o-credit-card')
                ->color('info')
                ->action('downloadEmployeeId');
        }

        $actions[] = Action::make('changePassword')
                ->label('Change Password')
                ->icon('heroicon-o-lock-closed')
                ->color('warning')
                ->form([
                    TextInput::make('current_password')
                        ->label('Current Password')
                        ->password()
                        ->required()
                        ->currentPassword()
                        ->revealable(),

                    TextInput::make('password')
                        ->label('New Password')
                        ->password()
                        ->required()
                        ->rule(Password::min(8))
                        ->different('current_password')
                        ->revealable(),

                    TextInput::make('password_confirmation')
                        ->label('Confirm New Password')
                        ->password()
                        ->required()
                        ->same('password')
                        ->revealable(),
                ])
                ->action(function (array $data): void {
                    Auth::user()->update([
                        'password' => Hash::make($data['password']),
                    ]);

                    Notification::make()
                        ->title('Password Changed')
                        ->body('Your password has been updated successfully.')
                        ->success()
                        ->send();
                });

        return $actions;
    }

    public static function canAccess(): bool
    {
        return (bool) auth()->user();
    }
}
