(function($){
    
    $(document).on('click', '.mf-wrap-postcode-single .box-fields button', function(e){

        var thisEl = $(this);

        // remove table shipping methods
        thisEl.parent('.box-fields').next('.box-results').fadeOut('500').html();

        var formData = new FormData();
        formData.append('product_id', thisEl.attr('data-product_id'));
        formData.append('mf-postcode', $('.mf-wrap-postcode-single .box-fields input[name="mf-postcode"]').val());
        formData.append('mf-single-quantity', $('.summary.entry-summary .cart .quantity input.qty').val());
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