<?php

namespace App\Filament\Resources;

use Closure;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Offer\Offer;
use App\Models\Product\Product;
use App\Traits\HasTranslations;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\OfferResource\Pages;
use App\Services\Product\ProductTranslationService;
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
                                            ->maxLength(65535)
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
                                            ->maxLength(65535)
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
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('products')
                                    ->label(__('app.forms.offer.product'))
                                    ->relationship('products', 'id', fn($query) => $query->with('translations'))
                                    ->multiple()
                                    ->required()
                                    ->searchable()
                                    ->preload()
                                    ->native(false)
                                    ->optionsLimit(25)
                                    ->getOptionLabelFromRecordUsing(function ($record) {
                                        $service = app(ProductTranslationService::class);
                                        return $service->getTranslatedName($record);
                                    })
                                    ->getSearchResultsUsing(function (string $search) {
                                        $service = app(ProductTranslationService::class);
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
                                    ->getOptionLabelsUsing(function (array $values) {
                                        $service = app(ProductTranslationService::class);
                                        return Product::query()
                                            ->with('translations')
                                            ->whereIn('id', $values)
                                            ->get()
                                            ->mapWithKeys(function ($product) use ($service) {
                                                return [$product->id => $service->getTranslatedName($product)];
                                            })
                                            ->toArray();
                                    })
                                    ->columnSpan(3),

                                Forms\Components\FileUpload::make('image_path')
                                    ->label(__('app.forms.offer.image'))
                                    ->image()
                                    ->imageEditor()
                                    ->imageCropAspectRatio('16:9')
                                    ->imageResizeTargetWidth('1200')
                                    ->imageResizeTargetHeight('675')
                                    ->disk('public')
                                    ->directory('offers/images')
                                    ->visibility('public')
                                    ->maxSize(4096)
                                    ->acceptedFileTypes(['image/jpeg', 'image/png', 'image/webp'])
                                    ->columnSpanFull(1),
                            ]),
                    ])
                    ->columns(3)
                    ->collapsible()
                    ->collapsed(false),

                Forms\Components\Section::make(__('app.forms.offer.discount'))
                    ->description(__('app.forms.offer.discount_description'))
                    ->icon('heroicon-o-tag')
                    ->schema([
                        Forms\Components\Grid::make(3)
                            ->schema([
                                Forms\Components\Select::make('discount_type')
                                    ->label(__('app.forms.offer.discount_type'))
                                    ->options([
                                        Offer::FIXED => __('app.forms.offer.discount_type_fixed'),
                                        Offer::PERCENTAGE => __('app.forms.offer.discount_type_percentage'),
                                    ])
                                    ->required()
                                    ->native(false),

                                Forms\Components\TextInput::make('discount_amount')
                                    ->label(__('app.forms.offer.discount_amount'))
                                    ->numeric()
                                    ->required()
                                    ->minValue(function (callable $get) {
                                        return $get('discount_type') === Offer::PERCENTAGE ? 1 : 0;
                                    })
                                    ->maxValue(function (callable $get) {
                                        return $get('discount_type') === Offer::PERCENTAGE ? 100 : null;
                                    })
                                    ->step(function (callable $get) {
                                        return $get('discount_type') === Offer::PERCENTAGE ? 1 : 0.01;
                                    })
                                    ->validationMessages([
                                        'min' => function (callable $get) {
                                            return $get('discount_type') === Offer::PERCENTAGE
                                                ? __('app.forms.offer.discount_amount_percentage_validation')
                                                : __('app.forms.offer.discount_amount_fixed_validation');
                                        }
                                    ])
                                    ->suffix(function (callable $get) {
                                        return $get('discount_type') === Offer::PERCENTAGE ? '%' : '$';
                                    })
                                    ->prefixIcon('heroicon-o-currency-dollar')
                                    ->helperText(function (callable $get) {
                                        $discountType = $get('discount_type');
                                        if ($discountType === Offer::PERCENTAGE) {
                                            return __('app.forms.offer.discount_amount_percentage_help');
                                        }
                                        return __('app.forms.offer.discount_amount_fixed_help');
                                    })
                                    ->columnSpan(1)
                                    ->rules([
                                        'required',
                                        'numeric',
                                        'min:0'
                                    ])
                                    ->live()
                                    ->reactive()
                                    ->afterStateUpdated(function (callable $set, callable $get, $state) {
                                        // Validate based on discount type
                                        $discountType = $get('discount_type');
                                        if ($discountType === Offer::PERCENTAGE && $state > 100) {
                                            $set('discount_amount', 100);
                                        }
                                    }),

                                Forms\Components\Select::make('status')
                                    ->label(__('app.forms.offer.status'))
                                    ->options([
                                        Offer::ACTIVE => __('app.status.active'),
                                        Offer::INACTIVE => __('app.status.inactive'),
                                    ])
                                    ->required()
                                    ->native(false)
                                    ->columnSpan(1),
                            ]),
                    ])
                    ->columns(3)
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
                    ->state(function (Offer $record) {
                        $locale = app()->getLocale();
                        return $record->translations->firstWhere('locale', $locale)?->name
                            ?? $record->translations->first()?->name
                            ?? ('#' . $record->id);
                    }),

                Tables\Columns\TextColumn::make('products_list')
                    ->label(__('app.columns.offer.product'))
                    ->state(function (Offer $record) {
                        $service = app(ProductTranslationService::class);
                        return $record->products->map(fn($p) => $service->getTranslatedName($p))->join(', ');
                    })
                    ->limit(40)
                    ->tooltip(fn($state) => $state)
                    ->badge()
                    ->icon('heroicon-o-cube'),

                Tables\Columns\TextColumn::make('discount')
                    ->label(__('app.columns.offer.discount'))
                    ->state(function (Offer $record) {
                        $amount = number_format((float)$record->discount_amount, 2);
                        return $record->discount_type === Offer::PERCENTAGE ? ($amount . '%') : ('$' . $amount);
                    })
                    ->badge()
                    ->color(fn(Offer $record): string => $record->discount_type === Offer::PERCENTAGE ? 'info' : 'success')
                    ->icon('heroicon-o-tag')
                    ->sortable(false),

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
                                return 'upcoming';
                            }
                            if ($now->between($record->start_date, $record->end_date)) {
                                return 'running';
                            }
                            return 'expired';
                        }
                        return 'unknown';
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

                Tables\Filters\SelectFilter::make('discount_type')
                    ->label(__('app.filters.discount_type'))
                    ->options([
                        Offer::FIXED => __('app.forms.offer.discount_type_fixed'),
                        Offer::PERCENTAGE => __('app.forms.offer.discount_type_percentage'),
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

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['translations', 'products.translations']);
    }
}
