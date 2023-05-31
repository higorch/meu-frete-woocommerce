<?php

if (!defined('ABSPATH')) {
    exit;
}

class Hide_Others_Shipping
{

    public function __construct()
    {
        add_filter('woocommerce_package_rates', array($this, 'hide_other_shipping_when_free_is_available'), 100, 2);
    }

    public function hide_other_shipping_when_free_is_available($rates, $package)
    {
        $free = array();

        foreach ($rates as $rate_id => $rate) :

            if ('free_shipping' === $rate->method_id) :
                $free[$rate_id] = $rate;
                break;
            endif;

        endforeach;

        return !empty($free) ? $free : $rates;
    }
}

new Hide_Others_Shipping();
