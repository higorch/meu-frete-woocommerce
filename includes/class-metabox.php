<?php

if (!defined('ABSPATH')) {
    exit;
}

class Metabox
{

    public function __construct()
    {
        add_action('add_meta_boxes', array($this, 'metaboxes'));
        add_action('save_post_shop_order', array($this, 'save_meta_box'));
    }

    public function metaboxes()
    {
        add_meta_box('order-code-correios', __('Código rastreio'), array($this, 'order_code_correios_render'), 'shop_order', 'side', 'high');
    }

    public function order_code_correios_render($post)
    {
        echo '<input style="width:100%;" type="text" name="order_code_correios" id="order_code_correios" value="' . get_post_meta($post->ID, 'order_code_correios', true) . '" placeholder="Código dos correios">';
    }

    function save_meta_box($post_id)
    {
        update_post_meta($post_id, 'order_code_correios', $_POST['order_code_correios']);
    }
}

new Metabox();
