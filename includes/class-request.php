<?php

if (!defined('ABSPATH')) {
    exit;
}

class Mf_Request
{
    public function __construct()
    {
        add_action('wp_enqueue_scripts', array($this, 'mf_enqueue_scripts'));
    }

    public function mf_enqueue_scripts()
    {
        wp_enqueue_style('mf-single-style', plugin_dir_url(__DIR__) . 'assets/css/style.css');

        wp_enqueue_script('mf-request', plugin_dir_url(__DIR__) . 'assets/js/request.js', array('jquery'), '1.0.1', true);
        wp_localize_script('mf-request', 'mf_ajax', array('ajax_url' => admin_url('admin-ajax.php')));
    }
}

new Mf_Request();
