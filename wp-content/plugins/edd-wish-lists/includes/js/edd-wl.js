jQuery(document).ready(function ($) {

    // Prevent the checkout form from submitting when hitting Enter in the list name field
    $('#edd-wl-modal').on('keypress', '#list-name', function (event) {
        if (event.keyCode == '13') {
            return false;
        }
    });

    // Hide unneeded elements. These are things that are required in case JS breaks or isn't present
    $('a.edd-add-to-wish-list').addClass('edd-has-js');

    // Send Add to Cart request
    $('body').on('click.eddAddToCartFromWishList', '.edd-add-to-cart-from-wish-list', function (e) {
        e.preventDefault();

    //    console.log( 'added to cart' );

        var $this = $(this);

        var container = $this.closest('div');

        // spinner
        var $spinner = $(this).find('.edd-loading');
        
        var spinnerWidth    = $spinner.width(),
        spinnerHeight       = $spinner.height();

        // Show the spinner
        $this.attr('data-edd-loading', '');

        $spinner.css({
            'margin-left': spinnerWidth / -2,
            'margin-top' : spinnerHeight / -2
        });

        var download       = $this.data('download-id');
        var variable_price = $this.data('variable-price');
        var price_option   = $this.data('price-option');
        var price_mode     = $this.data('price-mode');
        var item_price_ids = [];

        if( variable_price == 'yes' ) {

        //    console.log( price_option );

            item_price_ids[0] = price_option;

        } else {
            item_price_ids[0] = download;
        }

        var action = $this.data('action');

        var data   = {
            action: action,
            download_id: download,
            price_ids : item_price_ids,
            nonce: edd_wl_scripts.ajax_nonce,
        };

        $.ajax({
            type: "POST",
            data: data,
            dataType: "json",
            url: edd_wl_scripts.ajaxurl,
            success: function (response) {

                    // Add the new item to the cart widget
                    if ($('.cart_item.empty').length) {
                        $(response.cart_item).insertBefore('.cart_item.edd_subtotal');
                        $('.cart_item.edd_checkout, .cart_item.edd_subtotal').show();
                        $('.cart_item.empty').remove();
                    } else {
                        $(response.cart_item).insertBefore('.cart_item.edd_subtotal');
                    }

                    // update the subtotal
                    $('.cart_item.edd_subtotal span').html( response.subtotal );

                    // Update the cart quantity
                    $('span.edd-cart-quantity').each(function() {
                        var quantity = parseInt($(this).text(), 10) + 1;
                        $(this).text(quantity);
                    });

                    // Show the "number of items in cart" message
                    if ( $('.edd-cart-number-of-items').css('display') == 'none') {
                        $('.edd-cart-number-of-items').show('slow');
                    }

                    $('a.edd-add-to-cart-from-wish-list', container).toggle();
                    $('.edd-go-to-checkout-from-wish-list', container).css('display', 'inline-block');

                    if( response != 'incart' ) {
                        // Show the added message
                        $('.edd-cart-added-alert', container).fadeIn();
                        setTimeout(function () {
                            $('.edd-cart-added-alert', container).fadeOut();
                        }, 3000);
                    }
                    
         
            }
        }).fail(function (response) {
            console.log(response);
        }).done(function (response) {

        });

        return false;
    });

     // Send Remove from Wish List requests
    $('body').on('click.eddRemoveFromWishList', '.edd-remove-from-wish-list', function (e) {
     //   console.log('remove link clicked');

        e.preventDefault();

        var $this   = $(this),
            item    = $this.data('cart-item'),
            action  = $this.data('action'),
            id      = $this.data('download-id'),
            list_id = $this.data('list-id'),
            data   = {
                action: action,
                cart_item: item,
                list_id: list_id,
                nonce: edd_wl_scripts.ajax_nonce
            };

         $.ajax({
            type: "POST",
            data: data,
            dataType: "json",
            url: edd_wl_scripts.ajaxurl,
            success: function (response) {
                if ( response.removed ) {

                    if ( parseInt( edd_scripts.position_in_cart, 10 ) === parseInt( item, 10 ) ) {
                        window.location = window.location;
                        return false;
                    }
                    
                    if ( response.message ) {
                        // show message once all items have been removed
                        $('ul.edd-wish-list').parent().prepend( response.message );
                        
                        // remove add all to cart button
                        $('.edd-wl-add-all-to-cart').parent().remove();
                        // remove sharing
                        $('.edd-wl-sharing').remove();
                    }

                    // Remove the selected wish list item
                    var toRemove =  $('.edd-wish-list').find("[data-cart-item='" + item + "']").closest('.wl-row');

                    if ( toRemove ) {
                        toRemove.remove();
                    }
                    // backwards compatibility
                    else {
                         $('.edd-wish-list').find("[data-cart-item='" + item + "']").parent().parent().remove();
                    }

                   
                }
            }

        }).fail(function (response) {
            console.log(response);
        }).done(function (response) {

        });

        return false;
    });


    // opens the modal window when the add to wish list link is clicked 
    $('body').on('click.eddwlOpenModal', '.edd-wl-open-modal', function (e) {
        e.preventDefault();

        var $this = $(this), 
            form = $this.closest('form'); // get the closest form element
      
        var $spinner = $(this).find('.edd-loading');
        var container = $(this).closest('div');

        var spinnerWidth  = $spinner.width(),
        spinnerHeight = $spinner.height();

        // Show the spinner
        $(this).attr('data-edd-loading', '');

        $spinner.css({
            'margin-left': spinnerWidth / -2,
            'margin-top' : spinnerHeight / -2
        });

        var form            = jQuery('.edd_download_purchase_form');
        var download        = $this.data('download-id');
        var variable_price  = $this.data('variable-price');
        var price_mode      = $this.data('price-mode');
        var price_option    = $this.data('price-option');   // specified as shortcode parameter
        var item_price_ids  = [];
      
         // if price option manually set within shortcode
        if ( price_option >= 0 ) {
           item_price_ids[0] = price_option;

        } else if( variable_price == 'yes' ) {
            
            // might not need this
            if( ! $('.edd_price_option_' + download + ':checked', form).length  ) {
                 // hide the spinner
                $(this).removeAttr( 'data-edd-loading' );
                alert( edd_scripts.select_option );
                return;
            }

            // price ids
            $('.edd_price_option_' + download + ':checked', form).each(function( index ) {
                item_price_ids[ index ] = $(this).val();
            });
            
        } else {
            item_price_ids[0] = download;
        }

       

        var data = {
            action:     $(this).data('action'),
            post_id:    $(this).data('download-id'),
            price_ids:  item_price_ids,
            nonce:      edd_wl_scripts.ajax_nonce,
        };

        if ( price_option >= 0 ) {
          data['price_option_single'] = true;
        } 

        $.ajax({
            type:       "POST",
            data:       data,
            dataType:   "json",
            url:        edd_wl_scripts.ajaxurl,
            success: function (response) {
                // populate modal window with data
                $('#edd-wl-modal .modal-content').html( response.lists );

                $('.edd-wl-open-modal').removeAttr('data-edd-loading');

                $('a.edd-wl-open-modal').addClass('edd-has-js');

                // hide create list field
                if ( 'new-list' === $( 'input:radio[name=list-options]:checked' ).val() || response.list_count === 0 ) {
                    $('#list-name').show();
                    $('#list-status').show();
                    $('#user-lists').hide();
                    // check the radio input field
                    $('input:radio[name=list-options]').prop('checked', true);
                 
                } 
                else {
                    $('#list-name').hide();
                    $('#list-status').hide();
                }


                $('input:radio[name=list-options]').click(function () {
                    if ($(this).attr('id') === 'new-list') {
                        $('#list-name').show().focus();
                        $('#list-status').show();
                        $('#user-lists').hide();
                    } else {
                        $('#list-name').hide();
                        $('#list-status').hide();
                        $('#user-lists').show();
                    }
                });

                // load our modal window
                $('#edd-wl-modal').modal({
                    backdrop: 'static'
                });

            }
        })
        .fail(function (response) {
            console.log(response);
        })
        .done(function (response) {
            console.log(response);
        });

    });

    
    // Processes the add to wish list request. Creates a new list or stores downloads into existing list

    $('body').on('click.eddAddToWishList', '.edd-wl-save', function (e) {
    //    console.log( 'save link clicked');

        e.preventDefault();

        var $spinner        = $(this).find('.edd-loading');
        
        var spinnerWidth    = $spinner.width(),
        spinnerHeight       = $spinner.height();

        // Show the spinner
        $(this).attr('data-edd-loading', '');

        // center spinner
        $spinner.css({
            'margin-left': spinnerWidth / -2,
            'margin-top' : spinnerHeight / -2
        });

        var $this = $(this), 
            form = $this.closest('form'); // get the closest form element

        // set our form 
        var form = jQuery('.edd_download_purchase_form');

        var download       = $this.data('download-id');
        var variable_price = $this.data('variable-price');
        var price_mode     = $this.data('price-mode');
        var item_price_ids = [];

        // single_price_option mode (from shortcode)
        var single_price_option = $('input[name=edd-wl-single-price-option]').val();
        
        if ( single_price_option == 'yes' ) {
           item_price_ids[0] = $('input[name=edd-wish-lists-post-id]').val();
        }
        else if( variable_price == 'yes' ) {
            if( ! $('.edd_price_option_' + download + ':checked', form).length  ) {
                $(this).removeAttr( 'data-edd-loading' );
                alert( edd_scripts.select_option );
                return;
            }

            // get the price IDs from the hidden inputs, rather than the checkboxes    
            $('input[name=edd-wish-lists-post-id]').each(function( index ) {
                item_price_ids[ index ] = $(this).val();
            });

        } else {
            item_price_ids[0] = download;
        }
        

        if ( 'existing-list' == jQuery( 'input:radio[name=list-options]:checked' ).val() ) {
            list_id = jQuery('#user-lists').val();
        }


        var action          = $this.data('action'),
            list_id         = list_id,
            list_name       = jQuery( 'input[name=list-name]' ).val(),
            list_status     = jQuery( 'select[name=list-status]' ).val(),
            new_or_existing = jQuery( 'input:radio[name=list-options]:checked' ).val(), // whether we are adding to existing lightbox or creating a new one
            data            = {
                action: action,                     // edd_add_to_wish_list 
                download_id: download,              // our download ID
                list_id: list_id,                   // the list we're adding to
                price_ids : item_price_ids,         // item price IDs
                new_or_existing : new_or_existing,  // whether its a new list or existing. Could be true or false
                list_name : list_name,              // the list name entered by the user
                list_status : list_status,
                nonce: edd_wl_scripts.ajax_nonce      // nonce
            };

        $.ajax({
            type: "POST",
            data: data,
            dataType: "json",
            url: edd_wl_scripts.ajaxurl,
            success: function (response) {

                // hide the save button and show the close buttons
                $('.edd-wl-save').hide();
                $('.edd-wl-success').show();

                // show the success msg along with a link to the list item/s were added to
                $('.modal-body').html( response.success );

                // list was created
                if ( response.list_created == true ) {
                //    console.log( 'list created' );

                    // clear field
                    $('#list-name').val('');
                }
                
                // redirect to wish list if option is set
                if( edd_wl_scripts.redirect_to_wish_list == '1' ) {
                    window.location = edd_wl_scripts.wish_list_page;
                } 
                else {

                    if ( price_mode == 'multi' ) {
                        // remove spinner for multi
                        $this.removeAttr( 'data-edd-loading' );
                    }

                }
            }
        }).fail(function (response) {
            console.log(response);
        }).done(function (response) {

        });

        return false;
    });
    

});