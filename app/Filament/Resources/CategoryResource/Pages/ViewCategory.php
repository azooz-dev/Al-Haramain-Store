<?php

namespace App\Filament\Resources\CategoryResource\Pages;

use App\Filament\Resources\CategoryResource;
use App\Traits\HasCategoryTranslations;
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
      $this->form->fill($formData);
    }
  }
}
