<?php

namespace App\Filament\Concerns;

use Filament\Notifications\Notification;

trait SendsFilamentNotifications
{
  /**
   * Build a Filament Notification instance for success.
   * Use this in static contexts (Resource action config).
   */
  public static function buildSuccessNotification(string $titleKey, ?string $bodyKey = null, array $replace = [], ?int $durationMs = null): Notification
  {
    $notification = Notification::make()
      ->success()
      ->title(__($titleKey, $replace));

    if ($bodyKey) {
      $notification->body(__($bodyKey, $replace));
    }

    if ($durationMs) {
      $notification->duration($durationMs);
    }

    return $notification;
  }

  /**
   * Build a Filament Notification instance for error/danger.
   */
  public static function buildErrorNotification(string $titleKey, ?string $bodyKey = null, array $replace = [], ?int $durationMs = null): Notification
  {
    $notification = Notification::make()
      ->danger()
      ->title(__($titleKey, $replace));

    if ($bodyKey) {
      $notification->body(__($bodyKey, $replace));
    }

    if ($durationMs) {
      $notification->duration($durationMs);
    }

    return $notification;
  }

  /**
   * Convenience instance method to send a success notification (useful in page classes).
   */
  protected function notifySuccess(string $titleKey, ?string $bodyKey = null, array $replace = [], ?int $durationMs = null): void
  {
    self::buildSuccessNotification($titleKey, $bodyKey, $replace, $durationMs)->send();
  }

  /**
   * Convenience instance method to send an error notification.
   */
  protected function notifyError(string $titleKey, ?string $bodyKey = null, array $replace = [], ?int $durationMs = null): void
  {
    self::buildErrorNotification($titleKey, $bodyKey, $replace, $durationMs)->send();
  }
}
