<?php

namespace App\Filament\Resources;

use App\Constants\RoleConstants;
use App\Filament\Resources\CpdResourceResource\Pages;
use App\Models\CpdResource;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class CpdResourceResource extends Resource
{
    protected static ?string $model = CpdResource::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'CPD Resources';

    protected static ?string $navigationGroup = 'CPD Management';

    protected static ?int $navigationSort = 3;

    public static function shouldRegisterNavigation(): bool
    {
        return in_array(auth()->user()?->role_id, array_merge([RoleConstants::ADMIN], RoleConstants::teaching())) ?? false;
    }

    public static function canViewAny(): bool
    {
        return in_array(auth()->user()?->role_id, array_merge([RoleConstants::ADMIN], RoleConstants::teaching())) ?? false;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Resource Details')
                    ->schema([
                        Forms\Components\Select::make('user_id')
                            ->label('Shared By')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Forms\Components\TextInput::make('title')
                            ->required()
                            ->maxLength(255),

                        Forms\Components\Textarea::make('description')
                            ->rows(3)
                            ->columnSpanFull(),

                        Forms\Components\TextInput::make('subject')
                            ->maxLength(100),

                        Forms\Components\TextInput::make('grade')
                            ->maxLength(50),

                        Forms\Components\Select::make('type')
                            ->options([
                                'document' => 'Document',
                                'presentation' => 'Presentation',
                                'video' => 'Video',
                                'link' => 'Link',
                                'template' => 'Template',
                                'lesson_plan' => 'Lesson Plan',
                                'worksheet' => 'Worksheet',
                                'other' => 'Other',
                            ])
                            ->required(),

                        Forms\Components\TextInput::make('external_url')
                            ->label('External URL')
                            ->url()
                            ->maxLength(500),
                    ])
                    ->columns(2),

                Forms\Components\Section::make('File Upload')
                    ->schema([
                        Forms\Components\FileUpload::make('file_path')
                            ->label('File')
                            ->directory('cpd-resources')
                            ->maxSize(10240),

                        Forms\Components\TextInput::make('file_name')
                            ->label('File Name')
                            ->maxLength(255)
                            ->helperText('Original file name for display'),
                    ])
                    ->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->limit(40),

                Tables\Columns\TextColumn::make('user.name')
                    ->label('Shared By')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('subject')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('grade')
                    ->sortable()
                    ->toggleable(),

                Tables\Columns\TextColumn::make('type')
                    ->sortable(),

                Tables\Columns\TextColumn::make('download_count')
                    ->label('Downloads')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Date')
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('type')
                    ->options([
                        'document' => 'Document',
                        'presentation' => 'Presentation',
                        'video' => 'Video',
                        'link' => 'Link',
                        'template' => 'Template',
                        'lesson_plan' => 'Lesson Plan',
                        'worksheet' => 'Worksheet',
                        'other' => 'Other',
                    ]),

                Tables\Filters\SelectFilter::make('subject')
                    ->options(fn () => CpdResource::query()
                        ->whereNotNull('subject')
                        ->distinct()
                        ->pluck('subject', 'subject')
                        ->toArray()
                    ),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make(),
                ]),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListCpdResources::route('/'),
            'create' => Pages\CreateCpdResource::route('/create'),
            'edit' => Pages\EditCpdResource::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::getEloquentQuery()
            ->with(['user:id,name']);
    }
}
