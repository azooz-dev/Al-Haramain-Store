<?php

namespace Modules\Catalog\Repositories\Interface\Product;

interface ProductRepositoryInterface extends 
    ReadProductRepositoryInterface, 
    WriteProductRepositoryInterface, 
    QueryableProductRepositoryInterface
{
}


