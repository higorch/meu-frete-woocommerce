(function ($) {

    var is_variable = false;
    var variable_needed = false;

    // quando existe variacao de produto variavel selecionada
    $(document).on("show_variation", function (e) {
        variable_needed = false;
    });

    // quando nao existe variacao de produto variavel selecionada
    $(document).on("hide_variation", function (e) {
        is_variable = true;
        variable_needed = true;
        $('.mf-wrap-postcode-single .box-fields button').attr('data-variation_id', '');
    });

    // quando existe a variacao selecionada e retorna o id da variacao
    $(document).on("found_variation.wc-variation-form", function (e, v) {
        $('.mf-wrap-postcode-single .box-fields button').attr('data-variation_id', v.variation_id);
    });

    // enviar a requisicao para consultar os valores e metodos de frete
    $(document).on('click', '.mf-wrap-postcode-single .box-fields button', function (e) {

        if (is_variable && variable_needed) {
            alert('Selecione uma das opções do produto antes de calcular o frete.');
            return;
        }

        var thisEl = $(this);

        // remove table shipping methods
        thisEl.parent('.box-fields').next('.box-results').fadeOut('500').html();

        var formData = new FormData();
        formData.append('product_id', thisEl.attr('data-product_id'));
        formData.append('variation_id', thisEl.attr('data-variation_id'));
        formData.append('postcode', $('.mf-wrap-postcode-single .box-fields input[name="postcode"]').val());
        formData.append('quantity', $('.summary.entry-summary .cart .quantity input.qty').val());
        formData.append('action', 'get_shipping_methods');

        $.ajax({
            type: 'POST',
            url: mf_ajax.ajax_url,
            data: formData,
            processData: false,
            contentType: false,
            cache: false,
            beforeSend: function (xhr) {
                thisEl.attr('disabled', true);
                $(document).trigger('mfsp_process_process', [thisEl]);
            },
            success: function (response) {

                thisEl.attr('disabled', false);
                $(document).trigger('mfsp_process_finish', [thisEl]);

                // add table shipping methods
                thisEl.parent('.box-fields').next('.box-results').fadeIn().html(response);

            }
        });

    });

})(jQuery);