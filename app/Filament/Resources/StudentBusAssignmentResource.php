<?php

namespace App\Filament\Resources;

use App\Filament\Resources\StudentBusAssignmentResource\Pages;
use App\Models\BusFareStructure;
use App\Models\FeeCategory;
use App\Models\Student;
use App\Models\StudentBusAssignment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class StudentBusAssignmentResource extends Resource
{
    protected static ?string $model = StudentBusAssignment::class;
    protected static ?string $navigationIcon = 'heroicon-o-truck';
    protected static ?string $navigationGroup = 'Fees';
    protected static ?string $navigationLabel = 'Bus Assignments';
    protected static ?int $navigationSort = 32;
    // Hidden from navigation: bus is pay-as-you-go (parents pay K500 per month
    // at the cashier to board that month). No standing assignment / no debt.
    protected static bool $shouldRegisterNavigation = false;

    public static function form(Form $form): Form
    {
        $defaultMonthly = FeeCategory::where('code', FeeCategory::BUS)->value('default_amount') ?? 500;

        return $form->schema([
            Forms\Components\Select::make('student_id')
                ->label('Student')->relationship('student', 'name')
                ->searchable()->preload()->required(),
            Forms\Components\Select::make('bus_fare_structure_id')
                ->label('Route (optional)')
                ->options(BusFareStructure::orderBy('route_name')->pluck('route_name', 'id'))
                ->searchable()->nullable(),
            Forms\Components\TextInput::make('monthly_amount')->label('Monthly amount (K)')
                ->required()->numeric()->minValue(0)->step(0.01)->default($defaultMonthly),
            Forms\Components\DatePicker::make('started_at')->required()->default(now()),
            Forms\Components\DatePicker::make('ended_at')->nullable()
                ->helperText('Leave blank while the student is still riding the bus.'),
            Forms\Components\Toggle::make('is_active')->default(true),
            Forms\Components\Textarea::make('notes')->rows(2)->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('id', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('student.name')->label('Student')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('route.route_name')->label('Route')->placeholder('—'),
                Tables\Columns\TextColumn::make('monthly_amount')->money('ZMW'),
                Tables\Columns\TextColumn::make('started_at')->date()->sortable(),
                Tables\Columns\TextColumn::make('ended_at')->date()->placeholder('—'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
            ])
            ->filters([Tables\Filters\TernaryFilter::make('is_active')])
            ->actions([Tables\Actions\EditAction::make(), Tables\Actions\DeleteAction::make()]);
    }

    public static function getPages(): array
    {
        return ['index' => Pages\ManageStudentBusAssignments::route('/')];
    }
}
