<?php

/*
Plugin Name: Meu Frete para WooCommerce
Plugin URI: https://agenciapiracanjuba.com.br
Description: Integração com correios, SEDEX e PAC, cálculo do frete no carrinho e checkout
Version: 0.2.0
Author: Higor Christian (AP)
Author URI: https://github.com/higorch
Text Domain: wmf
Domain Path: /languages
*/

if (!defined('ABSPATH')) {
    exit;
}

include plugin_dir_path(__FILE__) . 'includes/helpers.php';
include plugin_dir_path(__FILE__) . 'includes/class-setup.php';
include plugin_dir_path(__FILE__) . 'includes/class-metabox.php';
include plugin_dir_path(__FILE__) . 'includes/correios/class-webservice-correios.php';
include plugin_dir_path(__FILE__) . 'includes/class-request.php';

add_action('wp_loaded', function () {
    include plugin_dir_path(__FILE__) . 'includes/class-shipping-methods-init.php';
    include plugin_dir_path(__FILE__) . 'includes/class-mf-cart-shipping-methods.php';
    include plugin_dir_path(__FILE__) . 'includes/class-hide-others-shipping-free-available.php';
    include plugin_dir_path(__FILE__) . 'includes/class-timeline-order.php';
    include plugin_dir_path(__FILE__) . 'includes/class-single-product-shipping-methods.php';
});
