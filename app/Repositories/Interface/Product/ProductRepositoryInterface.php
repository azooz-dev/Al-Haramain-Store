<?php

namespace App\Repositories\Interface\Product;

interface ProductRepositoryInterface extends 
    ReadProductRepositoryInterface, 
    WriteProductRepositoryInterface, 
    QueryableProductRepositoryInterface
{
}
