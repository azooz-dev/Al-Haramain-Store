<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use App\Models\Product\Product;
use App\Traits\HasTranslations;
use Filament\Resources\Resource;
use Filament\Support\Colors\Color;
use App\Rules\VariantQuantityMaxRule;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\ProductResource\Pages;
use App\Services\Product\ProductTranslationService;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Concerns\SendsFilamentNotifications;
use App\Filament\Resources\ProductResource\RelationManagers;

class ProductResource extends Resource
{
    use HasTranslations, SendsFilamentNotifications;

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

    protected static ?string $recordTitleAttribute = 'sku';

    public static function form(Form $form): Form
    {
        // get per-variant rules (now contains a Rule object, not closures)
        $perVariantRules = static::getVariantValidationRules()['variants.*.quantity'] ?? ['required', 'numeric', 'min:0'];

        return $form->schema([
            Forms\Components\Wizard::make([
                Forms\Components\Wizard\Step::make(__('app.forms.product.basic_information'))
                    ->schema([
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
                                            ->label(__('app.forms.product.total_stock_quantity'))
                                            ->numeric()
                                            ->required()
                                            ->minValue(0)
                                            ->placeholder(__('app.forms.product.enter_total_stock'))
                                            ->prefixIcon('heroicon-o-archive-box')
                                            ->helperText(__('app.forms.product.total_stock_help'))
                                            ->columnSpan(1),

                                        Forms\Components\Hidden::make('slug')
                                            ->columnSpan(1),
                                    ]),
                            ])
                            ->columns(3)
                            ->collapsible()
                            ->collapsed(false),
                    ]),


                Forms\Components\Wizard\Step::make(__('app.forms.product.translations'))
                    ->schema([
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

                                                Forms\Components\RichEditor::make('en.description')
                                                    ->label(__('app.forms.product.description_en'))
                                                    ->required()
                                                    ->placeholder(__('app.forms.product.enter_description_en'))
                                                    ->toolbarButtons([
                                                        'bold',
                                                        'italic',
                                                        'underline',
                                                        'bulletList',
                                                        'orderedList',
                                                        'link',
                                                    ])
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

                                                Forms\Components\RichEditor::make('ar.description')
                                                    ->label(__('app.forms.product.description_ar'))
                                                    ->required()
                                                    ->placeholder(__('app.forms.product.enter_description_ar'))
                                                    ->toolbarButtons([
                                                        'bold',
                                                        'italic',
                                                        'underline',
                                                        'bulletList',
                                                        'orderedList',
                                                        'link',
                                                    ])
                                                    ->extraAttributes(['dir' => 'rtl'])
                                                    ->columnSpanFull(),
                                            ]),
                                    ])
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->collapsed(false),
                    ]),


                Forms\Components\Wizard\Step::make(__('app.forms.product.colors_and_images'))
                    ->schema([
                        Forms\Components\Section::make(__('app.forms.product.colors_and_images'))
                            ->description(__('app.forms.product.colors_and_images_description'))
                            ->icon('heroicon-o-swatch')
                            ->schema([
                                Forms\Components\Repeater::make('colors')
                                    ->label('')
                                    ->relationship('colors')
                                    ->schema([
                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\ColorPicker::make('color_code')
                                                    ->label(__('app.forms.product.color.color_code'))
                                                    ->required()
                                                    ->hex()
                                                    ->columnSpan(1),
                                            ]),

                                        // Images and Variants side-by-side for this color
                                        Forms\Components\Grid::make(1)
                                            ->schema([
                                                // Images for this color
                                                Forms\Components\Repeater::make('images')
                                                    ->relationship('images') // ربط بعلاقة ProductColor->images
                                                    ->label(__('app.forms.product.color.images'))
                                                    ->schema([
                                                        Forms\Components\FileUpload::make('image_url')
                                                            ->label(__('app.forms.product.color.image'))
                                                            ->image()
                                                            ->imageEditor()
                                                            ->disk('public')
                                                            ->directory('products/images')
                                                            ->visibility('public')
                                                            ->maxSize(2048)
                                                            ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
                                                            ->downloadable(true)
                                                            ->columnSpan(1),

                                                    ])
                                                    ->defaultItems(1)
                                                    ->collapsible()
                                                    ->itemLabel(fn(array $state) => $state['alt_text'] ?? __('app.forms.product.color.image'))
                                                    ->addActionLabel(__('app.forms.product.color.add_image'))
                                                    ->reorderableWithButtons()
                                                    ->cloneable()
                                                    ->columnSpan(1),

                                                // Variants for this color (nested)
                                                Forms\Components\Repeater::make('variants')
                                                    ->relationship('variants')
                                                    ->label(__('app.forms.product.variants'))
                                                    ->schema([
                                                        Forms\Components\Grid::make(2)
                                                            ->schema([
                                                                Forms\Components\TextInput::make('size')
                                                                    ->label(__('app.forms.product.variant.size'))
                                                                    ->required()
                                                                    ->placeholder(__('app.forms.product.enter_size'))
                                                                    ->maxLength(50)
                                                                    ->columnSpan(1),

                                                                Forms\Components\TextInput::make('quantity')
                                                                    ->label(__('app.forms.product.variant.available_quantity'))
                                                                    ->numeric()
                                                                    ->required()
                                                                    ->minValue(0)
                                                                    ->rules($perVariantRules)->placeholder(__('app.forms.product.enter_variant_available_quantity'))
                                                                    ->helperText(__('app.forms.product.variant.quantity_help'))
                                                                    ->columnSpan(1),

                                                                Forms\Components\TextInput::make('price')
                                                                    ->label(__('app.forms.product.variant.price'))
                                                                    ->numeric()
                                                                    ->required()
                                                                    ->minValue(0)
                                                                    ->step(0.01)
                                                                    ->prefix('$')
                                                                    ->placeholder(__('app.forms.product.enter_price'))
                                                                    ->prefixIcon('heroicon-o-currency-dollar')
                                                                    ->columnSpan(1),

                                                                Forms\Components\TextInput::make('amount_discount_price')
                                                                    ->label(__('app.forms.product.variant.discount_price'))
                                                                    ->numeric()
                                                                    ->minValue(0)
                                                                    ->step(0.01)
                                                                    ->prefix('$')
                                                                    ->placeholder(__('app.forms.product.enter_discount_price'))
                                                                    ->prefixIcon('heroicon-o-tag')
                                                                    ->helperText(__('app.forms.product.variant.discount_price_help'))
                                                                    ->columnSpan(1),
                                                            ]),
                                                    ])
                                                    ->defaultItems(0)
                                                    ->collapsible()
                                                    ->itemLabel(fn(array $state) => ($state['size'] ?? __('app.forms.product.variant.new_variant')))
                                                    ->addActionLabel(__('app.forms.product.add_variant'))
                                                    ->reorderableWithButtons()
                                                    ->cloneable()
                                                    ->default([])
                                                    ->columnSpan(1),
                                            ]),
                                    ])
                                    ->defaultItems(0)
                                    ->collapsible()
                                    ->itemLabel(
                                        fn(array $state): ?string =>
                                        isset($state['color_code'])
                                            ? __('app.forms.product.color.color') . ': ' . strtoupper($state['color_code'])
                                            : ($state['name'] ?? __('app.forms.product.color.new_color'))
                                    )
                                    ->addActionLabel(__('app.forms.product.color.add_color'))
                                    ->reorderableWithButtons()
                                    ->cloneable()
                                    ->columnSpanFull(),
                            ])
                            ->collapsible()
                            ->collapsed(false),
                    ]),

                Forms\Components\Wizard\Step::make(__('app.forms.product.categories'))
                    ->schema([
                        Forms\Components\Section::make(__('app.forms.product.categories'))
                            ->description(__('app.forms.product.categories_description'))
                            ->icon('heroicon-o-rectangle-stack')
                            ->schema([
                                Forms\Components\Select::make('categories')
                                    ->label(__('app.forms.product.select_categories'))
                                    ->relationship('categories', 'slug', function ($query) {
                                        return $query->with('translations');
                                    })
                                    ->default([])
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
                                            ->disk('public')
                                            ->directory('categories/images')
                                            ->visibility('public')
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
                    ]),
            ])->columnSpanFull(),
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
                    ->icon('heroicon-o-tag')
                    ->weight('medium'),

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
                    ->wrap()
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
                        $record->quantity > 50 ? 'success' : ($record->quantity > 10 ? 'warning' : ($record->quantity > 0 ? 'danger' : 'gray'))
                    )
                    ->icon('heroicon-o-archive-box')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('colors_count')
                    ->label(__('app.columns.product.colors_count'))
                    ->counts('colors')
                    ->badge()
                    ->color('info')
                    ->sortable()
                    ->icon('heroicon-o-swatch')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('variants_count')
                    ->label(__('app.columns.product.variants_count'))
                    ->counts('variants')
                    ->badge()
                    ->color('success')
                    ->sortable()
                    ->icon('heroicon-o-squares-2x2')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('images_count')
                    ->label(__('app.columns.product.images_count'))
                    ->state(function (Product $record) {
                        return $record->colors->sum(fn($color) => $color->images()->count());
                    })
                    ->badge()
                    ->color('purple')
                    ->sortable(false)
                    ->icon('heroicon-o-photo')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('categories_count')
                    ->label(__('app.columns.product.categories_count'))
                    ->counts('categories')
                    ->badge()
                    ->color('warning')
                    ->sortable()
                    ->icon('heroicon-o-rectangle-stack')
                    ->alignCenter(),

                Tables\Columns\TextColumn::make('price_range')
                    ->label(__('app.columns.product.price_range'))
                    ->state(function (Product $record) {
                        $variants = $record->variants;

                        if ($variants->isEmpty()) {
                            return __('app.columns.product.no_variants');
                        }

                        $minPrice = $variants->min('price');
                        $maxPrice = $variants->max('price');

                        if ($minPrice === $maxPrice) {
                            return '$' . number_format($minPrice, 2);
                        }

                        return '$' . number_format($minPrice, 2) . ' - $' . number_format($maxPrice, 2);
                    })
                    ->badge()
                    ->color('emerald')
                    ->icon('heroicon-o-currency-dollar')
                    ->sortable(false),

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

                Tables\Filters\Filter::make('stock_status')
                    ->label(__('app.filters.stock_status'))
                    ->form([
                        Forms\Components\Select::make('stock')
                            ->label(__('app.filters.stock_level'))
                            ->options([
                                'in_stock' => __('app.filters.in_stock'),
                                'low_stock' => __('app.filters.low_stock'),
                                'out_of_stock' => __('app.filters.out_of_stock'),
                            ])
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when($data['stock'], function ($query, $stock) {
                            match ($stock) {
                                'in_stock' => $query->where('quantity', '>', 10),
                                'low_stock' => $query->whereBetween('quantity', [1, 10]),
                                'out_of_stock' => $query->where('quantity', 0),
                            };
                        });
                    }),

                Tables\Filters\Filter::make('has_variants')
                    ->label(__('app.filters.has_variants'))
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->has('variants')),

                Tables\Filters\Filter::make('has_colors')
                    ->label(__('app.filters.has_colors'))
                    ->toggle()
                    ->query(fn(Builder $query): Builder => $query->has('colors')),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye')
                        ->color('info'),

                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil')
                        ->color('warning'),

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
                        ->modalCancelActionLabel(__('app.actions.cancel'))
                        ->successNotification(fn($record) => self::buildSuccessNotification(
                            __('app.messages.product.deleted_success'),
                            __('app.messages.product.deleted_success_body', ['name' => $record->translations->where('local', app()->getLocale())->first()?->name
                                ?? $record->translations->first()?->name
                                ?? $record->slug])
                        )),
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
            ->paginated([10, 25, 50, 100])
            ->poll('30s') // Auto-refresh every 30 seconds
            ->extremePaginationLinks()
            ->deferLoading();
    }

    public static function getRelations(): array
    {
        return [
            // You might want to add relation managers here
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ])
            ->with(['translations', 'colors.images', 'variants', 'categories.translations']);
    }

    /**
     * Custom validation rules for product variants
     */
    public static function getVariantValidationRules(): array
    {
        return [
            'variants.*.quantity' => [
                'required',
                'numeric',
                'min:0',
                new VariantQuantityMaxRule(),
            ],

            // aggregate rule for variants array (keep the closure here for the server-side check)
            'variants' => [
                function ($attribute, $value, $fail) {
                    $totalFromVariants = 0;
                    if (!is_array($value)) {
                        $fail(__('app.validation.invalid_variants_format'));
                        return;
                    }
                    foreach ($value as $variant) {
                        $qty = isset($variant['quantity']) ? (int) $variant['quantity'] : 0;
                        $totalFromVariants += $qty;
                    }
                    $mainQuantity = (int) request()->input('quantity', 0);

                    if ($totalFromVariants !== $mainQuantity) {
                        $fail(__('app.validation.variants_total_must_equal_product_quantity', [
                            'variants_total' => $totalFromVariants,
                            'product_total'  => $mainQuantity,
                        ]));
                    }
                },
            ],
        ];
    }
}
