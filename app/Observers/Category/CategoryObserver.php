<?php

namespace App\Observers\Category;

use App\Models\Category\Category;
use Illuminate\Support\Facades\Storage;

class CategoryObserver
{

    /**
     * Handle the Category "updating" event.
     */
    public function updating(Category $category): void
    {
        if ($category->isDirty('image')) {
            $originalImagePath = $category->getOriginal('image');
            if ($originalImagePath) {
                Storage::disk('public')->delete($originalImagePath);
            }
        }
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        if ($category->image) {
            Storage::disk('public')->delete($category->image);
        }
    }
}
