<?php

namespace App\Filament\Widgets;

use App\Filament\Concerns\ResolvesServices;
use Filament\Tables;
use Filament\Tables\Table;
use Modules\Catalog\Entities\Product\Product;
use Filament\Widgets\TableWidget as BaseWidget;
use Modules\Analytics\Services\ProductAnalyticsService;
use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;

class TopProductsWidget extends BaseWidget
{
    use HasWidgetShield, ResolvesServices;

    protected static ?string $heading = null;
    protected static ?int $sort = 4;
    protected static ?string $maxHeight = '350px';
    protected static bool $isLazy = true;
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
        $service = $this->resolveService(ProductAnalyticsService::class);
        $period = $this->getPeriodDates();
        $topProducts = $service->getTopSellingProducts(
            $period['start'],
            $period['end'],
            3
        );

        return $table
            ->heading(__('app.widgets.products.top_selling_heading'))
            ->query(function () use ($topProducts) {
                $productIds = $topProducts->pluck('id')->toArray();

                if (empty($productIds)) {
                    return Product::query()->whereRaw('1 = 0');
                }

                // Preserve the order from the service (already sorted by total_sold desc)
                // Since productIds are integers from the database, it's safe to use them directly
                $idsString = implode(',', array_map('intval', $productIds));

                return Product::query()
                    ->whereIn('id', $productIds)
                    ->orderByRaw("FIELD(id, {$idsString})")
                    ->with(['translations', 'colors', 'variants']);
            })
            ->columns([
                Tables\Columns\TextColumn::make('rank')
                    ->label('#')
                    ->state(function ($livewire, $rowLoop, Product $record) use ($topProducts) {
                        // Find the rank based on total_sold from the service result
                        $product = $topProducts->firstWhere('id', $record->id);
                        if (!$product) {
                            return '-';
                        }
                        $rank = $topProducts->search(function ($item) use ($product) {
                            return $item->id === $product->id;
                        });
                        return $rank !== false ? $rank + 1 : '-';
                    })
                    ->alignCenter()
                    ->size(Tables\Columns\TextColumn\TextColumnSize::Small),

                Tables\Columns\TextColumn::make('translated_name')
                    ->label(__('app.widgets.products.product_name'))
                    ->state(function (Product $record) use ($service) {
                        return $service->getTranslatedProductName($record);
                    })
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
                    ->state(function (Product $record) use ($topProducts) {
                        $product = $topProducts->firstWhere('id', $record->id);
                        return $product->total_sold ?? 0;
                    })
                    ->badge()
                    ->color('success')
                    ->alignCenter()
                    ->sortable(false),

                Tables\Columns\TextColumn::make('total_revenue')
                    ->label(__('app.widgets.products.revenue'))
                    ->state(function (Product $record) use ($topProducts) {
                        $product = $topProducts->firstWhere('id', $record->id);
                        return $product->total_revenue ?? 0;
                    })
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
            ->paginated(false);
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
