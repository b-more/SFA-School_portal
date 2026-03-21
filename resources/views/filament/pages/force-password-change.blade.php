<x-filament-panels::page>
    <div class="max-w-xl mx-auto">
        <div class="mb-6 p-4 rounded-lg bg-warning-50 dark:bg-warning-900/20 border border-warning-200 dark:border-warning-700">
            <div class="flex gap-3">
                <x-heroicon-o-exclamation-triangle class="w-6 h-6 text-warning-500 shrink-0 mt-0.5" />
                <div>
                    <h3 class="font-semibold text-warning-800 dark:text-warning-200">Password Change Required</h3>
                    <p class="text-sm text-warning-700 dark:text-warning-300 mt-1">
                        For security reasons, you must change your password before continuing. Please choose a strong password that you haven't used before.
                    </p>
                </div>
            </div>
        </div>

        <form wire:submit="changePassword">
            {{ $this->form }}

            <div class="mt-6">
                <x-filament::button type="submit" class="w-full">
                    Change Password & Continue
                </x-filament::button>
            </div>
        </form>
    </div>
</x-filament-panels::page>
