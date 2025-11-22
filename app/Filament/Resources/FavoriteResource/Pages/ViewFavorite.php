<?php

namespace App\Filament\Resources\FavoriteResource\Pages;

use Filament\Infolists;
use Filament\Infolists\Infolist;
use App\Models\Favorite\Favorite;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\FavoriteResource;
use App\Services\Favorite\FavoriteService;

class ViewFavorite extends ViewRecord
{
  protected static string $resource = FavoriteResource::class;

  protected function getHeaderActions(): array
  {
    return [];
  }

  public function infolist(Infolist $infolist): Infolist
  {
    return $infolist
      ->schema([
        Infolists\Components\Section::make(__('app.forms.favorite.information'))
          ->description(__('app.forms.favorite.information_description'))
          ->icon('heroicon-o-heart')
          ->schema([
            Infolists\Components\Grid::make(2)
              ->schema([
                Infolists\Components\TextEntry::make('user.first_name')
                  ->label(__('app.forms.favorite.user'))
                  ->formatStateUsing(function ($record) {
                    if ($record->user) {
                      return $record->user->first_name . ' ' . $record->user->last_name;
                    }
                    return 'N/A';
                  })
                  ->icon('heroicon-o-user')

                  ->openUrlInNewTab(),

                Infolists\Components\TextEntry::make('user.email')
                  ->label(__('app.columns.favorite.user_email'))
                  ->icon('heroicon-o-envelope')
                  ->copyable(),

                Infolists\Components\TextEntry::make('product.sku')
                  ->label(__('app.columns.favorite.product_sku'))
                  ->icon('heroicon-o-tag')
                  ->color('primary')
                  ->url(fn(): string => route('filament.admin.resources.products.edit', $this->record->product_id))
                  ->openUrlInNewTab(),

                Infolists\Components\TextEntry::make('product.translations')
                  ->label(__('app.columns.favorite.product_name'))
                  ->getStateUsing(function (Favorite $record) {
                    return app(FavoriteService::class)->getTranslatedProductName($record);
                  })
                  ->icon('heroicon-o-cube'),

                Infolists\Components\TextEntry::make('created_at')
                  ->label(__('app.columns.favorite.created_at'))
                  ->dateTime()
                  ->icon('heroicon-o-calendar'),

                Infolists\Components\TextEntry::make('updated_at')
                  ->label(__('app.columns.favorite.updated_at'))
                  ->dateTime()
                  ->icon('heroicon-o-clock'),
              ]),
          ])
          ->columns(2)
          ->collapsible(),
      ]);
  }
}
