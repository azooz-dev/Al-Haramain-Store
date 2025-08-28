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

    protected static ?string $recordTitleAttribute = 'product.sku';



    public function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('product.translated_name')
                    ->label(__('app.columns.order_item.product_name'))
                    ->searchable(query: function (Builder $query, string $search) {
                        $query->whereHas('product.translations', function (Builder $q) use ($search) {
                            $q->where('name', 'like', "%{$search}%");
                        });
                    })
                    ->sortable()
                    ->weight('bold')
                    ->color('primary')
                    ->icon('heroicon-o-cube')
                    ->wrap()
                    ->state(fn(OrderItem $record) => $record->product?->translations->where('locale', app()->getLocale())->first()?->name ?? __('app.fields.no_name')),

                Tables\Columns\TextColumn::make('product.sku')
                    ->label(__('app.columns.order_item.sku'))
                    ->description(fn(OrderItem $record): ?string => $record->product?->translations->first()?->name ?? __('app.fields.no_name'))
                    ->wrap()
                    ->limit(40),

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
                    ->state(fn(OrderItem $record): float => $record->line_total)
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
