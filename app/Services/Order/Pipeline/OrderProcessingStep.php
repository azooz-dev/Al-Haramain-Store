<?php

namespace App\Services\Order\Pipeline;

interface OrderProcessingStep
{
    public function handle(array $data, \Closure $next);
}


