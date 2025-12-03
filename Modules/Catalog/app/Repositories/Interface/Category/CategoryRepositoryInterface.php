<?php

namespace Modules\Catalog\Repositories\Interface\Category;

interface CategoryRepositoryInterface extends 
    ReadCategoryRepositoryInterface, 
    WriteCategoryRepositoryInterface, 
    QueryableCategoryRepositoryInterface
{
}


