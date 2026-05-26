<?php

namespace App\Filament\Resources;

use App\Constants\RoleConstants;
use App\Filament\Resources\AlbumResource\Pages;
use App\Filament\Resources\AlbumResource\RelationManagers;
use App\Models\Album;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;

class AlbumResource extends Resource
{
    protected static ?string $model = Album::class;

    protected static ?string $navigationIcon  = 'heroicon-o-photo';
    protected static ?string $navigationGroup = 'Website Management';
    protected static ?string $navigationLabel = 'Photo Albums';
    protected static ?int    $navigationSort  = 20;

    public static function shouldRegisterNavigation(): bool
    {
        $u = auth()->user();
        return $u && in_array($u->role_id, [RoleConstants::ADMIN, RoleConstants::DIRECTOR ?? 11], true);
    }

    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Section::make('Album details')
                ->columns(2)
                ->schema([
                    Forms\Components\TextInput::make('title')
                        ->required()
                        ->maxLength(160)
                        ->reactive()
                        ->afterStateUpdated(fn ($state, callable $set, $get) =>
                            blank($get('slug')) ? $set('slug', Str::slug($state)) : null),

                    Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(180)
                        ->unique(ignoreRecord: true)
                        ->helperText('Used in the public URL: /gallery/your-slug'),

                    Forms\Components\Textarea::make('description')
                        ->rows(3)
                        ->maxLength(600)
                        ->columnSpanFull(),

                    Forms\Components\FileUpload::make('cover_image')
                        ->label('Cover image')
                        ->image()
                        ->directory('albums/covers')
                        ->maxSize(4096)
                        ->imageEditor()
                        ->imagePreviewHeight('120')
                        ->columnSpanFull()
                        ->helperText('Recommended 1600×1000. Falls back to the first photo in the album if blank.'),

                    Forms\Components\TextInput::make('order')
                        ->numeric()
                        ->default(0)
                        ->helperText('Lower numbers appear first.'),

                    Forms\Components\Select::make('status')
                        ->options([
                            'draft'     => 'Draft (hidden from public)',
                            'published' => 'Published (visible on /gallery)',
                        ])
                        ->default('draft')
                        ->required()
                        ->native(false),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->defaultSort('order')
            ->columns([
                Tables\Columns\ImageColumn::make('cover_image')
                    ->label('Cover')
                    ->disk('public')
                    ->square()
                    ->size(56),
                Tables\Columns\TextColumn::make('title')->searchable()->sortable()->weight('bold'),
                Tables\Columns\TextColumn::make('slug')
                    ->color('gray')
                    ->copyable()
                    ->toggleable(),
                Tables\Columns\TextColumn::make('photos_count')
                    ->label('Photos')
                    ->counts('photos')
                    ->badge(),
                Tables\Columns\BadgeColumn::make('status')
                    ->colors(['warning' => 'draft', 'success' => 'published']),
                Tables\Columns\TextColumn::make('order')->label('Sort')->sortable()->toggleable(),
                Tables\Columns\TextColumn::make('updated_at')->since()->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')->options([
                    'draft'     => 'Draft',
                    'published' => 'Published',
                ]),
            ])
            ->actions([
                Tables\Actions\Action::make('view_public')
                    ->label('Open public page')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('gray')
                    ->url(fn (Album $record) => url('/gallery/' . $record->slug))
                    ->openUrlInNewTab(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\PhotosRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListAlbums::route('/'),
            'create' => Pages\CreateAlbum::route('/create'),
            'edit'   => Pages\EditAlbum::route('/{record}/edit'),
        ];
    }
}
