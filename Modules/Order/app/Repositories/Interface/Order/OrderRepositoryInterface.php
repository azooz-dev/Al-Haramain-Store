<?php

namespace Modules\Order\Repositories\Interface\Order;

interface OrderRepositoryInterface extends 
    ReadOrderRepositoryInterface, 
    WriteOrderRepositoryInterface, 
    QueryableOrderRepositoryInterface
{
}
