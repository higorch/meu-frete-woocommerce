<?php

if (!defined('ABSPATH')) {
    exit;
}

class Shipping_Method_PAC extends WC_Shipping_Method
{
    /**
     * Constructor for your shipping class
     *
     * @access public
     * @return void
     */
    public function __construct($instance_id = 0)
    {
        $this->id = 'mf_shipping_method_pac';
        $this->instance_id = absint($instance_id);
        $this->enabled = "yes";
        $this->method_title = __('Pac');
        $this->method_description = __('Frete via Pac');
        $this->supports = array(
            'shipping-zones',
            'instance-settings',
        );
        $this->instance_form_fields = array(
            'enabled' => array(
                'title' => __('Habilitar/Desabilitar'),
                'type' => 'checkbox',
                'label' => __('Habilitar o método'),
                'default' => 'yes',
            ),
            'zip_code' => array(
                'title' => __('CEP de origem'),
                'type' => 'text',
                'default' => get_option('woocommerce_store_postcode'),
            ),
            'company_code' => array(
                'title' => __('Código da empresa'),
                'type' => 'text',
                'default' => '',
            ),
            'company_password' => array(
                'title' => __('Senha da empresa'),
                'type' => 'text',
                'default' => '',
            ),
            'additional_days' => array(
                'title' => __('Dias adicionais'),
                'type' => 'number',
                'default' => '2',
            ),
            'mao_propria' => array(
                'title' => __('Serviço mão propria'),
                'type' => 'checkbox',
                'default' => 'no',
            ),
            'aviso_recebimento' => array(
                'title' => __('Serviço aviso recebimento'),
                'type' => 'checkbox',
                'default' => 'no',
            ),
        );
        $this->enabled = $this->get_option('enabled');
        $this->title = 'Pac';

        $this->init();
    }

    /**
     * Init your settings
     *
     * @access public
     * @return void
     */
    public function init()
    {
        // Load the settings API
        $this->init_form_fields(); // This is part of the settings API. Override the method to add your own settings
        $this->init_settings(); // This is part of the settings API. Loads settings you previously init.

        // Save settings in admin if you have any defined
        add_action('woocommerce_update_options_shipping_' . $this->id, array($this, 'process_admin_options'));
    }

    /**
     * calculate_shipping function.
     *
     * @access public
     * @param mixed $package
     * @return void
     */
    public function calculate_shipping($package = array())
    {
        $correios = new Correios();

        $pac = $correios->setCodigoEmpresa($this->get_option('company_code'))
            ->setSenhaEmpresa($this->get_option('company_password'))
            ->setCepOrigem($this->get_store_postcode())
            ->setCepDestino($this->get_customer_postcode($package))
            ->setFormatoEncomenda(1) // caixa/pacote
            ->setCodigoServico('04510') // PAC
            ->setItens($this->get_itens($package))
            ->run();

        if ($pac['erro'] != false) :
            return;
        endif;

        $rate = array(
            'id' => $this->id,
            'label' => $this->title,
            'cost' => $pac['valor'],
            'meta_data' => array(
                '_delivery_estimate' => ((int) $pac['prazoEntrega'] + (int) $this->get_option('additional_days')),
            )
        );

        // Register the rate
        $this->add_rate($rate);
    }

    /**
     * obter itens do carrinho
     */
    public function get_itens($package)
    {
        $items = array();

        foreach ($package['contents'] as $key => $value) {

            $produto = $value['data'];

            $items[] = array(
                'largura' => $produto->get_width(),
                'altura' => $produto->get_height(),
                'comprimento' => $produto->get_length(),
                'peso' => $produto->get_weight(),
                'quantidade' => $value['quantity']
            );
        }

        return $items;
    }

    /**
     * obter CEP do cliente
     */
    public function get_customer_postcode($package)
    {
        $postal_code = $package['destination']['postcode'];

        return $postal_code;
    }

    /**
     * Obter o cep de origem
     */
    public function get_store_postcode()
    {
        return !empty($this->get_option('zip_code')) ? $this->get_option('zip_code') : WC()->countries->get_base_postcode();
    }
}
