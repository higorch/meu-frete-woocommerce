<?php

if (!defined('ABSPATH')) {
    exit;
}

class Timeline_Order
{

    public function __construct()
    {
        add_action('woocommerce_order_details_before_order_table', array($this, 'timeline'));
    }

    public function timeline($order)
    {
        $status = $order->get_status();
        $order_number = $order->get_order_number();
        $date_created = $order->get_date_created();
        $code_correios = get_post_meta($order->get_id(), 'order_code_correios', true);

        include plugin_dir_path(__DIR__) . '/templates/timeline-order.php';
    }
}

new Timeline_Order();
