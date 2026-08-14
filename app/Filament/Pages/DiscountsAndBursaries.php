<?php

namespace App\Filament\Pages;

use App\Constants\RoleConstants;
use App\Models\SchoolSettings;
use App\Models\Term;
use App\Services\DiscountPolicyService;
use Filament\Forms\Components\Select;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class DiscountsAndBursaries extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon  = 'heroicon-o-receipt-percent';
    protected static ?string $navigationGroup = 'Accounts & Finance';
    protected static ?string $navigationLabel = 'Discounts & Bursaries';
    protected static ?int    $navigationSort  = 11;
    protected static ?string $slug            = 'discounts-and-bursaries';
    protected static ?string $title           = 'Discounts & Bursaries';
    protected static string  $view            = 'filament.pages.discounts-and-bursaries';

    public ?int $termId = null;

    public static function shouldRegisterNavigation(): bool { return self::canAccess(); }

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role_id, [
            RoleConstants::ADMIN,
            RoleConstants::ACCOUNTANT,
            RoleConstants::DIRECTOR,
        ], true);
    }

    public function mount(): void
    {
        $this->termId = Term::where('is_current', true)->value('id')
            ?? Term::where('is_active', true)->orderBy('start_date', 'desc')->value('id')
            ?? Term::orderBy('start_date', 'desc')->value('id');
        $this->form->fill(['termId' => $this->termId]);
    }

    public function form(Form $form): Form
    {
        return $form->statePath('')->schema([
            Select::make('termId')
                ->label('Term')
                ->options(Term::orderBy('academic_year_id', 'desc')->orderBy('start_date')->get()->mapWithKeys(fn ($t) => [$t->id => $t->name . '  (' . optional($t->academicYear)->name . ')']))
                ->required()
                ->live(),
        ]);
    }

    /* --------------------------------------------------------------- */
    /* Data helpers used by the view                                     */
    /* --------------------------------------------------------------- */

    public function getSiblingPreviewProperty(): array
    {
        return app(DiscountPolicyService::class)->previewSibling((int) $this->termId);
    }

    public function getEarlyPreviewProperty(): array
    {
        return app(DiscountPolicyService::class)->previewEarlyPayment((int) $this->termId);
    }

    public function getCurrentTotalsProperty(): array
    {
        return app(DiscountPolicyService::class)->currentByTerm((int) $this->termId);
    }

    public function getSettingsProperty(): SchoolSettings
    {
        return SchoolSettings::getInstance();
    }

    /* --------------------------------------------------------------- */
    /* Actions                                                           */
    /* --------------------------------------------------------------- */

    public function applySibling(): void
    {
        $out = app(DiscountPolicyService::class)->applySibling((int) $this->termId, auth()->id());
        Notification::make()
            ->title('Sibling discount applied')
            ->body("Applied to {$out['applied']} fee row(s), skipped {$out['skipped']} (already up to date or held by another discount type).")
            ->success()->send();
    }

    public function applyEarlyPayment(): void
    {
        $out = app(DiscountPolicyService::class)->applyEarlyPayment((int) $this->termId, auth()->id());
        Notification::make()
            ->title('Early payment discount applied')
            ->body("Applied to {$out['applied']} fee row(s), skipped {$out['skipped']}.")
            ->success()->send();
    }

    public function reverseSibling(): void
    {
        $n = app(DiscountPolicyService::class)->reverse((int) $this->termId, DiscountPolicyService::TYPE_SIBLING, auth()->id());
        Notification::make()->title('Sibling discount reversed')->body("Cleared from {$n} fee row(s).")->warning()->send();
    }

    public function reverseEarlyPayment(): void
    {
        $n = app(DiscountPolicyService::class)->reverse((int) $this->termId, DiscountPolicyService::TYPE_EARLY_PAYMENT, auth()->id());
        Notification::make()->title('Early payment discount reversed')->body("Cleared from {$n} fee row(s).")->warning()->send();
    }
}
