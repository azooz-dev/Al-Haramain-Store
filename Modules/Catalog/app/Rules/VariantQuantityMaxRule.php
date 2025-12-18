<?php

namespace Modules\Catalog\Rules;

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
        if ($value === null || $value === '') {
            return;
        }

        if (! is_numeric($value)) {
            $fail(__('validation.numeric', ['attribute' => $attribute]) ?? 'The :attribute must be a number.');
            return;
        }

        $mainQuantity = (int) $value;


        // find snapshot JSON inside request components (Livewire)
        $components = request()->input('components', []);
        $snapshotJson = null;

        if (! empty($components) && is_array($components)) {
            foreach ($components as $comp) {
                if (isset($comp['snapshot'])) {
                    $snapshotJson = $comp['snapshot'];
                    break;
                }
            }
        }

        if (! $snapshotJson) {
            // nothing to validate against
            return;
        }

        $snapshot = json_decode($snapshotJson, true);
        if (! is_array($snapshot)) {
            return;
        }

        /**
         * Helper: try to convert strings that look like JSON into arrays.
         */
        $ensureArray = function ($maybe) {
            if (is_array($maybe)) {
                return $maybe;
            }
            if (is_string($maybe)) {
                $decoded = json_decode($maybe, true);
                if (is_array($decoded)) {
                    return $decoded;
                }
            }
            return null;
        };

        /**
         * Recursively collect numeric 'quantity' values from any subtree.
         */
        $collectQuantities = function ($node) use (&$collectQuantities) {
            $sum = 0;
            if (is_array($node)) {
                foreach ($node as $k => $v) {
                    if ($k === 'quantity' && is_numeric($v)) {
                        $sum += (int) $v;
                        continue;
                    }
                    $sum += $collectQuantities($v);
                }
            }
            return $sum;
        };

        /**
         * Walk the snapshot and whenever we see a key 'variants', sum all 'quantity'
         * values inside that subtree (robust to strings that contain JSON).
         */
        $sum = 0;
        $walker = function ($node) use (&$walker, $ensureArray, $collectQuantities, &$sum) {
            if (is_array($node)) {
                foreach ($node as $k => $v) {
                    if ($k === 'variants') {
                        // Normalize variants to array if possible
                        $variants = $ensureArray($v) ?? $v;
                        // sum any quantities inside this subtree
                        $sum += $collectQuantities($variants);
                        // continue walking in case there are nested 'variants' deeper
                        $walker($variants);
                    } else {
                        // if this value is a JSON string for a subtree, try to decode and walk it
                        $maybe = $ensureArray($v) ?? $v;
                        $walker($maybe);
                    }
                }
            }
            // if not an array, nothing to walk
        };

        // Start walking from the part of snapshot that contains your product nodes.
        // Defensive: if structure differs, walk the full snapshot.
        $productNodes = $snapshot['data']['data'] ?? $snapshot;
        $walker($productNodes);

        $mainQuantity = (int) $productNodes[0]['quantity'];

        // Compare
        if ($sum !== $mainQuantity) {
            $message = __(
                'app.validation.variant_quantity_must_equal_total',
                ['variants_sum' => $sum, 'total' => $mainQuantity]
            );
            if ($message === 'app.validation.variant_quantity_must_equal_total') {
                $message = "Sum of all variant quantities ({$sum}) must equal the product total quantity ({$mainQuantity}).";
            }
            $fail($message);
        }
    }
}
