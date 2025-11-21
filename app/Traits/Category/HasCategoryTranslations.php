<?php

namespace App\Traits\Category;

use App\Services\Category\CategoryTranslationService;

trait HasCategoryTranslations
{
  protected CategoryTranslationService $translationService;

  public function __construct()
  {
    $this->translationService = app(CategoryTranslationService::class);
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

    $this->translationService->saveTranslations($this->record, $translationData);
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
    return $this->translationService->generateSlugFromName($categoryName);
  }
}
