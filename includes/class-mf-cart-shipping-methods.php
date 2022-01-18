<?php

if (!defined('ABSPATH')) {
     exit;
}

class Mf_Cart_Shipping_Methods
{
     public function __construct()
     {
          add_filter('woocommerce_cart_shipping_method_full_label', array($this, 'shipping_method_estimate_label'), 10, 2);
     }

     public function shipping_method_estimate_label($label, $method)
     {
          $label .= '<br /><small>';

          if ($method->method_id == 'mf_shipping_method_sedex') :
               $label .= 'Entrega estimada: ' . $method->meta_data['_delivery_estimate'] . ' dias úteis';
          elseif ($method->method_id == 'mf_shipping_method_pac') :
               $label .= 'Entrega estimada: ' . $method->meta_data['_delivery_estimate'] . ' dias úteis';
          endif;

          $label .= '</small>';

          return $label;
     }
}

new Mf_Cart_Shipping_Methods();
