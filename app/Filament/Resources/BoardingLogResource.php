<?php

namespace App\Filament\Resources;

use App\Constants\RoleConstants;
use App\Filament\Resources\BoardingLogResource\Pages;
use App\Models\BusBoardingLog;
use App\Models\BusFareStructure;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class BoardingLogResource extends Resource
{
    protected static ?string $model = BusBoardingLog::class;

    protected static ?string $slug = 'boarding-logs';

    protected static ?string $navigationIcon = 'heroicon-o-clipboard-document-check';

    protected static ?string $navigationGroup = 'Finance Management';

    protected static ?string $navigationLabel = 'Bus Boarding History';

    protected static ?string $modelLabel = 'Boarding Log';

    protected static ?string $pluralModelLabel = 'Boarding Logs';

    protected static ?int $navigationSort = 8;

    public static function shouldRegisterNavigation(): bool
    {
        return in_array(auth()->user()?->role_id, [
            RoleConstants::ADMIN,
            RoleConstants::DIRECTOR,
            RoleConstants::SCHOOL_SECRETARY,
        ]);
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with([
                'busFareStructure:id,route_name',
                'student:id,name,grade_id,parent_guardian_id',
                'student.grade:id,name',
                'student.parentGuardian:id,name,phone',
                'recordedBy:id,name',
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('date', 'desc')
            ->columns([
                Tables\Columns\TextColumn::make('date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('busFareStructure.route_name')
                    ->label('Route')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-map-pin')
                    ->badge()
                    ->color('info'),

                Tables\Columns\BadgeColumn::make('trip')
                    ->label('Trip')
                    ->colors([
                        'warning' => 'to_school',
                        'info' => 'from_school',
                    ])
                    ->icons([
                        'heroicon-o-sun' => 'to_school',
                        'heroicon-o-moon' => 'from_school',
                    ])
                    ->formatStateUsing(fn ($state) => BusBoardingLog::trips()[$state] ?? $state),

                Tables\Columns\TextColumn::make('student.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user')
                    ->weight('bold'),

                Tables\Columns\TextColumn::make('student.grade.name')
                    ->label('Grade')
                    ->toggleable(),

                Tables\Columns\BadgeColumn::make('status')
                    ->label('Status')
                    ->colors([
                        'success' => 'boarded',
                        'gray' => 'absent',
                        'danger' => 'no_show',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'boarded',
                        'heroicon-o-minus-circle' => 'absent',
                        'heroicon-o-x-circle' => 'no_show',
                    ])
                    ->formatStateUsing(fn ($state) => BusBoardingLog::statuses()[$state] ?? $state),

                Tables\Columns\TextColumn::make('student.parentGuardian.name')
                    ->label('Parent')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('student.parentGuardian.phone')
                    ->label('Parent Phone')
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('recordedBy.name')
                    ->label('Driver')
                    ->placeholder('—')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Recorded')
                    ->dateTime('d M Y H:i')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(40)
                    ->placeholder('—')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\Filter::make('date')
                    ->form([
                        \Filament\Forms\Components\DatePicker::make('from')->label('From')->native(false),
                        \Filament\Forms\Components\DatePicker::make('to')->label('To')->native(false),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when($data['from'] ?? null, fn ($q, $d) => $q->whereDate('date', '>=', $d))
                            ->when($data['to'] ?? null, fn ($q, $d) => $q->whereDate('date', '<=', $d));
                    })
                    ->indicateUsing(function (array $data) {
                        $i = [];
                        if ($data['from'] ?? null) $i[] = 'From ' . \Carbon\Carbon::parse($data['from'])->format('d M Y');
                        if ($data['to'] ?? null) $i[] = 'To ' . \Carbon\Carbon::parse($data['to'])->format('d M Y');
                        return $i;
                    }),

                Tables\Filters\SelectFilter::make('bus_fare_structure_id')
                    ->label('Route')
                    ->options(fn () => BusFareStructure::orderBy('route_name')->pluck('route_name', 'id')->toArray())
                    ->native(false),

                Tables\Filters\SelectFilter::make('trip')
                    ->options(BusBoardingLog::trips())
                    ->native(false),

                Tables\Filters\SelectFilter::make('status')
                    ->options(BusBoardingLog::statuses())
                    ->native(false),
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->actions([
                Tables\Actions\ViewAction::make(),
            ])
            ->bulkActions([])
            ->emptyStateHeading('No Boarding Records')
            ->emptyStateDescription('Drivers mark boarding from their dashboard. Once they do, records appear here.')
            ->emptyStateIcon('heroicon-o-clipboard-document-check');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBoardingLogs::route('/'),
            'view' => Pages\ViewBoardingLog::route('/{record}'),
        ];
    }
}
