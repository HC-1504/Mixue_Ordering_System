<?php
require_once __DIR__ . '/../models/OrderDetail.php';
require_once __DIR__ . '/../models/Order.php';

class OrderDetailController
{
    public function show($id)
    {
        $orderModel = new Order();
        $detailModel = new OrderDetail();

        $order = $orderModel->find($id);
        if (!$order) {
            require __DIR__ . '/../views/order_details/not_found.php';
            return;
        }

        $details = $detailModel->getOrderDetailsByOrderId($id);
        require __DIR__ . '/../views/order/order_details.php';
    }
}
