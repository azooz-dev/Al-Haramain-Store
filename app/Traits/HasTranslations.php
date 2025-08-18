<?php

namespace App\Traits;

/**
 * HasTranslations Trait
 * 
 * Provides helper methods for working with translations in Filament resources.
 * This trait makes it easier to use translation keys consistently across
 * your application.
 * 
 * @package App\Traits
 */
trait HasTranslations
{
  /**
   * Get a translation key for navigation groups
   * 
   * @param string $group - The navigation group key
   * @return string - The translation key
   */
  protected function getNavigationGroupKey(string $group): string
  {
    return "navigation.{$group}";
  }

  /**
   * Get a translation key for resource labels
   * 
   * @param string $resource - The resource name
   * @param string $property - The property name (label, plural_label, etc.)
   * @return string - The translation key
   */
  protected function getResourceKey(string $resource, string $property): string
  {
    return "app.resources.{$resource}.{$property}";
  }

  /**
   * Get a translated resource label
   * 
   * @param string $resource - The resource name
   * @param string $property - The property name (label, plural_label, etc.)
   * @return string - The translated text
   */
  protected function getResourceLabel(string $resource, string $property): string
  {
    return __($this->getResourceKey($resource, $property));
  }

  /**
   * Get a translation key for form fields
   * 
   * @param string $resource - The resource name
   * @param string $field - The field name
   * @return string - The translation key
   */
  protected function getFormKey(string $resource, string $field): string
  {
    return "app.forms.{$resource}.{$field}";
  }

  /**
   * Get a translated form field label
   * 
   * @param string $resource - The resource name
   * @param string $field - The field name
   * @return string - The translated text
   */
  protected function getFormLabel(string $resource, string $field): string
  {
    return __($this->getFormKey($resource, $field));
  }

  /**
   * Get a translation key for table columns
   * 
   * @param string $column - The column name
   * @return string - The translation key
   */
  protected function getColumnKey(string $column): string
  {
    return "app.columns.{$column}";
  }

  /**
   * Get a translated column label
   * 
   * @param string $column - The column name
   * @return string - The translated text
   */
  protected function getColumnLabel(string $column): string
  {
    return __($this->getColumnKey($column));
  }

  /**
   * Get a translation key for messages
   * 
   * @param string $message - The message key
   * @return string - The translation key
   */
  protected function getMessageKey(string $message): string
  {
    return "app.messages.{$message}";
  }

  /**
   * Get a translated message
   * 
   * @param string $message - The message key
   * @return string - The translated text
   */
  protected function getMessage(string $message): string
  {
    return __($this->getMessageKey($message));
  }

  /**
   * Get a translation key for status labels
   * 
   * @param string $status - The status key
   * @return string - The translation key
   */
  protected function getStatusKey(string $status): string
  {
    return "status.{$status}";
  }

  /**
   * Get a translation key for actions
   * 
   * @param string $action - The action key
   * @return string - The translation key
   */
  protected function getActionKey(string $action): string
  {
    return "actions.{$action}";
  }

  /**
   * Get a translation key for filters
   * 
   * @param string $filter - The filter key
   * @return string - The translation key
   */
  protected function getFilterKey(string $filter): string
  {
    return "filters.{$filter}";
  }

  /**
   * Get a translation key for placeholders
   * 
   * @param string $placeholder - The placeholder key
   * @return string - The translation key
   */
  protected function getPlaceholderKey(string $placeholder): string
  {
    return "placeholders.{$placeholder}";
  }

  /**
   * Get a translation key for tooltips
   * 
   * @param string $tooltip - The tooltip key
   * @return string - The translation key
   */
  protected function getTooltipKey(string $tooltip): string
  {
    return "tooltips.{$tooltip}";
  }

  /**
   * Get the current locale
   * 
   * @return string - The current locale
   */
  protected function getCurrentLocale(): string
  {
    return app()->getLocale();
  }

  /**
   * Check if the current locale is Arabic
   * 
   * @return bool - True if current locale is Arabic
   */
  protected function isArabic(): bool
  {
    return $this->getCurrentLocale() === 'ar';
  }

  /**
   * Check if the current locale is English
   * 
   * @return bool - True if current locale is English
   */
  protected function isEnglish(): bool
  {
    return $this->getCurrentLocale() === 'en';
  }

  /**
   * Get text direction for the current locale
   * 
   * @return string - 'rtl' for Arabic, 'ltr' for others
   */
  protected function getTextDirection(): string
  {
    return $this->isArabic() ? 'rtl' : 'ltr';
  }

  /**
   * Get extra attributes for RTL support
   * 
   * @return array - Array with dir attribute if Arabic
   */
  protected function getRtlAttributes(): array
  {
    return $this->isArabic() ? ['dir' => 'rtl'] : [];
  }
}
