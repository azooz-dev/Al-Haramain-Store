<?php

namespace App\Repositories\Interface\Order;

interface OrderRepositoryInterface extends 
    ReadOrderRepositoryInterface, 
    WriteOrderRepositoryInterface, 
    QueryableOrderRepositoryInterface
{
}
