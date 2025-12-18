<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use Modules\Catalog\Traits\HasCategoryTranslations;
use Filament\Resources\Pages\ViewRecord;

class ViewCategory extends ViewRecord
{
  use HasCategoryTranslations;

  protected static string $resource = CategoryResource::class;

  protected function getHeaderActions(): array
  {
    return [
      \Filament\Actions\EditAction::make(),
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
      $formData = $this->getTranslationData();
      $formData['en'] = ['name' => $formData['en']['title'] ?? ($formData['en']['name'] ?? ''), 'description' => $formData['en']['details'] ?? ($formData['en']['description'] ?? '')];
      $formData['ar'] = ['name' => $formData['ar']['title'] ?? ($formData['ar']['name'] ?? ''), 'description' => $formData['ar']['details'] ?? ($formData['ar']['description'] ?? '')];
      $this->form->fill($formData);
    }
  }
}
