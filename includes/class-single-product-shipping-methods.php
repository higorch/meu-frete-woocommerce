<?php

if (!defined('ABSPATH')) {
    exit;
}

class Single_Product_Shipping_Methods
{
    public function __construct()
    {
        add_action('woocommerce_single_product_summary', array($this, 'add_template_postcode'), 40);

        add_action('wp_ajax_get_shipping_methods', array($this, 'get_shipping_methods'));
        add_action('wp_ajax_nopriv_get_shipping_methods',  array($this, 'get_shipping_methods'));
    }

    public function add_template_postcode()
    {
        global $product;

        $product_id = $product->get_id();

        include  plugin_dir_path(__DIR__) . 'templates/single-shipping-methods.php';
    }

    public function get_shipping_methods()
    {
        $data = $_POST;
        $output = '';
        $methods = $this->load_shipping_methods($data);

        if (is_array($methods)) :

            $output .= '<table>
                            <tr>
                                <th align="center">Entrega</th>
                                <th align="center">Frete</th>
                                <th align="center">Prazo</th>
                            </tr>';

            foreach ($methods as $value) :

                $estimate = isset($value['meta_data']['_delivery_estimate']) ? $value['meta_data']['_delivery_estimate'] . ' dias úteis' : '...';

                $output .= '<tr>
                                    <td align="center">' . $value['label'] . '</td>
                                    <td align="center"> R$ ' . number_format($value['cost'], 2, ',', '.') . '</td>
                                    <td align="center">' . apply_filters('wmfsp_estimative', $estimate, $value) . '</th>
                                </tr>';
            endforeach;

            $output .= '</table>';

        else :
            $output .= '<p class="mf-msg">' . $methods . '</p>';
        endif;

        echo $output;

        wp_die();
    }

    public function load_shipping_methods(array $request)
    {
        $product = wc_get_product($request['product_id']);
        $customer_postcode = str_replace('-', '', $request['postcode']);
        $quantity = (int) $request['quantity'];

        $output = array();

        if (!$product->needs_shipping() || get_option('woocommerce_calc_shipping') === 'no') :
            return 'Não foi possível calcular a entrega deste produto';
        endif;

        if (!$product->is_in_stock()) :
            return 'Não foi possível calcular a entrega deste produto, pois o mesmo não está disponível.';
        endif;

        if (!WC_Validation::is_postcode($customer_postcode, WC()->customer->get_shipping_country())) :
            return 'Por favor, insira um CEP válido.';
        endif;

        if (WC()->customer->get_shipping_country()) :
            $destination = [
                'country' => WC()->customer->get_shipping_country(),
                'state' => WC()->customer->get_shipping_state(),
                'postcode' => sanitize_text_field($customer_postcode),
                'city' => WC()->customer->get_shipping_city(),
                'address' => WC()->customer->get_shipping_address(),
                'address_2' => WC()->customer->get_shipping_address_2(),
            ];
        else :
            $destination = wc_get_customer_default_location();
        endif;

        $cartId = WC()->cart->generate_cart_id($product->id, $product->is_type('variable') ? $product->variation_id : 0);
        $price = wc_get_price_excluding_tax($product);
        $tax = wc_get_price_including_tax($product) - $price;

        $package['contents'] = [
            $cartId => [
                'key' => $cartId,
                'product_id' => $product->id,
                'variation_id' => 402,
                'quantity' => $quantity,
                'line_total' => $price,
                'line_tax' => $tax,
                'line_subtotal' => $price,
                'line_subtotal_tax' => $tax,
                'contents_cost' => $price,
                'data' => $request['variation_id'] == '' ?  $product : wc_get_product($request['variation_id']),
            ]
        ];

        $package['destination'] = $destination;
        $package['applied_coupons'] = WC()->cart->applied_coupons;
        $package['user'] = ['ID' => get_current_user_id()];

        $packageRates = WC()->shipping->calculate_shipping_for_package($package, $cartId);

        foreach ($packageRates['rates'] as $key => $value) :

            $output[$key] = array(
                'id' => $value->get_id(),
                'method_id' => $value->get_method_id(),
                'instance_id' => $value->get_instance_id(),
                'label' => $value->get_label(),
                'cost' => $value->get_cost(),
                'tax' => $value->get_shipping_tax(),
                'taxes' => $value->get_taxes(),
                'meta_data' => $value->get_meta_data(),
            );

        endforeach;

        return $output;
    }
}

new Single_Product_Shipping_Methods();
