<?php

namespace App\Filament\Widgets;

use App\Models\BusPayment;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class ValidBusPassesWidget extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;

    protected static bool $isDiscovered = false;

    public ?string $activeMonth = null;

    public ?string $activeYear = null;

    protected $listeners = ['refreshValidBusPasses' => '$refresh'];

    public function mount(): void
    {
        $this->activeMonth = now()->format('F');
        $this->activeYear = (string) now()->year;
    }

    public function setMonth(string $month): void
    {
        $this->activeMonth = $month;
        $this->resetTable();
    }

    public function setYear(string $year): void
    {
        $this->activeYear = $year;
        $this->resetTable();
    }

    public function table(Table $table): Table
    {
        $months = [
            'January', 'February', 'March', 'April', 'May', 'June',
            'July', 'August', 'September', 'October', 'November', 'December',
        ];

        $monthActions = [];
        foreach ($months as $month) {
            $count = BusPayment::where('month', $month)
                ->where('year', $this->activeYear)
                ->whereIn('payment_status', ['paid', 'partial'])
                ->count();

            $isActive = $this->activeMonth === $month;

            $monthActions[] = Tables\Actions\Action::make("month_{$month}")
                ->label(substr($month, 0, 3) . ($count > 0 ? " ({$count})" : ''))
                ->color($isActive ? 'primary' : 'gray')
                ->size('sm')
                ->outlined(!$isActive)
                ->action(fn () => $this->setMonth($month));
        }

        // Year toggle actions
        $yearActions = [];
        foreach (range(now()->year - 1, now()->year + 1) as $year) {
            $isActiveYear = $this->activeYear === (string) $year;
            $yearActions[] = Tables\Actions\Action::make("year_{$year}")
                ->label((string) $year)
                ->color($isActiveYear ? 'warning' : 'gray')
                ->size('sm')
                ->outlined(!$isActiveYear)
                ->action(fn () => $this->setYear((string) $year));
        }

        return $table
            ->heading('Valid Bus Passes - ' . $this->activeMonth . ' ' . $this->activeYear)
            ->description('Students with paid or partially paid bus passes for the selected month')
            ->query(
                BusPayment::query()
                    ->where('month', $this->activeMonth)
                    ->where('year', $this->activeYear)
                    ->whereIn('payment_status', ['paid', 'partial'])
                    ->with(['student.grade', 'busFareStructure'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('student.name')
                    ->label('Student Name')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-user'),

                Tables\Columns\TextColumn::make('student.student_id_number')
                    ->label('Student ID')
                    ->searchable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('student.grade.name')
                    ->label('Grade')
                    ->sortable(),

                Tables\Columns\TextColumn::make('busFareStructure.route_name')
                    ->label('Route')
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-m-map-pin'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Fare')
                    ->money('ZMW')
                    ->sortable(),

                Tables\Columns\TextColumn::make('amount_paid')
                    ->label('Paid')
                    ->money('ZMW')
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('balance')
                    ->label('Balance')
                    ->money('ZMW')
                    ->color(fn ($state) => $state > 0 ? 'danger' : 'success')
                    ->sortable(),

                Tables\Columns\BadgeColumn::make('payment_status')
                    ->label('Status')
                    ->colors([
                        'success' => 'paid',
                        'warning' => 'partial',
                    ])
                    ->icons([
                        'heroicon-o-check-circle' => 'paid',
                        'heroicon-o-clock' => 'partial',
                    ]),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('bus_fare_structure_id')
                    ->label('Route')
                    ->relationship('busFareStructure', 'route_name')
                    ->preload()
                    ->native(false),

                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'paid' => 'Fully Paid',
                        'partial' => 'Partially Paid',
                    ])
                    ->native(false),
            ])
            ->actions([
                Tables\Actions\Action::make('view_pass')
                    ->label('View Pass')
                    ->icon('heroicon-o-ticket')
                    ->color('primary')
                    ->url(fn (BusPayment $record) => route('bus-passes.view', $record))
                    ->openUrlInNewTab(),

                Tables\Actions\Action::make('view_receipt')
                    ->label('Receipt')
                    ->icon('heroicon-o-document-text')
                    ->color('success')
                    ->url(fn (BusPayment $record) => route('bus-receipts.view', $record))
                    ->openUrlInNewTab(),
            ])
            ->headerActions(array_merge($yearActions, $monthActions))
            ->emptyStateHeading('No Valid Bus Passes')
            ->emptyStateDescription("No students have paid bus passes for {$this->activeMonth} {$this->activeYear}.")
            ->emptyStateIcon('heroicon-o-ticket')
            ->defaultSort('student.name')
            ->paginated([10, 25, 50]);
    }
}
