<?php

namespace App\Filament\Resources;

use App\Filament\Resources\FavoriteResource\Pages;
use App\Models\Favorite\Favorite;
use App\Services\Favorite\FavoriteService;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class FavoriteResource extends Resource
{
    protected static ?string $model = Favorite::class;

    protected static ?string $slug = 'favorites';

    protected static ?string $navigationIcon = 'heroicon-o-heart';

    protected static ?string $navigationGroup = 'User Management';

    protected static ?string $navigationLabel = 'Favorites';

    protected static ?int $navigationSort = 6;

    protected static ?string $modelLabel = 'Favorite';

    protected static ?string $pluralModelLabel = 'Favorites';

    protected static ?string $recordTitleAttribute = 'id';

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
        return __('app.resources.favorite.navigation_label');
    }

    /**
     * Get the translated model label
     */
    public static function getModelLabel(): string
    {
        return __('app.resources.favorite.label');
    }

    /**
     * Get the translated plural model label
     */
    public static function getPluralModelLabel(): string
    {
        return __('app.resources.favorite.plural_label');
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make(__('app.forms.favorite.information'))
                    ->description(__('app.forms.favorite.information_description'))
                    ->icon('heroicon-o-heart')
                    ->schema([
                        Forms\Components\Grid::make(2)
                            ->schema([]),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('app.columns.favorite.id'))
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('user.first_name')
                    ->label(__('app.columns.favorite.user_name'))
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->icon('heroicon-o-user')
                    ->openUrlInNewTab()
                    ->formatStateUsing(function ($record) {
                        if ($record->user) {
                            return $record->user->first_name . ' ' . $record->user->last_name;
                        }
                        return 'N/A';
                    }),

                Tables\Columns\TextColumn::make('user.email')
                    ->label(__('app.columns.favorite.user_email'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->icon('heroicon-o-envelope')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('product.sku')
                    ->label(__('app.columns.favorite.product_sku'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('medium')
                    ->color('primary')
                    ->icon('heroicon-o-tag')
                    ->url(fn(Favorite $record): string => route('filament.admin.resources.products.edit', $record->product_id))
                    ->openUrlInNewTab(),

                Tables\Columns\TextColumn::make('product.translations')
                    ->label(__('app.columns.favorite.product_name'))
                    ->getStateUsing(function (Favorite $record) {
                        return app(FavoriteService::class)->getTranslatedProductName($record);
                    })
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-o-cube')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('created_at')
                    ->label(__('app.columns.favorite.created_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-calendar'),

                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('app.columns.favorite.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->icon('heroicon-o-clock'),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('user')
                    ->label(__('app.filters.favorite.user'))
                    ->relationship('user', 'email')
                    ->searchable()
                    ->preload()
                    ->native(false),

                Tables\Filters\SelectFilter::make('product')
                    ->label(__('app.filters.favorite.product'))
                    ->relationship('product', 'sku')
                    ->searchable()
                    ->preload()
                    ->native(false),

                Tables\Filters\Filter::make('created_at')
                    ->label(__('app.filters.favorite.created_at'))
                    ->form([
                        Forms\Components\DatePicker::make('created_from')
                            ->label(__('app.filters.favorite.created_from')),
                        Forms\Components\DatePicker::make('created_until')
                            ->label(__('app.filters.favorite.created_until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn(Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make()
                        ->icon('heroicon-o-eye')
                        ->color('info'),
                ]),
            ])
            ->bulkActions([])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->searchable()
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
            'index' => Pages\ListFavorites::route('/'),
            'view' => Pages\ViewFavorite::route('/{record}'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return app(FavoriteService::class)->getQueryBuilder();
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) app(FavoriteService::class)->getFavoritesCount();
    }
}
