<?php

namespace Modules\Catalog\Traits;

use Modules\Catalog\Services\Category\CategoryTranslationService;

trait HasCategoryTranslations
{
  protected ?CategoryTranslationService $translationService = null;

  protected function getTranslationService(): CategoryTranslationService
  {
    if ($this->translationService === null) {
      $this->translationService = resolve(CategoryTranslationService::class);
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

  protected function saveTranslations(array $translationData): void
  {
    if (!$this->record) {
      return;
    }

    $this->getTranslationService()->saveTranslations($this->record, $translationData);
  }

  protected function extractTranslationData(array $data): array
  {
    $translationData = [
      'en' => [
        'name' => $data['en']['name'] ?? null,
        'description' => $data['en']['description'] ?? null,
      ],
      'ar' => [
        'name' => $data['ar']['name'] ?? null,
        'description' => $data['ar']['description'] ?? null,
      ],
    ];

    // Remove translation data from main data array
    unset($data['en'], $data['ar']);

    return ['main' => $data, 'translations' => $translationData];
  }

  /**
   * Generate slug from translation data
   * 
   * Note: Slug generation is now primarily handled by CategoryService.
   * This method is kept for backward compatibility and can be used
   * if needed in other contexts.
   */
  protected function generateSlugFromName(string $categoryName): string
  {
    return $this->getTranslationService()->generateSlugFromName($categoryName);
  }
}
