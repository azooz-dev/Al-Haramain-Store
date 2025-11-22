<?php

namespace App\Filament\Resources\FavoriteResource\Pages;

use App\Filament\Resources\FavoriteResource;
use App\Services\Favorite\FavoriteService;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListFavorites extends ListRecords
{
    protected static string $resource = FavoriteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->icon('heroicon-o-plus')
                ->label(__('app.actions.create_favorite'))
                ->color('primary'),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // You can add widgets here for statistics
        ];
    }

    public function getTabs(): array
    {
        $favoriteService = app(FavoriteService::class);

        return [
            'all' => Tab::make(__('app.tabs.all'))
                ->icon('heroicon-o-heart')
                ->badge($favoriteService->getFavoritesCount()),

            'recent' => Tab::make(__('app.tabs.recent'))
                ->icon('heroicon-o-clock')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('created_at', '>=', now()->subDays(7)))
                ->badge($favoriteService->getRecentFavoritesCount(7)),
        ];
    }
}
