<?php

namespace App\Filament\Resources;

use App\Constants\RoleConstants;
use App\Filament\Resources\ClinicComplaintResource\Pages;
use App\Models\ClinicComplaint;
use Filament\Forms;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class ClinicComplaintResource extends Resource
{
    protected static ?string $model = ClinicComplaint::class;

    protected static ?string $navigationIcon  = 'heroicon-o-clipboard-document-list';
    protected static ?string $navigationGroup = 'Clinic';
    protected static ?string $navigationLabel = 'Complaints (medical)';
    protected static ?string $modelLabel      = 'Clinic complaint';
    protected static ?int    $navigationSort  = 3;

    public static function canAccess(): bool
    {
        return in_array(auth()->user()?->role_id, [
            RoleConstants::ADMIN, RoleConstants::CLINICIAN,
        ], true);
    }

    public static function shouldRegisterNavigation(): bool { return self::canAccess(); }

    public static function form(Forms\Form $form): Forms\Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('name')->required()->maxLength(120),
            Forms\Components\Toggle::make('is_active')->default(true),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('visits_count')->counts('visits')->label('Visits recorded'),
                Tables\Columns\IconColumn::make('is_active')->boolean(),
                Tables\Columns\TextColumn::make('created_at')->dateTime()->since()->sortable(),
            ])
            ->defaultSort('name')
            ->actions([Tables\Actions\EditAction::make()])
            ->bulkActions([Tables\Actions\DeleteBulkAction::make()]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListClinicComplaints::route('/'),
            'create' => Pages\CreateClinicComplaint::route('/create'),
            'edit'   => Pages\EditClinicComplaint::route('/{record}/edit'),
        ];
    }
}
