<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Modules\Offer\Entities\Offer\Offer;
use Modules\Catalog\Entities\Product\Product;
use App\Traits\HasTranslations;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\OfferResource\Pages;
use Modules\Offer\Contracts\OfferServiceInterface;
use Modules\Catalog\Services\Product\ProductTranslationService;
use App\Filament\Concerns\SendsFilamentNotifications;

class OfferResource extends Resource
{
    use HasTranslations, SendsFilamentNotifications;
    protected static ?string $model = Offer::class;

    protected static ?string $slug = 'offers';

    protected static ?string $navigationIcon = 'heroicon-o-percent-badge';

    protected static ?string $navigationGroup = 'Store Management';

    protected static ?string $navigationLabel = 'Offers';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Offer';

    protected static ?string $pluralModelLabel = 'Offers';

    protected static ?string $recordTitleAttribute = 'id';

    /**
     * Get navigation badge with offer count
     */
    public static function getNavigationBadge(): ?string
    {
        return app(OfferServiceInterface::class)->getOffersCount();
    }

    public static function getNavigationGroup(): ?string
    {
        return __('app.navigation.store_management');
    }

    public static function getNavigationLabel(): string
    {
        return __('app.resources.offer.navigation_label');
    }

    public static function getModelLabel(): string
    {
        return __('app.resources.offer.label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('app.resources.offer.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([

                Forms\Components\Section::make(__('app.forms.offer.translations'))
                    ->description(__('app.forms.offer.translations_description'))
                    ->icon('heroicon-o-language')
                    ->schema([
                        Forms\Components\Tabs::make('translations')
                            ->tabs([
                                Forms\Components\Tabs\Tab::make(__('app.forms.offer.english'))
                                    ->icon('heroicon-o-flag')
                                    ->schema([
                                        Forms\Components\TextInput::make('en.name')
                                            ->label(__('app.forms.offer.name_en'))
                                            ->required()
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('en.description')
                                            ->label(__('app.forms.offer.description_en'))
                                            ->rows(4)
                                            ->maxLength(255)
                                            ->columnSpanFull(),
                                    ]),
                                Forms\Components\Tabs\Tab::make(__('app.forms.offer.arabic'))
                                    ->icon('heroicon-o-globe-asia-australia')
                                    ->schema([
                                        Forms\Components\TextInput::make('ar.name')
                                            ->label(__('app.forms.offer.name_ar'))
                                            ->required()
                                            ->maxLength(255)
                                            ->extraAttributes(['dir' => 'rtl'])
                                            ->columnSpanFull(),
                                        Forms\Components\Textarea::make('ar.description')
                                            ->label(__('app.forms.offer.description_ar'))
                                            ->rows(4)
                                            ->maxLength(255)
                                            ->extraAttributes(['dir' => 'rtl'])
                                            ->columnSpanFull(),
                                    ]),
                            ])
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Forms\Components\Section::make(__('app.forms.offer.basic_information'))
                    ->description(__('app.forms.offer.basic_information_description'))
                    ->icon('heroicon-o-percent-badge')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\FileUpload::make('image_path')
                                    ->label(__('app.forms.offer.image'))
                                    ->image()
                                    ->imageEditor()
                                    ->disk('public')
                                    ->directory('offers/images')
                                    ->visibility('public')
                                    ->maxSize(4096)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->columnSpan(1),

                                Forms\Components\Select::make('status')
                                    ->label(__('app.forms.offer.status'))
                                    ->options([
                                        Offer::ACTIVE => __('app.status.active'),
                                        Offer::INACTIVE => __('app.status.inactive'),
                                    ])
                                    ->default(Offer::ACTIVE)
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false),

                Forms\Components\Section::make(__('app.forms.offer.products_selection'))
                    ->description(__('app.forms.offer.products_selection_description'))
                    ->icon('heroicon-o-shopping-cart')
                    ->schema([
                        Forms\Components\Repeater::make('offer_products')
                            ->label(__('app.forms.offer.offer_products'))
                            ->relationship('offerProducts')
                            ->schema([
                                Forms\Components\Grid::make(4)
                                    ->schema([
                                        Forms\Components\Select::make('product_id')
                                            ->label(__('app.forms.offer.product'))
                                            ->relationship('product', 'id', fn($query) => $query->with('translations'))
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->optionsLimit(25)
                                            ->getOptionLabelFromRecordUsing(function ($record) {
                                                $service = app(\Modules\Catalog\Contracts\ProductTranslationServiceInterface::class);
                                                return $service->getTranslatedName($record);
                                            })
                                            ->getSearchResultsUsing(function (string $search) {
                                                $service = app(\Modules\Catalog\Contracts\ProductTranslationServiceInterface::class);
                                                return Product::query()
                                                    ->with('translations')
                                                    ->whereHas('translations', function (Builder $q) use ($search) {
                                                        $q->where('name', 'like', "%{$search}%");
                                                    })
                                                    ->limit(50)
                                                    ->get()
                                                    ->mapWithKeys(function ($product) use ($service) {
                                                        return [$product->id => $service->getTranslatedName($product)];
                                                    })
                                                    ->toArray();
                                            })
                                            ->live()
                                            ->afterStateUpdated(function (callable $set, $state) {
                                                $set('product_variant_id', null);
                                                $set('product_color_id', null);
                                                $set('variant_price', 0);
                                            })
                                            ->columnSpan(1),

                                        Forms\Components\Select::make('product_color_id')
                                            ->label(__('app.forms.offer.product_color'))
                                            ->relationship('productColor', 'id', function ($query, callable $get) {
                                                $productId = $get('product_id');
                                                return $query->where('product_id', $productId);
                                            })
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->allowHtml()
                                            ->getOptionLabelFromRecordUsing(function ($record) {
                                                $code = strtoupper($record->color_code);
                                                $hex = ltrim($record->color_code, '#');
                                                $bg = '#' . $hex;
                                                $circle = '<span style="display:inline-block;width:14px;height:14px;border-radius:9999px;background-color:' . e($bg) . ';border:1px solid #ccc;margin-inline-end:6px;vertical-align:middle;"></span>';
                                                return $circle . '<span style="vertical-align:middle;">' . e($code) . '</span>';
                                            })
                                            ->live()
                                            ->afterStateUpdated(function (callable $set, $state) {
                                                // Reset variant selection when color changes
                                                $set('product_variant_id', null);
                                                $set('variant_price', 0);
                                            })
                                            ->columnSpan(1),

                                        Forms\Components\Select::make('product_variant_id')
                                            ->label(__('app.forms.offer.product_variant'))
                                            ->relationship('productVariant', 'id', function ($query, callable $get) {
                                                $productId = $get('product_id');
                                                $colorId = $get('product_color_id');

                                                $query = $query->where('product_id', $productId);

                                                // Filter variants by selected color
                                                if ($colorId) {
                                                    $query = $query->where('color_id', $colorId);
                                                }

                                                return $query->with('color');
                                            })
                                            ->required()
                                            ->searchable()
                                            ->preload()
                                            ->native(false)
                                            ->getOptionLabelFromRecordUsing(function ($record) {
                                                return $record->display_name ?? "{$record->size} - {$record->color?->color_code}";
                                            })
                                            ->live()
                                            ->afterStateUpdated(function (callable $set, $state) {
                                                if ($state) {
                                                    $variant = \Modules\Catalog\Entities\Product\ProductVariant::find($state);
                                                    if ($variant) {
                                                        $set('variant_price', $variant->effective_price);
                                                    }
                                                }
                                            })
                                            ->columnSpan(1),

                                        Forms\Components\Grid::make(2)
                                            ->schema([
                                                Forms\Components\TextInput::make('variant_price')
                                                    ->label(__('app.forms.offer.variant_price'))
                                                    ->numeric()
                                                    ->prefix('$')
                                                    ->required()
                                                    ->step(0.01)
                                                    ->minValue(0)
                                                    ->columnSpan(1),

                                                Forms\Components\TextInput::make('quantity')
                                                    ->label(__('app.forms.offer.quantity'))
                                                    ->numeric()
                                                    ->required()
                                                    ->default(1)
                                                    ->minValue(1)
                                                    ->columnSpan(1),
                                            ])
                                            ->columnSpan(1),
                                    ]),
                            ])
                            ->addActionLabel(__('app.forms.offer.add_product'))
                            ->defaultItems(1)
                            ->minItems(1)
                            ->collapsible()
                            ->itemLabel(
                                fn(array $state): ?string =>
                                $state['product_id'] ?
                                    (function () use ($state) {
                                        $product = app(\Modules\Catalog\Services\Product\ProductService::class)->findProductById($state['product_id']);
                                        return $product->translations->where('local', app()->getLocale())->first()?->name ?? 'Product';
                                    })()
                                    : null
                            )
                            ->addAction(
                                fn($action) => $action
                                    ->button()
                                    ->icon('heroicon-o-plus')
                                    ->color('success')
                            )
                            ->deleteAction(
                                fn($action) => $action
                                    ->requiresConfirmation()
                                    ->modalHeading(__('app.forms.offer.remove_product_confirm'))
                                    ->modalDescription(__('app.forms.offer.remove_product_description'))
                            )
                            ->columnSpanFull(),
                    ])
                    ->collapsible()
                    ->collapsed(false),

                Forms\Components\Section::make(__('app.forms.offer.discount'))
                    ->description(__('app.forms.offer.discount_description'))
                    ->icon('heroicon-o-tag')
                    ->schema([
                        Forms\Components\Grid::make(1)
                            ->schema([
                                Forms\Components\TextInput::make('offer_price')
                                    ->label(__('app.forms.offer.offer_price'))
                                    ->numeric()
                                    ->prefix('$')
                                    ->required()
                                    ->step(0.01)
                                    ->minValue(0)
                                    ->helperText(__('app.forms.offer.offer_price_help'))
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->columns(1)
                    ->collapsible(),

                Forms\Components\Section::make(__('app.forms.offer.schedule'))
                    ->description(__('app.forms.offer.schedule_description'))
                    ->icon('heroicon-o-calendar')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([
                                Forms\Components\DateTimePicker::make('start_date')
                                    ->label(__('app.forms.offer.start_date'))
                                    ->required()
                                    ->minDate(now())
                                    ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                        $startDate = $get('start_date');
                                        $endDate = $get('end_date');
                                        if ($startDate && $endDate && $startDate > $endDate) {
                                            $set('end_date', null);
                                        }
                                    })
                                    ->native(false)
                                    ->seconds(false),
                                Forms\Components\DateTimePicker::make('end_date')
                                    ->label(__('app.forms.offer.end_date'))
                                    ->required()
                                    ->minDate(function (callable $get) {
                                        $startDate = $get('start_date');
                                        return $startDate ? $startDate : now();
                                    })
                                    ->native(false)
                                    ->seconds(false)
                                    ->after('start_date'),
                            ]),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        $offerService = app(OfferServiceInterface::class);

        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('image_path')
                    ->label(__('app.columns.offer.image'))
                    ->circular()
                    ->size(50)
                    ->square()
                    ->disk('public'),

                Tables\Columns\TextColumn::make('translated_name')
                    ->label(__('app.columns.offer.name'))
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->whereHas('translations', function (Builder $q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    })
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->icon('heroicon-o-tag')
                    ->state(function (Offer $record) use ($offerService) {
                        return $offerService->getTranslatedName($record) ?: ('#' . $record->id);
                    }),

                Tables\Columns\TextColumn::make('products_count')
                    ->label(__('app.columns.offer.products_count'))
                    ->state(function (Offer $record) use ($offerService) {
                        return $offerService->getProductsCount($record);
                    })
                    ->badge()
                    ->color('info')
                    ->icon('heroicon-o-cube'),

                Tables\Columns\TextColumn::make('products_total_price')
                    ->label(__('app.columns.offer.total_price'))
                    ->money('USD')
                    ->sortable()
                    ->icon('heroicon-o-currency-dollar'),

                Tables\Columns\TextColumn::make('offer_price')
                    ->label(__('app.columns.offer.offer_price'))
                    ->money('USD')
                    ->sortable()
                    ->icon('heroicon-o-tag'),

                Tables\Columns\TextColumn::make('discount_amount')
                    ->label(__('app.columns.offer.discount'))
                    ->state(function (Offer $record) use ($offerService) {
                        return $offerService->getDiscountAmount($record);
                    })
                    ->color('success')
                    ->icon('heroicon-o-arrow-trending-down'),

                Tables\Columns\TextColumn::make('start_date')
                    ->label(__('app.columns.offer.start_date'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                Tables\Columns\TextColumn::make('end_date')
                    ->label(__('app.columns.offer.end_date'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->icon('heroicon-o-calendar'),

                Tables\Columns\TextColumn::make('status')
                    ->label(__('app.columns.offer.status'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        Offer::ACTIVE => 'success',
                        Offer::INACTIVE => 'gray',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        Offer::ACTIVE => 'heroicon-o-check-badge',
                        Offer::INACTIVE => 'heroicon-o-pause-circle',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        Offer::ACTIVE => __('app.status.active'),
                        Offer::INACTIVE => __('app.status.inactive'),
                        default => $state,
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('is_running')
                    ->label(__('app.columns.offer.runtime_status'))
                    ->badge()
                    ->state(function (Offer $record) {
                        $now = now();
                        if ($record->start_date && $record->end_date) {
                            if ($now->lt($record->start_date)) {
                                return __("app.columns.offer.upcoming");
                            }
                            if ($now->between($record->start_date, $record->end_date)) {
                                return __("app.columns.offer.running");
                            }
                            return __("app.columns.offer.expired");
                        }
                        return __("app.columns.offer.unknown");
                    })
                    ->colors([
                        'info' => 'upcoming',
                        'success' => 'running',
                        'danger' => 'expired',
                        'gray' => 'unknown',
                    ])
                    ->icons([
                        'heroicon-o-clock' => 'upcoming',
                        'heroicon-o-play-circle' => 'running',
                        'heroicon-o-x-circle' => 'expired',
                        'heroicon-o-question-mark-circle' => 'unknown',
                    ])
                    ->sortable(false)
                    ->toggleable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->label(__('app.filters.offer_status'))
                    ->options([
                        Offer::ACTIVE => __('app.status.active'),
                        Offer::INACTIVE => __('app.status.inactive'),
                    ])
                    ->multiple()
                    ->preload(),

                Tables\Filters\Filter::make('schedule')
                    ->label(__('app.filters.schedule'))
                    ->form([
                        Forms\Components\DatePicker::make('from')->label(__('app.filters.start_from')),
                        Forms\Components\DatePicker::make('until')->label(__('app.filters.end_until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when($data['from'] ?? null, fn(Builder $q, $date) => $q->whereDate('start_date', '>=', $date))
                            ->when($data['until'] ?? null, fn(Builder $q, $date) => $q->whereDate('end_date', '<=', $date));
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\EditAction::make()
                        ->icon('heroicon-o-pencil')
                        ->color('warning'),
                    Tables\Actions\DeleteAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading(__('app.messages.offer.confirm_delete_heading'))
                        ->modalDescription(__('app.messages.offer.confirm_delete_description'))
                        ->modalSubmitActionLabel(__('app.actions.delete'))
                        ->successNotification(fn($record) => self::buildSuccessNotification(
                            __('app.messages.offer.deleted_success'),
                            __('app.messages.offer.deleted_success_body', ['name' => $record->name])
                        )),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->icon('heroicon-o-trash')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading(__('app.messages.offer.confirm_delete_bulk_heading'))
                        ->modalDescription(__('app.messages.offer.confirm_delete_bulk_description'))
                        ->modalSubmitActionLabel(__('app.actions.delete'))
                        ->successNotification(fn($records) => self::buildSuccessNotification(
                            __('app.messages.offer.deleted_success_bulk'),
                            __('app.messages.offer.deleted_success_body_bulk')
                        )),
                ]),
            ])
            ->defaultSort('start_date', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('30s')
            ->extremePaginationLinks()
            ->deferLoading();
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
            'index' => Pages\ListOffers::route('/'),
            'create' => Pages\CreateOffer::route('/create'),
            'edit' => Pages\EditOffer::route('/{record}/edit'),
        ];
    }

    /**
     * Get the Eloquent query for the resource
     * Uses service layer for query building
     */
    public static function getEloquentQuery(): Builder
    {
        return app(OfferServiceInterface::class)->getQueryBuilder();
    }
}
