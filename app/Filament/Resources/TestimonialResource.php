<?php

namespace App\Filament\Resources;

use App\Constants\RoleConstants;
use App\Filament\Resources\TestimonialResource\Pages;
use App\Models\Testimonial;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class TestimonialResource extends Resource
{
    protected static ?string $model = Testimonial::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Website Management';

    protected static ?string $navigationLabel = 'Testimonials';

    protected static ?int $navigationSort = 30;

    public static function shouldRegisterNavigation(): bool
    {
        $user = auth()->user();
        return $user && in_array($user->role_id, [
            RoleConstants::ADMIN,
            RoleConstants::DIRECTOR ?? 11,
        ], true);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Testimonial')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->maxLength(120),
                    Forms\Components\TextInput::make('role')
                        ->label('Role / Relationship')
                        ->placeholder('e.g. Parent · Grade 4')
                        ->maxLength(120),
                    Forms\Components\Textarea::make('quote')
                        ->label('Quote')
                        ->rows(4)
                        ->required()
                        ->maxLength(600)
                        ->columnSpanFull(),
                    Forms\Components\TextInput::make('avatar_initials')
                        ->label('Avatar Initials')
                        ->maxLength(4)
                        ->helperText('Leave blank to derive automatically from the name.'),
                    Forms\Components\TextInput::make('sort_order')
                        ->label('Sort Order')
                        ->numeric()
                        ->default(0)
                        ->helperText('Lower numbers appear first.'),
                    Forms\Components\Toggle::make('is_active')
                        ->label('Show on landing page')
                        ->default(true),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->columns([
                Tables\Columns\TextColumn::make('name')->searchable()->sortable(),
                Tables\Columns\TextColumn::make('role')->toggleable(),
                Tables\Columns\TextColumn::make('quote')
                    ->limit(70)
                    ->tooltip(fn ($record) => $record->quote),
                Tables\Columns\IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                Tables\Columns\TextColumn::make('sort_order')->label('Order')->sortable(),
                Tables\Columns\TextColumn::make('updated_at')->since()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('is_active')->label('Active'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTestimonials::route('/'),
            'create' => Pages\CreateTestimonial::route('/create'),
            'edit'   => Pages\EditTestimonial::route('/{record}/edit'),
        ];
    }
}
