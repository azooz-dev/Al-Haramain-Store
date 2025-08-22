<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Traits\HasTranslations;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Models\Product\Product;
use App\Services\Product\ProductTranslationService;

class ProductResource extends Resource
{
    use HasTranslations;

    protected static ?string $model = Product::class;

    protected static ?string $slug = 'products';

    protected static ?string $navigationIcon = 'heroicon-o-cube';

    protected static ?string $navigationGroup = 'Store Management';

    protected static ?string $navigationLabel = 'Products';

    protected static ?int $navigationSort = 2;

    protected static ?string $modelLabel = 'Product';

    protected static ?string $pluralModelLabel = 'Products';

    /**
     * Get the translated navigation group
     */
    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation.store_management');
    }

    /**
     * Get the translated navigation label
     */
    public static function getNavigationLabel(): string
    {
        return __('app.resources.product.navigation_label');
    }

    /**
     * Get the translated model label
     */
    public static function getModelLabel(): string
    {
        return __('app.resources.product.label');
    }

    /**
     * Get the translated plural model label
     */
    public static function getPluralModelLabel(): string
    {
        return __('app.resources.product.plural_label');
    }

    protected static ?string $recordTitleAttribute = 'slug';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                // Basic Information Section
                Forms\Components\Section::make(__('app.forms.product.basic_information'))
                    ->description(__('app.forms.product.basic_information_description'))
                    ->icon('heroicon-o-cube')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\TextInput::make('sku')
                                    ->label(__('app.forms.product.sku'))
                                    ->required()
                                    ->unique(ignoreRecord: true)
                                    ->maxLength(50)
                                    ->placeholder(__('app.forms.product.enter_sku'))
                                    ->prefixIcon('heroicon-o-tag')
                                    ->columnSpan(1),

                                Forms\Components\TextInput::make('quantity')
                                    ->label(__('app.forms.product.quantity'))
                                    ->numeric()
                                    ->required()
                                    ->minValue(0)
                                    ->placeholder(__('app.forms.product.enter_quantity'))
                                    ->prefixIcon('heroicon-o-archive-box')
                                    ->columnSpan(1),

                                Forms\Components\Hidden::make('slug')
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->collapsed(false),

                // Product Translations Section
                Forms\Components\Section::make(__('app.forms.product.translations'))
                    ->description(__('app.forms.product.translations_description'))
                    ->icon('heroicon-o-language')
                    ->schema([
                        Forms\Components\Tabs::make('translations')
                            ->tabs([
                                // English Tab
                                Forms\Components\Tabs\Tab::make(__('app.forms.product.english'))
                                    ->icon('heroicon-o-flag')
                                    ->schema([
                                        Forms\Components\TextInput::make('en.name')
                                            ->label(__('app.forms.product.name_en'))
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder(__('app.forms.product.enter_name_en'))
                                            ->prefixIcon('heroicon-o-tag')
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('en.description')
                                            ->label(__('app.forms.product.description_en'))
                                            ->required()
                                            ->maxLength(65535)
                                            ->placeholder(__('app.forms.product.enter_description_en'))
                                            ->rows(6)
                                            ->columnSpanFull(),
                                    ]),

                                // Arabic Tab
                                Forms\Components\Tabs\Tab::make(__('app.forms.product.arabic'))
                                    ->icon('heroicon-o-globe-asia-australia')
                                    ->schema([
                                        Forms\Components\TextInput::make('ar.name')
                                            ->label(__('app.forms.product.name_ar'))
                                            ->required()
                                            ->maxLength(255)
                                            ->placeholder(__('app.forms.product.enter_name_ar'))
                                            ->prefixIcon('heroicon-o-tag')
                                            ->extraAttributes(['dir' => 'rtl'])
                                            ->columnSpanFull(),

                                        Forms\Components\Textarea::make('ar.description')
                                            ->label(__('app.forms.product.description_ar'))
                                            ->required()
                                            ->maxLength(65535)
                                            ->placeholder(__('app.forms.product.enter_description_ar'))
                                            ->rows(6)
                                            ->extraAttributes(['dir' => 'rtl'])
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                // Product Variants Section
                Forms\Components\Section::make(__('app.forms.product.variants'))
                    ->description(__('app.forms.product.variants_description'))
                    ->icon('heroicon-o-squares-2x2')
                    ->schema([
                        Forms\Components\Repeater::make('variants')
                            ->relationship()
                            ->schema([
                                Forms\Components\Grid::make(3)
                                    ->schema([
                                        Forms\Components\TextInput::make('size')
                                            ->label(__('app.forms.product.variant.size'))
                                            ->required()
                                            ->maxLength(50)
                                            ->placeholder(__('app.forms.product.enter_size'))
                                            ->prefixIcon('heroicon-o-arrows-pointing-out'),

                                        Forms\Components\TextInput::make('color')
                                            ->label(__('app.forms.product.variant.color'))
                                            ->required()
                                            ->maxLength(50)
                                            ->placeholder(__('app.forms.product.enter_color'))
                                            ->prefixIcon('heroicon-o-swatch'),

                                        Forms\Components\TextInput::make('quantity')
                                            ->label(__('app.forms.product.variant.quantity'))
                                            ->numeric()
                                            ->required()
                                            ->minValue(0)
                                            ->placeholder(__('app.forms.product.enter_variant_quantity'))
                                            ->prefixIcon('heroicon-o-archive-box'),
                                    ]),

                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\TextInput::make('price')
                                            ->label(__('app.forms.product.variant.price'))
                                            ->numeric()
                                            ->required()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->prefix('$')
                                            ->placeholder(__('app.forms.product.enter_price'))
                                            ->prefixIcon('heroicon-o-currency-dollar'),

                                        Forms\Components\TextInput::make('amount_discount_price')
                                            ->label(__('app.forms.product.variant.discount_price'))
                                            ->numeric()
                                            ->minValue(0)
                                            ->step(0.01)
                                            ->prefix('$')
                                            ->placeholder(__('app.forms.product.enter_discount_price'))
                                            ->prefixIcon('heroicon-o-tag')
                                            ->helperText(__('app.forms.product.variant.discount_price_help')),
                                    ]),
                            ])
                            ->columns(1)
                            ->defaultItems(0)
                            ->reorderable(false)
                            ->collapsible()
                            ->itemLabel(
                                fn(array $state): ?string =>
                                isset($state['size'], $state['color'])
                                    ? "{$state['size']} - {$state['color']}"
                                    : null
                            )
                            ->addActionLabel(__('app.forms.product.add_variant'))
                            ->cloneable()
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                // Product Images Section
                Forms\Components\Section::make(__('app.forms.product.images'))
                    ->description(__('app.forms.product.images_description'))
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\Repeater::make('images')
                            ->relationship()
                            ->schema([
                                Forms\Components\Grid::make(2)
                                    ->schema([
                                        Forms\Components\FileUpload::make('image_url')
                                            ->label(__('app.forms.product.image'))
                                            ->image()
                                            ->imageEditor()
                                            ->imageCropAspectRatio('4:3')
                                            ->imageResizeTargetWidth('800')
                                            ->imageResizeTargetHeight('600')
                                            ->disk('public')
                                            ->directory('products/images')
                                            ->visibility('public')
                                            ->maxSize(2048)
                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                            ->previewable()
                                            ->downloadable()
                                            ->openable()
                                            ->required()
                                            ->columnSpan(1),

                                        Forms\Components\TextInput::make('alt_text')
                                            ->label(__('app.forms.product.alt_text'))
                                            ->maxLength(255)
                                            ->placeholder(__('app.forms.product.enter_alt_text'))
                                            ->helperText(__('app.forms.product.alt_text_help'))
                                            ->columnSpan(1),
                                    ]),
                            ])
                            ->columns(1)
                            ->defaultItems(0)
                            ->reorderable(true)
                            ->collapsible()
                            ->itemLabel(
                                fn(array $state): ?string =>
                                isset($state['alt_text']) ? $state['alt_text'] : __('app.forms.product.image')
                            )
                            ->addActionLabel(__('app.forms.product.add_image'))
                            ->cloneable()
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                // Categories Section
                Forms\Components\Section::make(__('app.forms.product.categories'))
                    ->description(__('app.forms.product.categories_description'))
                    ->icon('heroicon-o-rectangle-stack')
                    ->schema([
                        Forms\Components\Select::make('categories')
                            ->label(__('app.forms.product.select_categories'))
                            ->relationship('categories', 'slug', function ($query) {
                                return $query->with('translations');
                            })
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->createOptionForm([
                                Forms\Components\Tabs::make('category_translations')
                                    ->tabs([
                                        Forms\Components\Tabs\Tab::make(__('app.forms.category.english'))
                                            ->icon('heroicon-o-flag')
                                            ->schema([
                                                Forms\Components\TextInput::make('en.name')
                                                    ->label(__('app.forms.category.name_en'))
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder(__('app.forms.category.enter_name_en')),
                                                Forms\Components\Textarea::make('en.description')
                                                    ->label(__('app.forms.category.description_en'))
                                                    ->maxLength(65535)
                                                    ->placeholder(__('app.forms.category.enter_description_en'))
                                                    ->rows(3),
                                            ]),
                                        Forms\Components\Tabs\Tab::make(__('app.forms.category.arabic'))
                                            ->icon('heroicon-o-globe-asia-australia')
                                            ->schema([
                                                Forms\Components\TextInput::make('ar.name')
                                                    ->label(__('app.forms.category.name_ar'))
                                                    ->required()
                                                    ->maxLength(255)
                                                    ->placeholder(__('app.forms.category.enter_name_ar'))
                                                    ->extraAttributes(['dir' => 'rtl']),
                                                Forms\Components\Textarea::make('ar.description')
                                                    ->label(__('app.forms.category.description_ar'))
                                                    ->maxLength(65535)
                                                    ->placeholder(__('app.forms.category.enter_description_ar'))
                                                    ->rows(3)
                                                    ->extraAttributes(['dir' => 'rtl']),
                                            ]),
                                    ]),
                                Forms\Components\FileUpload::make('image')
                                    ->label(__('app.forms.category.image'))
                                    ->image()
                                    ->imageEditor()
                                    ->imageCropAspectRatio('16:9')
                                    ->imageResizeTargetWidth('800')
                                    ->imageResizeTargetHeight('450')
                                    ->disk('local')
                                    ->directory('products/images')
                                    ->visibility('private')
                                    ->maxSize(2048)
                                    ->helperText(__('app.forms.category.upload_image_help'))
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->previewable()
                                    ->downloadable()
                                    ->openable(),
                            ])
                            ->getOptionLabelFromRecordUsing(
                                fn($record) =>
                                $record->translations->where('local', app()->getLocale())->first()?->name
                                    ?? $record->translations->first()?->name
                                    ?? $record->slug
                            )
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(false),


            ]);
    }

    public static function table(Table $table): Table
    {
        $translationService = app(ProductTranslationService::class);

        return $table
            ->columns([
                Tables\Columns\TextColumn::make('sku')
                    ->label(__('app.columns.product.sku'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->badge()
                    ->color('gray')
                    ->icon('heroicon-o-tag'),

                Tables\Columns\TextColumn::make('translated_name')
                    ->label(__('app.columns.product.name'))
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->whereHas('translations', function (Builder $q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    })
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->icon('heroicon-o-cube')
                    ->state(function (Product $record) use ($translationService) {
                        return $translationService->getTranslatedName($record);
                    }),

                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('app.columns.product.quantity'))
                    ->numeric()
                    ->sortable()
                    ->badge()
                    ->color(
                        fn(Product $record): string =>
                        $record->quantity > 10 ? 'success' : ($record->quantity > 0 ? 'warning' : 'danger')
                    )
                    ->icon('heroicon-o-archive-box'),

                Tables\Columns\TextColumn::make('variants_count')
                    ->label(__('app.columns.product.variants_count'))
                    ->counts('variants')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->icon('heroicon-o-squares-2x2'),

                Tables\Columns\TextColumn::make('images_count')
                    ->label(__('app.columns.product.images_count'))
                    ->counts('images')
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->icon('heroicon-o-photo'),

                Tables\Columns\TextColumn::make('categories_count')
                    ->label(__('app.columns.product.categories_count'))
                    ->counts('categories')
                    ->badge()
                    ->color('warning')
                    ->sortable()
                    ->icon('heroicon-o-rectangle-stack'),



                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('app.columns.product.created_at'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-calendar'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('app.columns.product.updated_at'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-clock'),
            ])
            ->filters([
                Tables\Filters\Filter::make('created_at')
                    ->label(__('app.filters.created_at'))
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label(__('app.filters.created_from')),
                        Forms\Components\DatePicker::make('created_until')
                            ->label(__('app.filters.created_until'))
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $data): Builder => $query->whereDate('created_at', '>=', $data),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $data): Builder => $query->whereDate('created_at', '<=', $data)
                            );
                    }),
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
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading(__('app.messages.product.confirm_delete_heading'))
                        ->modalDescription(function (Product $record) {
                            $translatedName = $record->translations->where('local', app()->getLocale())->first()?->name
                                ?? $record->translations->first()?->name
                                ?? $record->slug;
                            return __('app.messages.product.confirm_delete_description', ['name' => $translatedName]);
                        })
                        ->modalSubmitActionLabel(__('app.actions.delete'))
                        ->modalCancelActionLabel(__('app.actions.cancel')),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading(__('app.messages.product.confirm_delete_bulk_heading'))
                        ->modalDescription(__('app.messages.product.confirm_delete_bulk_description'))
                        ->modalSubmitActionLabel(__('app.actions.delete'))
                        ->modalCancelActionLabel(__('app.actions.cancel')),
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
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'view' => Pages\ViewProduct::route('/{record}'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
