<?php

namespace Modules\Catalog\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use Modules\Catalog\Contracts\ProductServiceInterface;
use Modules\Catalog\Http\Resources\Product\ProductApiResource;

use function App\Helpers\showAll;
use function App\Helpers\showOne;

class ProductController extends Controller
{

    public function __construct(private ProductServiceInterface $productService) {}
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = $this->productService->getProducts();

        return showAll(ProductApiResource::collection($products), 'products', 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = $this->productService->findProductById($id);

        return showOne($product, 'product', 200);
    }
}


