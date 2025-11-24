<?php

namespace App\Repositories\Interface\Category;

interface CategoryRepositoryInterface extends 
    ReadCategoryRepositoryInterface, 
    WriteCategoryRepositoryInterface, 
    QueryableCategoryRepositoryInterface
{
}
