<?php
interface DeliveryStrategy
{
    public function getFee(): float;
    public function getMessage(): string;
}
