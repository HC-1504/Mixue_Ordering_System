<?php
require_once __DIR__ . '/../DeliveryStrategy.php';

class SelfPickup implements DeliveryStrategy
{
    public function getFee(): float
    {
        return 0.00;
    }
    public function getMessage(): string
    {
        return "Please pickup your order at the selected Mixue branch.";
    }
}
