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
      $formData = $this->translationService->getFormData($this->record);
      $formData['en'] = ['name' => $formData['en']['title'], 'description' => $formData['en']['details']];
      $formData['ar'] = ['name' => $formData['ar']['title'], 'description' => $formData['ar']['details']];
      $formData['quantity'] = $formData['stock'];

      unset($formData['stock']);

      $this->form->fill($formData);
    }
  }
}
