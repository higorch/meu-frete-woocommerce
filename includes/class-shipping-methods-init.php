<?php

if (!defined('ABSPATH')) {
    exit;
}

class Shipping_Methods_Init
{

    public function __construct()
    {
        add_action('woocommerce_shipping_init', array($this, 'mf_shipping_method_init'));
        add_filter('woocommerce_shipping_methods', array($this, 'mf_shipping_methods'));
    }

    public function mf_shipping_method_init()
    {
        include plugin_dir_path(__DIR__) .  'includes/correios/methods/class-shipping-method-pac.php';
        include plugin_dir_path(__DIR__) .  'includes/correios/methods/class-shipping-method-sedex.php';
    }

    public function mf_shipping_methods($methods)
    {
        $methods['mf_shipping_method_pac'] = 'Shipping_Method_PAC';
        $methods['mf_shipping_method_sedex'] = 'Shipping_Method_Sedex';

        return $methods;
    }
}

new Shipping_Methods_Init();
