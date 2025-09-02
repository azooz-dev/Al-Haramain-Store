<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use App\Models\Product\Product;
use Illuminate\Support\Facades\DB;
use Filament\Tables\Columns\TextColumn;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Services\Product\ProductTranslationService;
use Filament\Tables\Columns\TextColumn\TextColumnSize;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class TopProductsWidget extends BaseWidget
{
    use HasWidgetShield;
    protected static ?string $heading = null;
    protected static ?int $sort = 4;
    protected static ?string $maxHeight = '350px';
    protected int | string | array $columnSpan = [
        'md' => 2,
        'xl' => 1,
    ];

    public ?string $filter = 'last_30_days';

    protected function getTableFilters(): array
    {
        return [
            'today' => __('app.widgets.products.filters.today'),
            'last_7_days' => __('app.widgets.products.filters.last_7_days'),
            'last_30_days' => __('app.widgets.products.filters.last_30_days'),
            'this_year' => __('app.widgets.products.filters.this_year'),
        ];
    }

    public function getHeading(): ?string
    {
        return __('app.widgets.products.top_selling_heading');
    }

    public function table(Table $table): Table
    {
        $period = $this->getPeriodDates();
        $translationService = app(ProductTranslationService::class);

        return $table
            ->heading(__('app.widgets.products.top_selling_heading'))
            ->query(
                Product::query()
                    ->select([
                        'products.*',
                        DB::raw('SUM(order_items.quantity) as total_sold'),
                        DB::raw('SUM(order_items.quantity * order_items.total_price) as total_revenue'),
                        DB::raw('COUNT(DISTINCT orders.id) as order_count')
                    ])
                    ->join('order_items', 'products.id', '=', 'order_items.product_id')
                    ->join('orders', 'order_items.order_id', '=', 'orders.id')
                    ->where('orders.status', '!=', 'cancelled')
                    ->where('orders.status', '!=', 'refunded')
                    ->whereBetween('orders.created_at', [$period['start'], $period['end']])
                    ->groupBy('products.id')
                    ->orderByDesc('total_sold')
                    ->limit(10)
                    ->with(['translations', 'colors', 'variants'])
            )
            ->columns([
                Tables\Columns\TextColumn::make('rank')
                    ->label('#')
                    ->state(fn($livewire, $rowLoop) => $rowLoop->iteration)
                    ->alignCenter()
                    ->size(Tables\Columns\TextColumn\TextColumnSize::Small),

                Tables\Columns\TextColumn::make('translated_name')
                    ->label(__('app.widgets.products.product_name'))
                    ->state(fn(Product $record) => $translationService->getTranslatedName($record))
                    ->searchable(false)
                    ->sortable(false)
                    ->weight('medium')
                    ->wrap()
                    ->limit(30),

                Tables\Columns\TextColumn::make('sku')
                    ->label(__('app.widgets.products.sku'))
                    ->badge()
                    ->color('gray')
                    ->size(Tables\Columns\TextColumn\TextColumnSize::Small),

                Tables\Columns\TextColumn::make('total_sold')
                    ->label(__('app.widgets.products.sold'))
                    ->badge()
                    ->color('success')
                    ->alignCenter()
                    ->sortable(false),

                Tables\Columns\TextColumn::make('total_revenue')
                    ->label(__('app.widgets.products.revenue'))
                    ->money('USD')
                    ->color('primary')
                    ->weight('bold')
                    ->alignEnd()
                    ->sortable(false),

                Tables\Columns\TextColumn::make('current_stock')
                    ->label(__('app.widgets.products.stock'))
                    ->state(fn(Product $record) => $record->quantity)
                    ->badge()
                    ->color(
                        fn(Product $record): string =>
                        $record->quantity > 50 ? 'success' : ($record->quantity > 10 ? 'warning' : ($record->quantity > 0 ? 'danger' : 'gray'))
                    )
                    ->alignCenter()
                    ->sortable(false),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->icon('heroicon-m-eye')
                    ->url(fn(Product $record): string => route('filament.admin.resources.products.view', $record))
                    ->openUrlInNewTab(),
            ])
            ->emptyStateHeading(__('app.resources.product.plural_label'))
            ->emptyStateDescription(__('app.widgets.products.no_products_found'))
            ->paginated(false)
            ->defaultSort('total_sold', 'desc');
    }

    private function getPeriodDates(): array
    {
        return match ($this->filter) {
            'today' => [
                'start' => now()->startOfDay(),
                'end' => now()->endOfDay(),
            ],
            'last_7_days' => [
                'start' => now()->subDays(6)->startOfDay(),
                'end' => now()->endOfDay(),
            ],
            'last_30_days' => [
                'start' => now()->subDays(29)->startOfDay(),
                'end' => now()->endOfDay(),
            ],
            'this_year' => [
                'start' => now()->startOfYear(),
                'end' => now()->endOfYear(),
            ],
            default => [
                'start' => now()->subDays(29)->startOfDay(),
                'end' => now()->endOfDay(),
            ],
        };
    }
}
