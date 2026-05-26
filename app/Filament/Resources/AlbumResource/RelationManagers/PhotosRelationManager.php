<?php

namespace App\Filament\Resources\AlbumResource\RelationManagers;

use App\Models\Photo;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class PhotosRelationManager extends RelationManager
{
    protected static string $relationship = 'photos';

    protected static ?string $title = 'Photos in this album';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\FileUpload::make('image_path')
                ->label('Image')
                ->image()
                ->directory('albums/photos')
                ->maxSize(6144)
                ->imageEditor()
                ->required()
                ->columnSpanFull(),

            Forms\Components\TextInput::make('caption')
                ->label('Caption (shown on hover)')
                ->maxLength(200)
                ->columnSpanFull(),

            Forms\Components\TextInput::make('alt_text')
                ->label('Alt text (for accessibility)')
                ->maxLength(200)
                ->columnSpanFull(),

            Forms\Components\Grid::make(3)->schema([
                Forms\Components\TextInput::make('title')
                    ->label('Title (optional)')
                    ->maxLength(120),
                Forms\Components\TextInput::make('order')
                    ->numeric()
                    ->default(0),
                Forms\Components\Toggle::make('featured')
                    ->label('Featured (highlights in grid)')
                    ->default(false),
            ]),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('caption')
            ->reorderable('order')
            ->defaultSort('order')
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label('')
                    ->disk('public')
                    ->square()
                    ->size(72),
                Tables\Columns\TextColumn::make('caption')
                    ->limit(60)
                    ->placeholder('—'),
                Tables\Columns\IconColumn::make('featured')->boolean()->label('★'),
                Tables\Columns\TextColumn::make('order')->label('#')->sortable(),
                Tables\Columns\TextColumn::make('created_at')->since()->toggleable(),
            ])
            ->filters([
                Tables\Filters\TernaryFilter::make('featured'),
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make()
                    ->label('Add photo')
                    ->modalHeading('Add a photo to this album'),

                Tables\Actions\Action::make('bulkUpload')
                    ->label('Bulk upload')
                    ->icon('heroicon-o-cloud-arrow-up')
                    ->color('primary')
                    ->modalHeading('Upload multiple photos to this album')
                    ->modalSubmitActionLabel('Upload all')
                    ->form([
                        Forms\Components\FileUpload::make('files')
                            ->label('Drag photos here or browse')
                            ->multiple()
                            ->image()
                            ->directory('albums/photos')
                            ->maxSize(6144)
                            ->reorderable()
                            ->panelLayout('grid')
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $files = (array) ($data['files'] ?? []);
                        $album = $this->getOwnerRecord();
                        $startOrder = (int) ($album->photos()->max('order') ?? 0) + 1;
                        foreach ($files as $i => $path) {
                            Photo::create([
                                'album_id'   => $album->id,
                                'image_path' => $path,
                                'order'      => $startOrder + $i,
                                'featured'   => false,
                            ]);
                        }
                    }),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\BulkAction::make('feature')
                        ->label('Mark as featured')
                        ->icon('heroicon-o-star')
                        ->action(fn ($records) => $records->each->update(['featured' => true]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\BulkAction::make('unfeature')
                        ->label('Unmark featured')
                        ->action(fn ($records) => $records->each->update(['featured' => false]))
                        ->deselectRecordsAfterCompletion(),
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
