<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

class VariantQuantityMaxRule implements ValidationRule
{
    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // If value is null or empty, let other rules (required) handle it.
        if ($value === null || $value === '') {
            return;
        }

        // Ensure numeric
        if (! is_numeric($value)) {
            $fail(__('validation.numeric', ['attribute' => $attribute]) ?? 'The :attribute must be a number.');
            return;
        }

        // Extract main product quantity from Livewire form data
        $mainQuantity = 0;
        $components = request()->input('components', []);

        if (!empty($components) && isset($components[0]['snapshot'])) {
            $snapshot = json_decode($components[0]['snapshot'], true);
            if (isset($snapshot['data']['data'][0]['quantity'])) {
                $mainQuantity = (int) $snapshot['data']['data'][0]['quantity'];
            }
        }

        // Compare as integers
        $variantQty = (int) $value;

        if ($variantQty > $mainQuantity) {
            // Use your translation key if available, otherwise fallback to a clear message
            $message = __(
                'app.validation.variant_quantity_exceeds_stock',
                ['variant_quantity' => $variantQty, 'total_stock' => $mainQuantity]
            );

            if ($message === 'app.validation.variant_quantity_exceeds_stock') {
                // translation key not found — fallback
                $message = "Variant quantity ({$variantQty}) must not exceed product total quantity ({$mainQuantity}).";
            }

            $fail($message);
        }
    }
}
