<?php
require_once __DIR__ . '/../DeliveryStrategy.php';

class GrabDelivery implements DeliveryStrategy {
    public function getFee(): float {
        return 4.00;
    }
    public function getMessage(): string {
        return "Your order will be delivered via Grab.";
    }
}
