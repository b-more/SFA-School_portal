<?php

namespace App\Filament\Resources\StudentResource\Pages;

use App\Filament\Resources\StudentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Filament\Notifications\Notification;

class EditStudent extends EditRecord
{
    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        // No need to modify data before filling the form
        return $data;
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If grade changed but student ID wasn't manually updated, generate new ID
        $originalGradeId = $this->record->grade_id;
        $newGradeId = $data['grade_id'] ?? $originalGradeId;

        if ($originalGradeId != $newGradeId) {
            $newGrade = \App\Models\Grade::find($newGradeId);

            if ($newGrade) {
                $shouldGenerateNewId = empty($data['student_id_number']);

                if ($shouldGenerateNewId) {
                    $data['student_id_number'] = StudentResource::generateStudentId($newGrade);

                    $originalGradeName = $this->record->grade?->name ?? 'Unknown';
                    Notification::make()
                        ->title('Student ID Updated')
                        ->body("Because the grade changed from {$originalGradeName} to {$newGrade->name}, the student ID was updated to {$data['student_id_number']}.")
                        ->success()
                        ->send();
                }
            }
        }

        return $data;
    }
}
