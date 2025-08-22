<?php

namespace App\Traits\Product;

use App\Services\Product\ProductTranslationService;

trait HasProductTranslations
{
  protected ProductTranslationService $translationService;

  public function __construct()
  {
    $this->translationService = app(ProductTranslationService::class);
  }


  protected function getTranslationData(): array
  {
    if (!$this->record) {
      return [];
    }

    return $this->translationService->getFormData($this->record);
  }

  protected function saveTranslations(array $translationData): void
  {
    if (!$this->record) {
      return;
    }


    $this->translationService->saveTranslation($this->record, $translationData);
  }

  protected function extractTranslationData(array $data): array
  {
    $translationData = [
      'en' => [
        'name' => $data['en']['name'],
        'description' => $data['en']['description']
      ],
      'ar' => [
        'name' => $data['ar']['name'],
        'description' => $data['ar']['description']
      ],
    ];

    unset($data['en'], $data['ar']);

    return ['main' => $data, 'translations' => $translationData];
  }

  protected function generateSlugFromName(string $productName): string
  {
    return $this->translationService->generateSlugFromName($productName);
  }
}
