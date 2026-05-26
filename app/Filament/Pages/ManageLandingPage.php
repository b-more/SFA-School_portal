<?php

namespace App\Filament\Pages;

use App\Constants\RoleConstants;
use App\Models\SchoolSettings;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ManageLandingPage extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-window';
    protected static ?string $navigationGroup = 'Website Management';
    protected static ?string $navigationLabel = 'Landing Page';
    protected static ?string $title           = 'Landing Page Content';
    protected static ?int    $navigationSort  = 10;

    protected static string $view = 'filament.pages.manage-landing-page';

    public ?array $data = [];

    public static function shouldRegisterNavigation(): bool
    {
        return auth()->user()?->role_id === RoleConstants::ADMIN;
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->role_id === RoleConstants::ADMIN;
    }

    public static function defaults(): array
    {
        return [
            'eyebrow'              => null,
            'hero_headline'        => null,
            'hero_headline_accent' => null,
            'hero_subheadline'     => null,
            'hero_image'           => null,
            'cta_primary_label'    => null,
            'cta_primary_url'      => null,
            'cta_secondary_label'  => null,
            'cta_secondary_url'    => null,
            'announcement_text'    => null,
            'announcement_url'     => null,

            'show_news'            => true,
            'show_events'          => true,
            'show_testimonials'    => true,
            'show_stats'           => true,
            'show_programs'        => true,
            'show_features'        => true,
            'show_portal'          => true,
            'show_gallery'         => true,
            'show_cta_banner'      => true,
            'show_trust_strip'     => true,

            'gallery_images'       => [],

            'hero_stats'           => [
                ['value' => 'K–12',  'label' => 'Early Years · Primary · Secondary'],
                ['value' => '1:18',  'label' => 'Teacher · Student Ratio'],
                ['value' => 'ECZ',   'label' => 'Accredited Curriculum'],
                ['value' => '24/7',  'label' => 'Parent & Teacher Portal'],
            ],

            'about_image'          => null,
            'about_vision'         => null,
            'about_mission'        => null,
            'about_values'         => 'Faith, integrity, excellence, service, and respect — the pillars on which our community stands.',
            'about_badge_value'    => '200+',
            'about_badge_label'    => 'Active Students',

            'stats_years'          => 25,
            'stats_label_students' => 'Active Learners',
            'stats_label_teachers' => 'Qualified Teachers',
            'stats_label_year'     => 'Current Academic Year',
            'stats_label_years'    => 'Years of Excellence',

            'programs' => [
                ['title' => 'Early Years & Reception', 'age_range' => 'Ages 3 – 6',  'description' => 'Play-based, language-rich learning that builds confidence, curiosity, and foundational literacy and numeracy.', 'image' => null, 'cta_label' => 'Discuss enrolment', 'cta_url' => '#contact'],
                ['title' => 'Primary School',          'age_range' => 'Grades 1 – 7','description' => 'A robust academic core in literacy, numeracy and the sciences, alongside arts, sport, ICT and Christian formation.', 'image' => null, 'cta_label' => 'Discuss enrolment', 'cta_url' => '#contact'],
                ['title' => 'Secondary School',        'age_range' => 'Grades 8 – 12','description' => 'ECZ-aligned curriculum with strong sciences, languages and humanities — preparing learners for university and life.', 'image' => null, 'cta_label' => 'Discuss enrolment', 'cta_url' => '#contact'],
            ],

            'features' => [
                [
                    'icon'        => 'users',
                    'title'       => 'Capped at 25 children per classroom.',
                    'description' => 'Personalised attention isn\'t a slogan here — it\'s structural. Every classroom is intentionally small so teachers know each child by name, by strength, and by struggle.',
                ],
                [
                    'icon'        => 'graduation-cap',
                    'title'       => '100% pass rate at ECZ — Class of 2023.',
                    'description' => 'Our most recent Grade 12 cohort placed every single learner through their national examinations.',
                ],
                [
                    'icon'        => 'star',
                    'title'       => 'First place at the National Science Fair.',
                    'description' => 'Our Grade 10 team won top honours nationally for an innovative renewable-energy project — selected from entries across the country.',
                ],
                [
                    'icon'        => 'monitor',
                    'title'       => 'A new, fully-equipped computer lab.',
                    'description' => 'Recently opened — modern workstations and software supporting digital literacy from upper primary through to senior secondary.',
                ],
            ],

            'accreditations' => [
                ['label' => 'Ministry of Education',     'logo' => null],
                ['label' => 'ECZ',                       'logo' => null],
                ['label' => 'Catholic Mission Council',  'logo' => null],
                ['label' => 'Apostolic recognition',     'logo' => null],
            ],
            'accreditation_heading' => 'Recognised & Accredited By',

            'portal_cards' => [
                ['icon' => 'parent',  'title' => 'Parent Portal',     'description' => 'Track attendance, results, homework and fees. Pay online and submit assignments from your phone.', 'cta_label' => 'Open Parent App',  'cta_url' => 'https://parent.stfrancisofassisizm.com',  'open_in_new_tab' => true],
                ['icon' => 'teacher', 'title' => 'Teacher Portal',    'description' => 'Mark attendance, post homework, enter results, manage CPD and message parents — all in one place.',    'cta_label' => 'Open Teacher App', 'cta_url' => 'https://teacher.stfrancisofassisizm.com', 'open_in_new_tab' => true],
                ['icon' => 'lock',    'title' => 'Staff & Admin',     'description' => 'Administrators, accountants, librarians and heads of school manage every aspect of operations here.', 'cta_label' => 'Sign in',          'cta_url' => '/admin/login',                            'open_in_new_tab' => false],
                ['icon' => 'card',    'title' => 'Pay School Fees',   'description' => 'Pay securely via mobile money, card or bank transfer. Receipts are emailed instantly — no login required.', 'cta_label' => 'Pay now',     'cta_url' => '/pay',                                    'open_in_new_tab' => false],
            ],

            'cta_banner_title'     => null,
            'cta_banner_body'      => null,
            'cta_banner_primary_label'   => 'Apply now',
            'cta_banner_primary_url'     => '#contact',
            'cta_banner_secondary_label' => 'Call us',
            'cta_banner_secondary_url'   => null,
        ];
    }

    public function mount(): void
    {
        $settings = SchoolSettings::firstOrCreate([], []);
        $custom   = (array) ($settings->custom_settings ?? []);
        $landing  = (array) ($custom['landing'] ?? []);

        $this->form->fill(array_replace_recursive(static::defaults(), $landing));
    }

    public function form(Form $form): Form
    {
        $iconOptions = [
            'graduation-cap' => 'Graduation Cap',
            'shield'         => 'Shield',
            'users'          => 'Users / People',
            'monitor'        => 'Monitor / Screen',
            'globe'          => 'Globe',
            'home'           => 'Home / Building',
            'book'           => 'Book',
            'star'           => 'Star',
            'heart'          => 'Heart',
            'parent'         => 'Parent (Users)',
            'teacher'        => 'Teacher',
            'lock'           => 'Lock',
            'card'           => 'Card / Payment',
        ];

        return $form->statePath('data')->schema([

            Forms\Components\Tabs::make('LandingTabs')
                ->columnSpanFull()
                ->persistTabInQueryString()
                ->tabs([

                    /* ============== HERO ============== */
                    Forms\Components\Tabs\Tab::make('Hero')
                        ->icon('heroicon-o-megaphone')
                        ->schema([
                            Forms\Components\Section::make('Top Announcement Bar')
                                ->description('Optional banner shown above the navigation. Leave empty to hide.')
                                ->columns(2)
                                ->collapsible()
                                ->schema([
                                    Forms\Components\TextInput::make('announcement_text')
                                        ->placeholder('e.g. Admissions for 2026 are now open — apply today.')
                                        ->maxLength(160),
                                    Forms\Components\TextInput::make('announcement_url')
                                        ->label('Link (optional)')
                                        ->url()
                                        ->placeholder('https://…'),
                                ]),

                            Forms\Components\Section::make('Hero Section')
                                ->columns(2)
                                ->schema([
                                    Forms\Components\TextInput::make('eyebrow')
                                        ->label('Eyebrow text')
                                        ->placeholder('e.g. 2026 · Admissions Open')
                                        ->maxLength(80),
                                    Forms\Components\FileUpload::make('hero_image')
                                        ->label('Hero background image')
                                        ->image()
                                        ->directory('landing')
                                        ->maxSize(4096)
                                        ->imageEditor()
                                        ->imagePreviewHeight('120')
                                        ->helperText('Recommended 1920×1080.'),
                                    Forms\Components\TextInput::make('hero_headline')
                                        ->label('Headline')
                                        ->placeholder('Educating minds.')
                                        ->columnSpanFull()
                                        ->maxLength(160),
                                    Forms\Components\TextInput::make('hero_headline_accent')
                                        ->label('Accent line (gold)')
                                        ->placeholder('Forming character. Inspiring purpose.')
                                        ->columnSpanFull()
                                        ->maxLength(160),
                                    Forms\Components\Textarea::make('hero_subheadline')
                                        ->label('Sub-headline')
                                        ->rows(3)
                                        ->columnSpanFull()
                                        ->maxLength(400),
                                ]),

                            Forms\Components\Section::make('Hero CTA Buttons')
                                ->columns(2)
                                ->schema([
                                    Forms\Components\TextInput::make('cta_primary_label')->label('Primary label'),
                                    Forms\Components\TextInput::make('cta_primary_url')  ->label('Primary URL'),
                                    Forms\Components\TextInput::make('cta_secondary_label')->label('Secondary label'),
                                    Forms\Components\TextInput::make('cta_secondary_url')  ->label('Secondary URL'),
                                ]),

                            Forms\Components\Section::make('Hero Quick Stats (4 cells)')
                                ->description('Small badges below the hero CTAs. Leave empty to hide.')
                                ->schema([
                                    Forms\Components\Repeater::make('hero_stats')
                                        ->label('Hero stats')
                                        ->columns(2)
                                        ->maxItems(4)
                                        ->reorderable()
                                        ->collapsible()
                                        ->schema([
                                            Forms\Components\TextInput::make('value')->required()->maxLength(40),
                                            Forms\Components\TextInput::make('label')->required()->maxLength(80),
                                        ])
                                        ->itemLabel(fn (array $state): ?string => ($state['value'] ?? '') . ' — ' . ($state['label'] ?? '')),
                                ]),
                        ]),

                    /* ============== ABOUT ============== */
                    Forms\Components\Tabs\Tab::make('About')
                        ->icon('heroicon-o-information-circle')
                        ->schema([
                            Forms\Components\Section::make('About Section')
                                ->columns(2)
                                ->schema([
                                    Forms\Components\FileUpload::make('about_image')
                                        ->label('About image')
                                        ->image()
                                        ->directory('landing')
                                        ->maxSize(4096)
                                        ->imageEditor()
                                        ->columnSpanFull(),
                                    Forms\Components\Textarea::make('about_vision')
                                        ->label('Vision')
                                        ->placeholder('Pulled from School Settings → vision if blank.')
                                        ->rows(3)->columnSpanFull(),
                                    Forms\Components\Textarea::make('about_mission')
                                        ->label('Mission')
                                        ->placeholder('Pulled from School Settings → mission if blank.')
                                        ->rows(3)->columnSpanFull(),
                                    Forms\Components\Textarea::make('about_values')
                                        ->label('Values')
                                        ->rows(3)->columnSpanFull(),
                                    Forms\Components\TextInput::make('about_badge_value')
                                        ->label('Floating badge value')
                                        ->placeholder('200+'),
                                    Forms\Components\TextInput::make('about_badge_label')
                                        ->label('Floating badge label')
                                        ->placeholder('Active Students'),
                                ]),
                        ]),

                    /* ============== PROGRAMS ============== */
                    Forms\Components\Tabs\Tab::make('Programs')
                        ->icon('heroicon-o-academic-cap')
                        ->schema([
                            Forms\Components\Repeater::make('programs')
                                ->label('Academic Programs')
                                ->columns(2)
                                ->reorderable()
                                ->collapsible()
                                ->cloneable()
                                ->schema([
                                    Forms\Components\TextInput::make('age_range')->label('Age / Grade range')->required()->maxLength(60),
                                    Forms\Components\TextInput::make('title')->required()->maxLength(120),
                                    Forms\Components\Textarea::make('description')->rows(3)->columnSpanFull()->maxLength(500),
                                    Forms\Components\FileUpload::make('image')
                                        ->image()
                                        ->directory('landing/programs')
                                        ->maxSize(4096)
                                        ->columnSpanFull(),
                                    Forms\Components\TextInput::make('cta_label')->label('CTA text')->placeholder('Discuss enrolment'),
                                    Forms\Components\TextInput::make('cta_url')->label('CTA link')->placeholder('#contact'),
                                ])
                                ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'New program'),
                        ]),

                    /* ============== WHY US ============== */
                    Forms\Components\Tabs\Tab::make('Why Us')
                        ->icon('heroicon-o-sparkles')
                        ->schema([
                            Forms\Components\Repeater::make('features')
                                ->label('Why Choose Us')
                                ->columns(2)
                                ->reorderable()
                                ->collapsible()
                                ->cloneable()
                                ->schema([
                                    Forms\Components\Select::make('icon')
                                        ->options($iconOptions)
                                        ->required()
                                        ->native(false),
                                    Forms\Components\TextInput::make('title')->required()->maxLength(80),
                                    Forms\Components\Textarea::make('description')->rows(2)->columnSpanFull()->maxLength(300),
                                ])
                                ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'New feature'),
                        ]),

                    /* ============== PORTAL ============== */
                    Forms\Components\Tabs\Tab::make('Portal Cards')
                        ->icon('heroicon-o-rectangle-group')
                        ->schema([
                            Forms\Components\Repeater::make('portal_cards')
                                ->label('Portal Access cards')
                                ->columns(2)
                                ->reorderable()
                                ->collapsible()
                                ->cloneable()
                                ->maxItems(8)
                                ->schema([
                                    Forms\Components\Select::make('icon')
                                        ->options($iconOptions)
                                        ->required()
                                        ->native(false),
                                    Forms\Components\TextInput::make('title')->required()->maxLength(60),
                                    Forms\Components\Textarea::make('description')->rows(2)->columnSpanFull()->maxLength(280),
                                    Forms\Components\TextInput::make('cta_label')->required()->maxLength(40),
                                    Forms\Components\TextInput::make('cta_url')->required()->placeholder('https://… or /pay'),
                                    Forms\Components\Toggle::make('open_in_new_tab')->default(false)->columnSpanFull(),
                                ])
                                ->itemLabel(fn (array $state): ?string => $state['title'] ?? 'New card'),
                        ]),

                    /* ============== GALLERY ============== */
                    Forms\Components\Tabs\Tab::make('Gallery')
                        ->icon('heroicon-o-photo')
                        ->schema([
                            Forms\Components\FileUpload::make('gallery_images')
                                ->label('Gallery images (first becomes the wide tile)')
                                ->multiple()
                                ->reorderable()
                                ->image()
                                ->directory('landing/gallery')
                                ->maxSize(4096)
                                ->panelLayout('grid')
                                ->maxFiles(7),
                        ]),

                    /* ============== TRUST / CTA ============== */
                    Forms\Components\Tabs\Tab::make('Trust & CTA')
                        ->icon('heroicon-o-shield-check')
                        ->schema([
                            Forms\Components\Section::make('Accreditation logos')
                                ->schema([
                                    Forms\Components\TextInput::make('accreditation_heading')
                                        ->label('Heading')->maxLength(120),
                                    Forms\Components\Repeater::make('accreditations')
                                        ->columns(2)
                                        ->reorderable()
                                        ->collapsible()
                                        ->schema([
                                            Forms\Components\TextInput::make('label')->label('Alt / Label')->required()->maxLength(80),
                                            Forms\Components\FileUpload::make('logo')
                                                ->label('Logo image')
                                                ->image()
                                                ->directory('landing/accreditations')
                                                ->maxSize(2048),
                                        ])
                                        ->itemLabel(fn (array $state): ?string => $state['label'] ?? 'Logo'),
                                ]),

                            Forms\Components\Section::make('CTA Banner')
                                ->columns(2)
                                ->schema([
                                    Forms\Components\TextInput::make('cta_banner_title')
                                        ->label('Title')
                                        ->placeholder('Ready to join the {school} family?')
                                        ->columnSpanFull(),
                                    Forms\Components\Textarea::make('cta_banner_body')
                                        ->rows(2)
                                        ->columnSpanFull()
                                        ->placeholder('Admissions are open for the {year} academic year. Schedule a campus tour or begin your application today.'),
                                    Forms\Components\TextInput::make('cta_banner_primary_label')->default('Apply now'),
                                    Forms\Components\TextInput::make('cta_banner_primary_url')->placeholder('#contact'),
                                    Forms\Components\TextInput::make('cta_banner_secondary_label')->default('Call us'),
                                    Forms\Components\TextInput::make('cta_banner_secondary_url')->placeholder('tel:+260…'),
                                ]),
                        ]),

                    /* ============== STATS ============== */
                    Forms\Components\Tabs\Tab::make('Stats')
                        ->icon('heroicon-o-chart-bar')
                        ->schema([
                            Forms\Components\Section::make('Live counters')
                                ->description('Student / teacher counts come from the database. You can override the labels and the years-of-excellence number here.')
                                ->columns(2)
                                ->schema([
                                    Forms\Components\TextInput::make('stats_label_students')->label('Label · students'),
                                    Forms\Components\TextInput::make('stats_label_teachers')->label('Label · teachers'),
                                    Forms\Components\TextInput::make('stats_label_year')->label('Label · academic year'),
                                    Forms\Components\TextInput::make('stats_label_years')->label('Label · years of excellence'),
                                    Forms\Components\TextInput::make('stats_years')
                                        ->label('Years of excellence (number)')
                                        ->numeric()
                                        ->default(25),
                                ]),
                        ]),

                    /* ============== TOGGLES ============== */
                    Forms\Components\Tabs\Tab::make('Sections')
                        ->icon('heroicon-o-adjustments-horizontal')
                        ->schema([
                            Forms\Components\Section::make('Toggle sections on/off')
                                ->columns(2)
                                ->schema([
                                    Forms\Components\Toggle::make('show_trust_strip')->label('Trust / accreditation strip'),
                                    Forms\Components\Toggle::make('show_stats')->label('Live stats counters'),
                                    Forms\Components\Toggle::make('show_programs')->label('Programs section'),
                                    Forms\Components\Toggle::make('show_features')->label('Why Choose Us'),
                                    Forms\Components\Toggle::make('show_portal')->label('Portal access cards'),
                                    Forms\Components\Toggle::make('show_gallery')->label('Campus gallery'),
                                    Forms\Components\Toggle::make('show_news')->label('Latest news'),
                                    Forms\Components\Toggle::make('show_events')->label('Upcoming events'),
                                    Forms\Components\Toggle::make('show_testimonials')->label('Testimonials'),
                                    Forms\Components\Toggle::make('show_cta_banner')->label('CTA banner'),
                                ]),
                        ]),
                ]),
        ]);
    }

    protected function getFormActions(): array
    {
        return [
            \Filament\Actions\Action::make('save')
                ->label('Save changes')
                ->icon('heroicon-o-check-circle')
                ->submit('save'),
            \Filament\Actions\Action::make('view')
                ->label('Preview landing')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('gray')
                ->url(url('/'), shouldOpenInNewTab: true),
            \Filament\Actions\Action::make('reset')
                ->label('Reset to defaults')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->requiresConfirmation()
                ->action('resetToDefaults'),
        ];
    }

    public function save(): void
    {
        $state = $this->form->getState();

        $settings = SchoolSettings::firstOrCreate([], []);
        $custom   = (array) ($settings->custom_settings ?? []);
        $custom['landing'] = $state;

        $settings->custom_settings = $custom;
        $settings->settings_last_updated_at = now();
        $settings->settings_updated_by = auth()->id();
        $settings->save();

        \Illuminate\Support\Facades\Cache::forget('school_settings');

        Notification::make()
            ->title('Landing page updated')
            ->success()
            ->body('Your changes are live on the public landing page.')
            ->send();
    }

    public function resetToDefaults(): void
    {
        $settings = SchoolSettings::firstOrCreate([], []);
        $custom   = (array) ($settings->custom_settings ?? []);
        unset($custom['landing']);
        $settings->custom_settings = $custom;
        $settings->settings_last_updated_at = now();
        $settings->settings_updated_by = auth()->id();
        $settings->save();

        \Illuminate\Support\Facades\Cache::forget('school_settings');

        $this->form->fill(static::defaults());

        Notification::make()
            ->title('Landing page reset')
            ->success()
            ->body('All landing page customisations have been removed. Defaults restored.')
            ->send();
    }
}
