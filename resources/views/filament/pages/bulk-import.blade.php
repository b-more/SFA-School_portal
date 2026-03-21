<x-filament-panels::page>
    <form wire:submit.prevent="import">
        {{ $this->form }}

        <div class="mt-6 flex gap-3">
            <x-filament::button type="button" wire:click="preview" color="gray">
                Preview Data
            </x-filament::button>

            <x-filament::button type="submit">
                Import Data
            </x-filament::button>
        </div>
    </form>

    @if(count($previewData) > 0)
        <x-filament::section class="mt-6">
            <x-slot name="heading">
                Preview (First {{ count($previewData) }} rows)
            </x-slot>

            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-gray-100 dark:bg-gray-800">
                            @if(isset($data['importType']) && $data['importType'] === 'parents')
                                <th class="px-4 py-2 text-left">No.</th>
                                <th class="px-4 py-2 text-left">Full Name</th>
                                <th class="px-4 py-2 text-left">Email</th>
                                <th class="px-4 py-2 text-left">Phone</th>
                                <th class="px-4 py-2 text-left">Relationship</th>
                                <th class="px-4 py-2 text-left">Occupation</th>
                            @elseif(isset($data['importType']) && $data['importType'] === 'students')
                                <th class="px-4 py-2 text-left">No.</th>
                                <th class="px-4 py-2 text-left">Full Name</th>
                                <th class="px-4 py-2 text-left">Gender</th>
                                <th class="px-4 py-2 text-left">Date of Birth</th>
                                <th class="px-4 py-2 text-left">Grade</th>
                                <th class="px-4 py-2 text-left">Section</th>
                                <th class="px-4 py-2 text-left">Parent Phone</th>
                            @elseif(isset($data['importType']) && $data['importType'] === 'teachers')
                                <th class="px-4 py-2 text-left">No.</th>
                                <th class="px-4 py-2 text-left">Full Name</th>
                                <th class="px-4 py-2 text-left">Email</th>
                                <th class="px-4 py-2 text-left">Phone</th>
                                <th class="px-4 py-2 text-left">Qualification</th>
                                <th class="px-4 py-2 text-left">Grade</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($previewData as $row)
                            <tr class="border-b dark:border-gray-700">
                                @if(isset($data['importType']) && $data['importType'] === 'parents')
                                    <td class="px-4 py-2">{{ $row[0] }}</td>
                                    <td class="px-4 py-2">{{ $row[1] }}</td>
                                    <td class="px-4 py-2">{{ $row[2] }}</td>
                                    <td class="px-4 py-2">{{ $row[3] }}</td>
                                    <td class="px-4 py-2">{{ $row[7] }}</td>
                                    <td class="px-4 py-2">{{ $row[8] }}</td>
                                @elseif(isset($data['importType']) && $data['importType'] === 'students')
                                    <td class="px-4 py-2">{{ $row[0] }}</td>
                                    <td class="px-4 py-2">{{ $row[1] }}</td>
                                    <td class="px-4 py-2">{{ $row[2] }}</td>
                                    <td class="px-4 py-2">{{ $row[3] }}</td>
                                    <td class="px-4 py-2">{{ $row[6] }}</td>
                                    <td class="px-4 py-2">{{ $row[7] }}</td>
                                    <td class="px-4 py-2">{{ $row[10] }}</td>
                                @elseif(isset($data['importType']) && $data['importType'] === 'teachers')
                                    <td class="px-4 py-2">{{ $row[0] }}</td>
                                    <td class="px-4 py-2">{{ $row[1] }}</td>
                                    <td class="px-4 py-2">{{ $row[2] }}</td>
                                    <td class="px-4 py-2">{{ $row[3] }}</td>
                                    <td class="px-4 py-2">{{ $row[6] }}</td>
                                    <td class="px-4 py-2">{{ $row[12] }}</td>
                                @endif
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </x-filament::section>
    @endif

    @if($showResults)
        <x-filament::section class="mt-6">
            <x-slot name="heading">
                Import Results
            </x-slot>

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg border border-green-200 dark:border-green-800">
                        <div class="text-sm text-green-600 dark:text-green-400 font-medium">Successful Imports</div>
                        <div class="text-3xl font-bold text-green-700 dark:text-green-300 mt-2">{{ $successCount }}</div>
                    </div>

                    <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border border-red-200 dark:border-red-800">
                        <div class="text-sm text-red-600 dark:text-red-400 font-medium">Failed Imports</div>
                        <div class="text-3xl font-bold text-red-700 dark:text-red-300 mt-2">{{ $errorCount }}</div>
                    </div>
                </div>

                @if(count($errors) > 0)
                    <div class="mt-4">
                        <h4 class="font-semibold text-red-700 dark:text-red-300 mb-2">Errors:</h4>
                        <div class="bg-red-50 dark:bg-red-900/20 p-4 rounded-lg border border-red-200 dark:border-red-800 max-h-64 overflow-y-auto">
                            <ul class="space-y-1 text-sm text-red-600 dark:text-red-400">
                                @foreach($errors as $error)
                                    <li class="flex items-start gap-2">
                                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                        <span>{{ $error }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                @endif
            </div>
        </x-filament::section>
    @endif

    <x-filament::section class="mt-6">
        <x-slot name="heading">
            Important Notes
        </x-slot>

        <div class="prose dark:prose-invert max-w-none">
            <ul class="text-sm space-y-2">
                <li><strong>Default Password:</strong> All imported users will have the password <code>password123</code>. They should change it upon first login.</li>
                <li><strong>Parent Matching:</strong> When importing students, the system will match parents by phone number. If not found, a new parent record will be created.</li>
                <li><strong>Duplicate Prevention:</strong> The system checks for duplicates based on key identifiers (phone for parents, email for teachers, name+DOB for students).</li>
                <li><strong>Required Fields:</strong> Fields marked with * in the templates must be filled. Rows with missing required fields will be skipped.</li>
                <li><strong>Data Validation:</strong> All data is validated before import. Invalid data will be reported in the errors list.</li>
                <li><strong>Transaction Safety:</strong> All imports are performed in a database transaction. If a critical error occurs, all changes will be rolled back.</li>
            </ul>
        </div>
    </x-filament::section>
</x-filament-panels::page>
