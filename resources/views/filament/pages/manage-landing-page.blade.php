<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex flex-wrap items-center gap-3">
            @foreach ($this->getFormActions() as $action)
                {{ $action }}
            @endforeach
        </div>
    </form>

    <x-filament::section class="mt-8" collapsible collapsed>
        <x-slot name="heading">Tips for great landing-page copy</x-slot>

        <ul class="list-disc list-inside text-sm text-gray-600 dark:text-gray-400 space-y-1">
            <li><strong>Headline + accent:</strong> Keep the headline short and confident. The accent line appears in gold below it — use it for a second beat.</li>
            <li><strong>Sub-headline:</strong> One or two sentences explaining who you serve and what makes the school distinctive.</li>
            <li><strong>CTA URLs:</strong> Use <code>#contact</code> to scroll to the contact form, <code>#portal</code> for portal cards, or any full URL.</li>
            <li><strong>Hero image:</strong> Upload a high-quality landscape (≥ 1920×1080). Bright, on-brand campus photos work best.</li>
            <li><strong>Gallery:</strong> First image becomes a wide tile. Aim for variety — classrooms, sports, ceremonies, sciences.</li>
            <li><strong>Testimonials</strong> are managed under <em>Website Management → Testimonials</em>.</li>
            <li><strong>News & Events</strong> sections automatically pull recent records from the existing modules.</li>
        </ul>
    </x-filament::section>
</x-filament-panels::page>
