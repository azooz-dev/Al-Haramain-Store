<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Traits\HasTranslations;
use Filament\Resources\Resource;
use App\Models\Category\Category;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\CategoryResource\Pages;
use App\Filament\Concerns\SendsFilamentNotifications;
use App\Services\Category\CategoryTranslationService;

class CategoryResource extends Resource
{
    use HasTranslations, SendsFilamentNotifications;
    protected static ?string $model = Category::class;

    protected static ?string $slug = 'categories';

    protected static ?string $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?string $navigationGroup = 'Store Management';

    protected static ?string $navigationLabel = 'Categories';

    protected static ?int $navigationSort = 1;

    protected static ?string $modelLabel = 'Category';

    protected static ?string $pluralModelLabel = 'Categories';

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
        return __('app.resources.category.navigation_label');
    }

    /**
     * Get the translated model label
     */
    public static function getModelLabel(): string
    {
        return __('app.resources.category.label');
    }

    /**
     * Get the translated plural model label
     */
    public static function getPluralModelLabel(): string
    {
        return __('app.resources.category.plural_label');
    }

    protected static ?string $recordTitleAttribute = 'slug';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.forms.category.information'))
                    ->description(__('app.forms.category.information_description'))
                    ->icon('heroicon-o-tag')
                    ->schema([
                        // Slug will be auto-generated from the name
                        Forms\Components\Hidden::make('slug'),

                        Forms\Components\Tabs::make(__('app.forms.category.translations'))
                            ->columnSpanFull()
                            ->tabs([
                                Forms\Components\Tabs\Tab::make(__('app.forms.category.english'))
                                    ->icon('heroicon-o-language')
                                    ->schema([
                                        Forms\Components\TextInput::make('en.name')
                                            ->label(__('app.forms.category.name_en'))
                                            ->maxLength(255)
                                            ->placeholder(__('app.forms.category.enter_name_en'))
                                            ->prefixIcon('heroicon-o-tag')
                                            ->required()
                                            ->rules(['required', 'string', 'max:255']),
                                        Forms\Components\Textarea::make('en.description')
                                            ->label(__('app.forms.category.description_en'))
                                            ->maxLength(65535)
                                            ->placeholder(__('app.forms.category.enter_description_en'))
                                            ->rows(4)
                                            ->rules(['nullable', 'string', 'max:65535']),
                                    ]),

                                Forms\Components\Tabs\Tab::make(__('app.forms.category.arabic'))
                                    ->icon('heroicon-o-globe-asia-australia')
                                    ->schema([
                                        Forms\Components\TextInput::make('ar.name')
                                            ->label(__('app.forms.category.name_ar'))
                                            ->maxLength(255)
                                            ->placeholder(__('app.forms.category.enter_name_ar'))
                                            ->extraAttributes(['dir' => 'rtl'])
                                            ->required()
                                            ->rules(['required', 'string', 'max:255']),
                                        Forms\Components\Textarea::make('ar.description')
                                            ->label(__('app.forms.category.description_ar'))
                                            ->maxLength(65535)
                                            ->placeholder(__('app.forms.category.enter_description_ar'))
                                            ->rows(4)
                                            ->extraAttributes(['dir' => 'rtl'])
                                            ->rules(['nullable', 'string', 'max:65535']),
                                    ]),
                            ]),
                    ])
                    ->columns(2)
                    ->collapsible()
                    ->collapsed(false),

                Forms\Components\Section::make(__('app.forms.category.image'))
                    ->description(__('app.forms.category.image_description'))
                    ->icon('heroicon-o-photo')
                    ->schema([
                        Forms\Components\FileUpload::make('image')
                            ->required()
                            ->image()
                            ->imageEditor()
                            ->disk('public')
                            ->directory('categories/images')
                            ->visibility('public')
                            ->maxSize(2048)
                            ->helperText(__('app.forms.category.upload_image_help'))
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
                    ->label(__('app.columns.category.image'))
                    ->circular()
                    ->size(50)
                    ->square()
                    ->disk('public')
                    ->visibility('public'),

                Tables\Columns\TextColumn::make('translated_name')
                    ->label(__('app.columns.category.translated_name'))
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
                    ->label(__('app.columns.category.slug'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->copyMessage(__('app.messages.category.slug_copied'))
                    ->copyMessageDuration(1500)
                    ->badge()
                    ->color('gray'),

                Tables\Columns\TextColumn::make('translated_description')
                    ->label(__('app.columns.category.translated_description'))
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
                    ->label(__('app.columns.category.product_count'))
                    ->state(function (Category $record) {
                        return $record->products->count();
                    })
                    ->badge()
                    ->color('success')
                    ->sortable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('app.columns.category.created_at'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-calendar'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('app.columns.category.updated_at'))
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
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading(__('app.messages.category.confirm_delete_heading'))
                        ->modalDescription(function (Category $record) use ($translationService) {
                            $translatedName = $translationService->getTranslatedName($record);
                            return __('app.messages.category.confirm_delete_description', ['name' => $translatedName]);
                        })
                        ->modalSubmitActionLabel(__('app.actions.delete'))
                        ->modalCancelActionLabel(__('app.actions.cancel'))
                        ->successNotification(fn($record) => self::buildSuccessNotification(
                            __('app.messages.category.deleted_success'),
                            __('app.messages.category.deleted_success_body', ['name' => $record->translations->where('local', app()->getLocale())->first()?->name
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
                        ->modalHeading(__('app.messages.category.confirm_delete_bulk_heading'))
                        ->modalDescription(__('app.messages.category.confirm_delete_bulk_description'))
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
            'index' => Pages\ListCategories::route('/'),
            'create' => Pages\CreateCategory::route('/create'),
            'view' => Pages\ViewCategory::route('/{record}'),
            'edit' => Pages\EditCategory::route('/{record}/edit'),
        ];
    }
}
