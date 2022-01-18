<?php

if (!defined('ABSPATH')) {
    exit;
}

class Setup
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));

        // remover produtos fora de estoque do carrinho
        add_action('woocommerce_before_cart', array($this, 'remove_out_of_stock_in_cart'));
        add_action('woocommerce_add_to_cart_fragments', array($this, 'remove_out_of_stock_in_cart'));
    }

    public function enqueue_scripts()
    {
        wp_enqueue_style('mf-style', plugin_dir_url(__DIR__) . 'assets/css/style.css', array(), '1.0.1');
    }

    // remover produtos fora de estoque do carrinho
    public function remove_out_of_stock_in_cart()
    {
        if (WC()->cart->is_empty()) {
            return;
        }

        $removed_products = [];

        foreach (WC()->cart->get_cart() as $cart_item_key => $cart_item) {
            $product_obj = $cart_item['data'];

            if (!$product_obj->is_in_stock()) {
                WC()->cart->remove_cart_item($cart_item_key);
                $removed_products[] = $product_obj;
            }
        }

        if (!empty($removed_products)) {
            wc_clear_notices(); // remove any WC notice about sorry about out of stock products to be removed from cart.

            foreach ($removed_products as $idx => $product_obj) {
                $product_name = $product_obj->get_title();
                $msg = sprintf(__("O produto '%s' foi removido do carrinho que está sem estoque."), $product_name);

                wc_add_notice($msg, 'error');
            }
        }
    }
}

new Setup();
