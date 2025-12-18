<?php

namespace Modules\Catalog\Events;

use Modules\Catalog\Entities\Product\Product;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProductUpdated
{
    use Dispatchable, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(
        public Product $product
    ) {}
}

