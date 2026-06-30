<?php

namespace App\Libraries\Shipping;

interface ShippingInterface
{
    public function calculate(array $params): array;
    public function tracking(string $trackingCode): array;
}
