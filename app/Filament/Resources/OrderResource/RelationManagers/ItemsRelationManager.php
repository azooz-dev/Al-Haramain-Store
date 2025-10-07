<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Order\OrderItem;

use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\RelationManagers\RelationManager;


class ItemsRelationManager extends RelationManager
{
    protected static string $relationship = 'items';

    protected static ?string $recordTitleAttribute = 'orderable_name';



    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('orderable_type')
                    ->label(__('app.columns.order_item.item_type'))
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'App\\Models\\Product\\Product' => 'primary',
                        'App\\Models\\Offer\\Offer' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'App\\Models\\Product\\Product' => 'heroicon-o-cube',
                        'App\\Models\\Offer\\Offer' => 'heroicon-o-gift',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'App\\Models\\Product\\Product' => __('app.item_types.product'),
                        'App\\Models\\Offer\\Offer' => __('app.item_types.offer'),
                        default => __('app.item_types.unknown'),
                    })
                    ->sortable(),

                Tables\Columns\TextColumn::make('orderable_name')
                    ->label(__('app.columns.order_item.item_name'))
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->whereHasMorph('orderable', ['App\\Models\\Product\\Product', 'App\\Models\\Offer\\Offer'], function (Builder $q) use ($search) {
                            $q->whereHas('translations', function (Builder $translationQuery) use ($search) {
                                $translationQuery->where('name', 'like', "%{$search}%");
                            });
                        });
                    })
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->icon('heroicon-o-tag')
                    ->wrap()
                    ->state(fn(?OrderItem $record): string => $record?->orderable_name ?? __('app.fields.no_name')),

                Tables\Columns\TextColumn::make('orderable.sku')
                    ->label(__('app.columns.order_item.sku'))
                    ->description(fn(?OrderItem $record): ?string => $record?->orderable_name)
                    ->wrap()
                    ->limit(40)
                    ->visible(fn(?OrderItem $record): bool => $record?->orderable_type === 'App\\Models\\Product\\Product'),

                Tables\Columns\TextColumn::make('quantity')
                    ->label(__('app.fields.qty'))
                    ->badge()
                    ->alignCenter()
                    ->sortable(),

                Tables\Columns\TextColumn::make('total_price')
                    ->label(__('app.columns.order_item.unit_price'))
                    ->money('USD')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('amount_discount_price')
                    ->label(__('app.columns.order_item.discount'))
                    ->money('USD')
                    ->alignEnd()
                    ->color('danger')
                    ->toggleable(),

                Tables\Columns\TextColumn::make('line_total')
                    ->label(__('app.columns.order_item.line_total'))
                    ->state(fn(?OrderItem $record): float => $record?->line_total ?? 0)
                    ->formatStateUsing(fn($state) => '$' . number_format((float) $state, 2))
                    ->weight('bold')
                    ->color('success')
                    ->alignEnd(),
            ])
            ->paginated(false)
            ->striped()
            ->headerActions([])
            ->actions([])
            ->bulkActions([])
            ->defaultSort('id')
            ->emptyStateHeading(__('app.empty_states.no_items'))
            ->emptyStateDescription(__('app.empty_states.no_items_description'))
            ->contentGrid(['md' => 1]);
    }
}
