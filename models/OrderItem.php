<?php
require_once __DIR__ . '/Model.php';

class OrderItem extends Model
{
    protected static $table = 'order_items';
    protected $primaryKey = 'item_id';
    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_price',
        'subtotal',
        'notes'
    ];

    // Relationship with Product
    public function product()
    {
        require_once __DIR__ . '/Product.php';
        return Product::find($this->product_id);
    }

    // Calculate subtotal
    public function calculateSubtotal()
    {
        $this->subtotal = $this->quantity * $this->unit_price;
        return $this->save();
    }

    // Get items for a specific order
    public static function getByOrderId($orderId)
    {
        return self::where('order_id', $orderId);
    }

    // Update quantity and recalculate
    public function updateQuantity($newQuantity)
    {
        $this->quantity = $newQuantity;
        $this->calculateSubtotal();
        return $this->save();
    }
}
