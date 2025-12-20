<?php

namespace Modules\Catalog\Traits;

use Modules\Catalog\Services\Product\ProductTranslationService;

trait HasProductTranslations
{
  protected ?ProductTranslationService $translationService = null;

  protected function getTranslationService(): ProductTranslationService
  {
    if ($this->translationService === null) {
      $this->translationService = resolve(ProductTranslationService::class);
    }

    return $this->translationService;
  }

  protected function getTranslationData(): array
  {
    if (!$this->record) {
      return [];
    }

    return $this->getTranslationService()->getFormData($this->record);
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

  /**
   * Generate slug from translation data
   * 
   * Note: Slug generation is now primarily handled by ProductService.
   * This method is kept for backward compatibility and can be used
   * if needed in other contexts.
   */
  protected function generateSlugFromName(string $productName): string
  {
    return $this->getTranslationService()->generateSlugFromName($productName);
  }
}
