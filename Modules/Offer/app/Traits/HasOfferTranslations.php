<?php

namespace Modules\Offer\Traits;

use Modules\Offer\Services\OfferTranslationService;

trait HasOfferTranslations
{
  protected OfferTranslationService $translationService;

  public function __construct()
  {
    $this->translationService = app(OfferTranslationService::class);
  }

  protected function getTranslationData(): array
  {
    if (!$this->record) {
      return [];
    }

    return $this->translationService->getFormData($this->record);
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
}
