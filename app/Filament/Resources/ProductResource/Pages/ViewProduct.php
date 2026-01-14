<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Modules\Catalog\Traits\HasProductTranslations;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewProduct extends ViewRecord
{
  use HasProductTranslations;
  protected static string $resource = ProductResource::class;

  protected function getHeaderActions(): array
  {
    return [
      Actions\EditAction::make()
        ->icon('heroicon-o-pencil')
        ->label(__('app.actions.edit')),
    ];
  }

  protected function getRedirectUrl(): string
  {
    return $this->getResource()::getUrl('index');
  }

  protected function fillForm(): void
  {
    parent::fillForm();

    if ($record = $this->record) {
      $apiData = $this->getTranslationService()->getFormData($this->record);
      
      // Only extract the fields that the form schema expects
      // Exclude complex nested arrays like available_sizes, available_colors, reviews, etc.
      // that Livewire cannot serialize properly
      $formData = [
        'sku' => $apiData['sku'] ?? '',
        'slug' => $apiData['slug'] ?? '',
        'quantity' => $apiData['stock'] ?? 0,
        'en' => [
          'name' => $apiData['en']['title'] ?? '',
          'description' => $apiData['en']['details'] ?? '',
        ],
        'ar' => [
          'name' => $apiData['ar']['title'] ?? '',
          'description' => $apiData['ar']['details'] ?? '',
        ],
        // Colors and categories are handled via relationships in the Repeater/Select components
        'colors' => $apiData['colors'] ?? [],
        'categories' => $apiData['categories'] ?? [],
      ];

      $this->form->fill($formData);
    }
  }
}
