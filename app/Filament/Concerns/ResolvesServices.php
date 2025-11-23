<?php

namespace App\Filament\Concerns;

trait ResolvesServices
{
  protected function resolveService(string $serviceClass): object
  {
    $propertyName = $this->getServicePropertyName($serviceClass);

    // Lazy loading: resolve only when first accessed
    if (!isset($this->$propertyName)) {
      $this->$propertyName = resolve($serviceClass);
    }

    return $this->$propertyName;
  }

  protected function getServicePropertyName(string $serviceClass): string
  {
    $shortName = class_basename($serviceClass);
    return lcfirst($shortName);
  }

  protected function clearResolvedServices(): void
  {
    $reflection = new \ReflectionClass($this);
    $properties = $reflection->getProperties(\ReflectionProperty::IS_PROTECTED | \ReflectionProperty::IS_PRIVATE);

    foreach ($properties as $property) {
      $property->setAccessible(true);
      $value = $property->getValue($this);

      // Check if the property value is a service (object) that was resolved
      if (is_object($value) && str_starts_with(get_class($value), 'App\\Services\\')) {
        $property->setValue($this, null);
      }
    }
  }
}
