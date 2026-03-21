<?php

namespace App\Filament\Resources;

use App\Constants\RoleConstants;
use App\Filament\Resources\PaymentTransactionResource\Pages;
use App\Models\AcademicYear;
use App\Models\Grade;
use App\Models\ParentGuardian;
use App\Models\PaymentTransaction;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\Term;
use Carbon\Carbon;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PaymentTransactionResource extends Resource
{
    protected static ?string $model = PaymentTransaction::class;

    protected static ?string $navigationIcon = 'heroicon-o-banknotes';

    protected static ?string $navigationGroup = 'Financial Management';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        $user = Auth::user();
        if ($user?->role_id === RoleConstants::STUDENT) return 'My Payments';
        if ($user?->role_id === RoleConstants::PARENT) return 'Fee Payments';
        return 'Payments Received';
    }

    public static function getModelLabel(): string
    {
        return 'Payment';
    }

    public static function getPluralModelLabel(): string
    {
        $user = Auth::user();
        if ($user?->role_id === RoleConstants::STUDENT) return 'My Payments';
        if ($user?->role_id === RoleConstants::PARENT) return 'Fee Payments';
        return 'Payment Transactions';
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Transaction Details')
                    ->schema([
                        Forms\Components\TextInput::make('reference_number')
                            ->label('Reference Number')
                            ->disabled(),
                        Forms\Components\TextInput::make('amount')
                            ->label('Amount (ZMW)')
                            ->numeric()
                            ->disabled(),
                        Forms\Components\TextInput::make('type')
                            ->disabled(),
                        Forms\Components\TextInput::make('payment_method')
                            ->disabled(),
                        Forms\Components\DateTimePicker::make('transaction_date')
                            ->disabled(),
                        Forms\Components\Textarea::make('notes')
                            ->disabled()
                            ->columnSpanFull(),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        $user = Auth::user();
        $isStudent = $user && $user->role_id === RoleConstants::STUDENT;
        $isParent = $user && $user->role_id === RoleConstants::PARENT;
        $isFinance = $user && in_array($user->role_id, [RoleConstants::ADMIN, RoleConstants::ACCOUNTANT]);

        // Get parent's children for the child filter
        $childOptions = [];
        if ($isParent) {
            $parent = ParentGuardian::where('user_id', $user->id)->first();
            if ($parent) {
                $childOptions = $parent->students()
                    ->where('enrollment_status', 'active')
                    ->pluck('name', 'id')
                    ->toArray();
            }
        }

        return $table
            ->defaultSort('transaction_date', 'desc')
            ->striped()
            ->columns([
                Tables\Columns\TextColumn::make('transaction_date')
                    ->label('Date')
                    ->date('d M Y')
                    ->sortable()
                    ->searchable()
                    ->description(fn (PaymentTransaction $record): string =>
                        $record->transaction_date?->format('h:i A') ?? ''
                    ),

                Tables\Columns\TextColumn::make('reference_number')
                    ->label('Receipt No.')
                    ->searchable()
                    ->copyable()
                    ->copyMessage('Copied!')
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->size('sm'),

                Tables\Columns\TextColumn::make('studentFee.student.name')
                    ->label('Student')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->description(fn (PaymentTransaction $record) =>
                        $record->studentFee?->student?->grade?->name ?? ''
                    )
                    ->visible(!$isStudent),

                Tables\Columns\TextColumn::make('studentFee.term.name')
                    ->label('Term')
                    ->sortable()
                    ->badge()
                    ->color('gray')
                    ->size('sm'),

                Tables\Columns\TextColumn::make('amount')
                    ->label('Amount Paid')
                    ->formatStateUsing(fn ($state) => 'K ' . number_format($state, 2))
                    ->sortable()
                    ->weight('bold')
                    ->color('success')
                    ->alignment('right')
                    ->summarize([
                        Tables\Columns\Summarizers\Sum::make()
                            ->money('ZMW')
                            ->label('Total'),
                    ]),

                Tables\Columns\TextColumn::make('payment_method')
                    ->label('Method')
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'cash' => 'Cash',
                        'mobile_money' => 'Mobile Money',
                        'bank_transfer' => 'Bank Transfer',
                        'cheque' => 'Cheque',
                        default => ucfirst(str_replace('_', ' ', $state ?? 'N/A')),
                    })
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'cash' => 'success',
                        'mobile_money' => 'info',
                        'bank_transfer' => 'warning',
                        'cheque' => 'primary',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'payment' => 'Payment',
                        'refund' => 'Refund',
                        'adjustment' => 'Adjustment',
                        'balance_forward' => 'B/F',
                        'overpayment' => 'Overpayment',
                        'credit_applied' => 'Credit',
                        default => ucfirst($state ?? ''),
                    })
                    ->color(fn ($state) => match ($state) {
                        'payment' => 'success',
                        'refund' => 'danger',
                        'adjustment' => 'warning',
                        'overpayment' => 'info',
                        default => 'gray',
                    })
                    ->toggleable(isToggledHiddenByDefault: $isParent || $isStudent),

                Tables\Columns\TextColumn::make('studentFee.balance')
                    ->label('Balance')
                    ->formatStateUsing(fn ($state) => 'K ' . number_format($state ?? 0, 2))
                    ->sortable()
                    ->color(fn ($state) => ($state ?? 0) > 0 ? 'danger' : 'success')
                    ->weight(fn ($state) => ($state ?? 0) > 0 ? 'bold' : 'normal'),

                Tables\Columns\TextColumn::make('studentFee.payment_status')
                    ->label('Status')
                    ->badge()
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'paid' => 'Paid',
                        'partial' => 'Partial',
                        'unpaid' => 'Unpaid',
                        'overpaid' => 'Overpaid',
                        default => ucfirst($state ?? ''),
                    })
                    ->color(fn ($state) => match ($state) {
                        'paid' => 'success',
                        'partial' => 'warning',
                        'unpaid' => 'danger',
                        'overpaid' => 'info',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('notes')
                    ->label('Notes')
                    ->limit(30)
                    ->tooltip(fn ($record) => $record->notes)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('processedBy.name')
                    ->label('Processed By')
                    ->visible($isFinance)
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Recorded')
                    ->dateTime('d M Y, h:i A')
                    ->sortable()
                    ->visible($isFinance)
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Parent child selector
                SelectFilter::make('child')
                    ->label('Child')
                    ->options($childOptions)
                    ->visible($isParent && count($childOptions) > 1)
                    ->query(function (Builder $query, array $data) {
                        if ($data['value']) {
                            $query->whereHas('studentFee', fn (Builder $q) =>
                                $q->where('student_id', $data['value'])
                            );
                        }
                    })
                    ->native(false)
                    ->indicator('Child'),

                Filter::make('transaction_date')
                    ->form([
                        Forms\Components\DatePicker::make('from')
                            ->label('From')
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->closeOnDateSelection(),
                        Forms\Components\DatePicker::make('until')
                            ->label('Until')
                            ->native(false)
                            ->displayFormat('d M Y')
                            ->closeOnDateSelection(),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'], fn (Builder $q, $d) => $q->whereDate('transaction_date', '>=', $d))
                            ->when($data['until'], fn (Builder $q, $d) => $q->whereDate('transaction_date', '<=', $d));
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];
                        if ($data['from'] ?? null) $indicators[] = 'From: ' . Carbon::parse($data['from'])->format('d M Y');
                        if ($data['until'] ?? null) $indicators[] = 'Until: ' . Carbon::parse($data['until'])->format('d M Y');
                        return $indicators;
                    })
                    ->columnSpan(2),

                SelectFilter::make('term')
                    ->label('Term')
                    ->relationship('studentFee.term', 'name')
                    ->preload()
                    ->native(false)
                    ->indicator('Term'),

                SelectFilter::make('payment_method')
                    ->label('Payment Method')
                    ->options([
                        'cash' => 'Cash',
                        'mobile_money' => 'Mobile Money',
                        'bank_transfer' => 'Bank Transfer',
                        'cheque' => 'Cheque',
                        'other' => 'Other',
                    ])
                    ->native(false)
                    ->indicator('Method'),

                SelectFilter::make('payment_status')
                    ->label('Fee Status')
                    ->options([
                        'paid' => 'Fully Paid',
                        'partial' => 'Partially Paid',
                        'unpaid' => 'Unpaid',
                    ])
                    ->query(function (Builder $query, array $data) {
                        if ($data['value']) {
                            $query->whereHas('studentFee', fn (Builder $q) =>
                                $q->where('payment_status', $data['value'])
                            );
                        }
                    })
                    ->native(false)
                    ->indicator('Status'),

                SelectFilter::make('grade')
                    ->label('Grade')
                    ->options(Grade::pluck('name', 'id'))
                    ->native(false)
                    ->indicator('Grade')
                    ->query(function (Builder $query, $state) {
                        if ($state['value']) {
                            $query->whereHas('studentFee.student', fn (Builder $q) =>
                                $q->where('grade_id', $state['value'])
                            );
                        }
                    })
                    ->visible($isFinance),
            ], layout: Tables\Enums\FiltersLayout::AboveContentCollapsible)
            ->actions([
                Tables\Actions\Action::make('viewDetails')
                    ->label('')
                    ->icon('heroicon-m-eye')
                    ->color('gray')
                    ->tooltip('View Details')
                    ->modalHeading(fn (PaymentTransaction $record) =>
                        'Payment Receipt — ' . $record->reference_number
                    )
                    ->modalContent(function (PaymentTransaction $record) {
                        $student = $record->studentFee?->student;
                        $fee = $record->studentFee;
                        $feeStructure = $fee?->feeStructure;

                        $html = '<div class="space-y-4 text-sm">';

                        // Header
                        $html .= '<div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4 border border-gray-200 dark:border-gray-600">';
                        $html .= '<div class="grid grid-cols-2 gap-3">';
                        $html .= '<div><span class="text-gray-500 dark:text-gray-400">Student:</span> <strong>' . e($student?->name ?? 'N/A') . '</strong></div>';
                        $html .= '<div><span class="text-gray-500 dark:text-gray-400">Grade:</span> <strong>' . e($student?->grade?->name ?? 'N/A') . '</strong></div>';
                        $html .= '<div><span class="text-gray-500 dark:text-gray-400">Receipt No:</span> <strong class="text-blue-600">' . e($record->reference_number) . '</strong></div>';
                        $html .= '<div><span class="text-gray-500 dark:text-gray-400">Date:</span> <strong>' . ($record->transaction_date?->format('d M Y, h:i A') ?? '') . '</strong></div>';
                        $html .= '</div>';
                        $html .= '</div>';

                        // Amount
                        $html .= '<div class="text-center py-3">';
                        $html .= '<p class="text-gray-500 dark:text-gray-400 text-xs uppercase tracking-wide">Amount Paid</p>';
                        $html .= '<p class="text-3xl font-bold text-green-600">K ' . number_format($record->amount, 2) . '</p>';
                        $html .= '<p class="text-gray-500 dark:text-gray-400 text-xs mt-1">via ' . ucfirst(str_replace('_', ' ', $record->payment_method ?? 'N/A')) . '</p>';
                        $html .= '</div>';

                        // Fee breakdown
                        if ($feeStructure) {
                            $html .= '<div class="border-t border-gray-200 dark:border-gray-600 pt-3">';
                            $html .= '<div class="grid grid-cols-3 gap-3 text-center">';
                            $html .= '<div><p class="text-xs text-gray-400">Total Fee</p><p class="font-semibold">K ' . number_format($feeStructure->total_fee ?? 0, 2) . '</p></div>';
                            $html .= '<div><p class="text-xs text-gray-400">Total Paid</p><p class="font-semibold text-green-600">K ' . number_format($fee?->amount_paid ?? 0, 2) . '</p></div>';
                            $balance = $fee?->balance ?? 0;
                            $balanceColor = $balance > 0 ? 'text-red-600' : 'text-green-600';
                            $html .= '<div><p class="text-xs text-gray-400">Balance</p><p class="font-semibold ' . $balanceColor . '">K ' . number_format($balance, 2) . '</p></div>';
                            $html .= '</div>';
                            $html .= '</div>';
                        }

                        if ($record->notes) {
                            $html .= '<div class="border-t border-gray-200 dark:border-gray-600 pt-3">';
                            $html .= '<p class="text-xs text-gray-400">Notes</p>';
                            $html .= '<p class="text-gray-700 dark:text-gray-300">' . e($record->notes) . '</p>';
                            $html .= '</div>';
                        }

                        $html .= '</div>';
                        return new \Illuminate\Support\HtmlString($html);
                    })
                    ->modalSubmitAction(false)
                    ->modalCancelActionLabel('Close')
                    ->modalWidth('lg'),

                Tables\Actions\Action::make('printReceipt')
                    ->label('Receipt')
                    ->icon('heroicon-m-arrow-down-tray')
                    ->color('success')
                    ->tooltip('Download Receipt PDF')
                    ->size('sm')
                    ->url(fn (PaymentTransaction $record) => route('student-fees.transaction-receipt', [
                        'fee' => $record->student_fee_id,
                        'transaction' => $record->id,
                    ]))
                    ->openUrlInNewTab(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('exportSelected')
                        ->label('Export to PDF')
                        ->icon('heroicon-m-arrow-down-tray')
                        ->color('primary')
                        ->requiresConfirmation()
                        ->modalHeading('Export Payment Records')
                        ->modalDescription('Export selected payment transactions to PDF.')
                        ->action(function ($records) {
                            // Export functionality
                        })
                        ->visible($isFinance),
                ]),
            ])
            ->emptyStateHeading($isParent
                ? 'No Payments Found'
                : ($isStudent ? 'No Payments Found' : 'No Payment Transactions')
            )
            ->emptyStateDescription($isParent
                ? 'Payment records for your children will appear here once payments are made.'
                : ($isStudent
                    ? 'Your payment transactions will appear here once payments are recorded.'
                    : 'Payment transactions will appear here once fees are recorded.'
                )
            )
            ->emptyStateIcon('heroicon-o-banknotes')
            ->paginated([10, 25, 50, 100])
            ->defaultPaginationPageOption(25)
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->deferLoading();
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery()
            ->with([
                'studentFee.student.grade',
                'studentFee.student.classSection',
                'studentFee.student.parentGuardian',
                'studentFee.feeStructure',
                'studentFee.term',
                'studentFee.academicYear',
                'processedBy',
            ]);

        $user = Auth::user();

        if ($user && $user->role_id === RoleConstants::STUDENT) {
            $student = Student::where('user_id', $user->id)->first();
            if ($student) {
                $query->whereHas('studentFee', fn (Builder $q) => $q->where('student_id', $student->id));
            } else {
                return $query->whereRaw('1 = 0');
            }
        } elseif ($user && $user->role_id === RoleConstants::PARENT) {
            $parent = ParentGuardian::where('user_id', $user->id)->first();
            if ($parent) {
                $childrenIds = $parent->students()->where('enrollment_status', 'active')->pluck('id');
                if ($childrenIds->isNotEmpty()) {
                    $query->whereHas('studentFee', fn (Builder $q) => $q->whereIn('student_id', $childrenIds));
                } else {
                    return $query->whereRaw('1 = 0');
                }
            } else {
                return $query->whereRaw('1 = 0');
            }
        }

        return $query;
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPaymentTransactions::route('/'),
            'view' => Pages\ViewPaymentTransaction::route('/{record}'),
        ];
    }

    public static function canCreate(): bool
    {
        return false;
    }

    public static function canEdit($record): bool
    {
        return false;
    }

    public static function canDelete($record): bool
    {
        return false;
    }

    public static function shouldRegisterNavigation(): bool
    {
        $user = Auth::user();
        if (!$user) return false;

        return in_array($user->role_id, [
            RoleConstants::ADMIN,
            RoleConstants::ACCOUNTANT,
            RoleConstants::STUDENT,
            RoleConstants::PARENT,
        ]);
    }
}
