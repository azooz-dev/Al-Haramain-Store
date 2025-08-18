<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CategoryResource\Pages;
use App\Models\Category\Category;
use App\Services\Category\CategoryTranslationService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $slug = 'categories';

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Store Management';

    protected static ?string $navigationLabel = 'Categories';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Category';

    protected static ?string $pluralModelLabel = 'Categories';

    protected static ?string $recordTitleAttribute = 'slug';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Category Information')
                    ->description('Enter the basic details for this category')
                    ->icon('heroicon-o-tag')
                    ->schema([
                        // Slug will be auto-generated from the name
                        Forms\Components\Hidden::make('slug'),

                        Forms\Components\Tabs::make('Translations')
                            ->columnSpanFull()
                            ->tabs([
                                Forms\Components\Tabs\Tab::make('English')
                                    ->icon('heroicon-o-language')
                                    ->schema([
                                        Forms\Components\TextInput::make('en.name')
                                            ->label('Name (EN)')
                                            ->maxLength(255)
                                            ->placeholder('Enter category name (English)')
                                            ->prefixIcon('heroicon-o-tag')
                                            ->required()
                                            ->rules(['required', 'string', 'max:255']),
                                        Forms\Components\Textarea::make('en.description')
                                            ->label('Description (EN)')
                                            ->maxLength(65535)
                                            ->placeholder('Describe this category in English...')
                                            ->rows(4)
                                            ->rules(['nullable', 'string', 'max:65535']),
                                    ]),
                                Forms\Components\Tabs\Tab::make('Arabic')
                                    ->icon('heroicon-o-globe-asia-australia')
                                    ->schema([
                                        Forms\Components\TextInput::make('ar.name')
                                            ->label('Name (AR)')
                                            ->maxLength(255)
                                            ->placeholder('أدخل اسم التصنيف')
                                            ->extraAttributes(['dir' => 'rtl'])
                                            ->required()
                                            ->rules(['required', 'string', 'max:255']),
                                        Forms\Components\Textarea::make('ar.description')
                                            ->label('Description (AR)')
                                            ->maxLength(65535)
                                            ->placeholder('صِف هذا التصنيف باللغة العربية...')
                                            ->rows(4)
                                            ->extraAttributes(['dir' => 'rtl'])
                                            ->rules(['nullable', 'string', 'max:65535']),
                                    ]),
                            ]),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false),

                Forms\Components\Section::make('Category Image')
                    ->description('Upload a representative image for this category')
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->required()
                            ->image()
                            ->imageEditor()
                            ->imageCropAspectRatio('16:9')
                            ->imageResizeTargetWidth('800')
                            ->imageResizeTargetHeight('450')
                            ->disk('local')
                            ->directory('categories/images')
                            ->visibility('private')
                            ->maxSize(2048)
                            ->helperText('Upload a high-quality image (max 2MB). Recommended size: 800x450px')
                            ->columnSpanFull()
                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                            ->previewable()
                            ->downloadable()
                            ->openable()
                    ])
                    ->collapsible()
                    ->collapsed(false),
            ]);
    }

    public static function table(Table $table): Table
    {
        $translationService = app(CategoryTranslationService::class);

        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image')
                    ->label('Image')
                    ->circular()
                    ->size(50)
                    ->square()
                    ->disk('local')
                    ->visibility('private'),

                Tables\Columns\TextColumn::make('translated_name')
                    ->label('Category Name')
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->whereHas('translations', function (Builder $q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    })
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->icon('heroicon-o-tag')
                    ->state(function (Category $record) use ($translationService) {
                        return $translationService->getTranslatedName($record);
                    }),

                Tables\Columns\TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage('Slug copied!')
                    ->copyMessageDuration(1500)
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('translated_description')
                    ->label('Description')
                    ->limit(50)
                    ->wrap()
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->whereHas('translations', function (Builder $q) use ($search) {
                            $q->where('description', 'like', "%{$search}%");
                        });
                    })
                    ->sortable()
                    ->state(function (Category $record) use ($translationService) {
                        return $translationService->getTranslatedDescription($record);
                    })
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('product_count')
                    ->label('Products')
                    ->counts('products')
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-calendar'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-clock'),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->form([
                        Forms\Components\DatePicker::make('created_from'),
                        Forms\Components\DatePicker::make('created_until')
                    ])
                    ->query(
                        function (Builder $query, array $data): Builder {
                            return $query
                                ->when(
                                    $data['created_from'],
                                    fn(Builder $query, $data): Builder => $query->whereDate('created_at', '>=', $data),
                                )
                                ->when(
                                    $data['created_until'],
                                    fn(Builder $query, $data): Builder => $query->whereDate('created_at', '<=', $data)
                                );
                        }
                    )
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye')
                        ->url(fn($record) => static::getUrl('view', ['record' => $record])),
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil'),
                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger'),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'view' => Pages\ViewCategory::route('/{record}'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
