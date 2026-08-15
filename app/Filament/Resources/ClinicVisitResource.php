<?php

namespace App\Filament\Resources;

use App\Constants\RoleConstants;
use App\Filament\Resources\ClinicVisitResource\Pages;
use App\Models\ClinicComplaint;
use App\Models\ClinicVisit;
use App\Models\MedicalStockItem;
use App\Models\Student;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClinicVisitResource extends Resource
{
    protected static ?string $model = ClinicVisit::class;

    protected static ?string $navigationIcon  = 'heroicon-o-heart';
    protected static ?string $navigationGroup = 'Clinic';
    protected static ?string $navigationLabel = 'Clinic visits';
    protected static ?string $modelLabel      = 'Clinic visit';
    protected static ?int    $navigationSort  = 1;

    public static function canAccess(): bool
    {
        // Clinician: full CRUD. Admin: read + edit (they may need to override).
        // Medical records — no other role can see this.
        return in_array(auth()->user()?->role_id, [
            RoleConstants::ADMIN, RoleConstants::CLINICIAN,
        ], true);
    }

    public static function shouldRegisterNavigation(): bool { return self::canAccess(); }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            // === Row 1: Date + Student picker + name + grade ===
            Forms\Components\Grid::make(6)->schema([
                Forms\Components\DatePicker::make('visit_date')
                    ->label('Date')
                    ->default(now())->maxDate(now())->required()
                    ->native(false)
                    ->columnSpan(1),

                Forms\Components\Select::make('student_id')
                    ->label('Student (search)')
                    ->searchable()
                    ->extraInputAttributes(['autofocus' => true])
                    ->getSearchResultsUsing(fn (string $search) => Student::query()
                        ->where('enrollment_status', 'active')
                        ->where('name', 'like', "%{$search}%")
                        ->limit(20)
                        ->with('classSection.grade')
                        ->get()
                        ->mapWithKeys(fn ($s) => [$s->id => $s->name . ($s->classSection?->grade?->name ? ' — ' . $s->classSection->grade->name : '')])
                    )
                    ->getOptionLabelUsing(fn ($value) => Student::with('classSection.grade')->find($value)?->name)
                    ->helperText('Empty → free-text below')
                    ->live()
                    ->afterStateUpdated(function ($state, Forms\Set $set) {
                        if (! $state) return;
                        $s = Student::with('classSection.grade')->find($state);
                        if (! $s) return;
                        $set('student_name', $s->name);
                        if ($s->classSection?->grade) $set('grade', $s->classSection->grade->name);
                    })
                    ->columnSpan(3),

                Forms\Components\TextInput::make('student_name')
                    ->label('Name')->required()->maxLength(120)
                    ->columnSpan(1),

                Forms\Components\TextInput::make('grade')
                    ->label('Grade / Form')->required()->maxLength(20)
                    ->placeholder('10 or Form 1')
                    ->columnSpan(1),
            ]),

            // === Row 2: Complaints + short notes ===
            Forms\Components\Grid::make(3)->schema([
                Forms\Components\Select::make('clinic_complaints')
                    ->label('Complaint(s)')
                    ->multiple()
                    ->relationship('complaints', 'name')
                    ->options(ClinicComplaint::where('is_active', true)->orderBy('name')->pluck('name', 'id'))
                    ->searchable()->preload()
                    ->createOptionForm([Forms\Components\TextInput::make('name')->required()->maxLength(120)])
                    ->createOptionUsing(fn (array $data) => ClinicComplaint::create($data + ['is_active' => true])->id)
                    ->required()
                    ->columnSpan(2),

                Forms\Components\TextInput::make('complaint_notes')
                    ->label('Notes (optional)')
                    ->placeholder('e.g. post-malaria, bruise on lip')
                    ->maxLength(255)
                    ->columnSpan(1),
            ]),

            // === Row 3: Treatments repeater ===
            Forms\Components\Repeater::make('treatments')
                ->label('Treatments dispensed')
                ->helperText('Deducts from stock on save · balance shown live')
                ->schema([
                    Forms\Components\Select::make('item_id')
                        ->label('Stock item')
                        ->options(MedicalStockItem::where('is_active', true)->orderBy('name')
                            ->get()->mapWithKeys(fn ($i) => [$i->id => "{$i->name} · {$i->current_balance} {$i->unit}"]))
                        ->searchable()->required()->live()
                        ->columnSpan(3),
                    Forms\Components\TextInput::make('quantity')
                        ->numeric()->minValue(1)->default(1)->required()
                        ->extraInputAttributes(['inputmode' => 'numeric'])
                        ->columnSpan(1),
                ])
                ->columns(4)
                ->defaultItems(0)
                ->addActionLabel('+ Add treatment')
                ->itemLabel(fn (array $state) => (($state['item_id'] ?? null) ? optional(MedicalStockItem::find($state['item_id']))->name : '') ?: 'New treatment')
                ->dehydrated(true)
                ->statePath('treatments'),

            // === Row 4: Outcome flags (all optional) ===
            Forms\Components\Grid::make(4)->schema([
                Forms\Components\Toggle::make('sick_note_issued')
                    ->label('Sick note issued')
                    ->inline(false)
                    ->default(false),
                Forms\Components\Select::make('outcome')
                    ->label('Outcome')
                    ->options([
                        'returned_to_class' => 'Returned to class',
                        'sent_home'         => 'Sent home',
                        'referred'          => 'Referred out',
                    ])
                    ->default('returned_to_class')
                    ->helperText('Defaults to returned. SMSes parent on sent-home / referred.')
                    ->native(false)
                    ->columnSpan(2),
                Forms\Components\Toggle::make('needs_review')
                    ->label('Flag for review')
                    ->inline(false),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('visit_date')->date()->sortable(),
                Tables\Columns\TextColumn::make('student_name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('grade'),
                Tables\Columns\TextColumn::make('complaints.name')
                    ->badge()
                    ->separator(',')
                    ->limit(60),
                Tables\Columns\IconColumn::make('sick_note_issued')->label('Sick note')->boolean(),
                Tables\Columns\TextColumn::make('outcome')->badge()
                    ->color(fn ($state) => $state === 'referred' ? 'danger' : ($state === 'sent_home' ? 'warning' : 'success')),
                Tables\Columns\IconColumn::make('needs_review')->label('Review?')->boolean()
                    ->toggleable(),
            ])
            ->defaultSort('visit_date', 'desc')
            ->filters([
                Tables\Filters\Filter::make('date_range')
                    ->form([
                        Forms\Components\DatePicker::make('from')->native(false),
                        Forms\Components\DatePicker::make('to')->native(false),
                    ])
                    ->query(fn ($query, array $data) => $query
                        ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('visit_date', '>=', $d))
                        ->when($data['to']   ?? null, fn ($q, $d) => $q->whereDate('visit_date', '<=', $d))),

                Tables\Filters\SelectFilter::make('complaint')
                    ->options(ClinicComplaint::orderBy('name')->pluck('name', 'id'))
                    ->query(fn ($query, array $data) => $query->when($data['value'] ?? null,
                        fn ($q, $v) => $q->whereHas('complaints', fn ($q) => $q->where('clinic_complaints.id', $v)))),

                Tables\Filters\SelectFilter::make('outcome')->options([
                    'returned_to_class' => 'Returned to class',
                    'sent_home'         => 'Sent home',
                    'referred'          => 'Referred',
                ]),
                Tables\Filters\Filter::make('sick_note')->label('Sick note issued')->toggle()
                    ->query(fn ($query) => $query->where('sick_note_issued', true)),
                Tables\Filters\Filter::make('needs_review')->label('Needs review')->toggle()
                    ->query(fn ($query) => $query->where('needs_review', true)),
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListClinicVisits::route('/'),
            'create' => Pages\CreateClinicVisit::route('/create'),
            'view'   => Pages\ViewClinicVisit::route('/{record}'),
            'edit'   => Pages\EditClinicVisit::route('/{record}/edit'),
        ];
    }
}
