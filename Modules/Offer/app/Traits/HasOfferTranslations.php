<?php

namespace Modules\Offer\Traits;

use Modules\Offer\Contracts\OfferTranslationServiceInterface;

trait HasOfferTranslations
{
  protected ?OfferTranslationServiceInterface $translationService = null;

  protected function getTranslationService(): OfferTranslationServiceInterface
  {
    if ($this->translationService === null) {
      $this->translationService = resolve(OfferTranslationServiceInterface::class);
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
}
