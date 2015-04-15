<?php
/**
 * This is the actual implementation of the Stripe gateway.
 * @since 3.3
 */
class WPBDP_2Checkout_Gateway extends WPBDP_Payment_Gateway {

    public function get_id() {
        return '2checkout';
    }

    public function get_name() {
        return __( '2Checkout', 'wpbdp-2checkout' );
    }

    public function get_supported_currencies() {
        return array( 'ARS', 'AUD', 'BRL', 'GBP', 'CAD', 'DKK', 'EUR', 'HKD', 'INR', 'ILS', 'JPY', 'LTL', 'MYR',
                      'MXN', 'NZD', 'NOK', 'PHP', 'RON', 'RUB', 'SGD', 'ZAR', 'SEK', 'CHF', 'TRY', 'AED', 'USD' );
    }    

    public function get_integration_method() {
        return WPBDP_Payment_Gateway::INTEGRATION_BUTTON;
    }

    public function register_config( &$settings ) {
        $s = $settings->add_section( 'payment',
                                     '2checkout',
                                     _x( '2Checkout Gateway Settings', 'admin settings', 'WPBDM' ) );
        $settings->add_setting( $s,
                                '2checkout',
                                _x( 'Activate 2Checkout?', 'admin settings', 'WPBDM' ),
                                'boolean',
                                false );
        
        $settings->add_setting( $s,
                                '2checkout-seller',
                                _x( '2Checkout seller/vendor ID', 'admin settings', 'WPBDM' ) );
        $settings->register_dep( '2checkout-seller', 'requires-true', '2checkout' );
    }

    public function validate_config() {
        if ( ' ' == trim( wpbdp_get_option( '2checkout-seller') ) )
            return array( __( '2Checkout seller/vendor ID missing.', 'wpbdp-2checkout' ) );
    }

    public function render_integration( &$payment ) {
        $html  = '';
        $html .= '<form action="https://www.2checkout.com/checkout/purchase" method="POST">';
        $html .= sprintf( '<input type="hidden" name="sid" value="%s" />', wpbdp_get_option( '2checkout-seller' ) );
        $html .= '<input type="hidden" name="mode" value="2CO" />';
        $html .= sprintf( '<input type="hidden" name="merchant_order_id" value="%s" />', $payment->get_id() );
        $html .= '<input type="hidden" name="pay_method" value="CC" />';
        $html .= sprintf( '<input type="hidden" name="x_receipt_link_url" value="%s" />', $this->get_url( $payment, 'process') );

        if ( wpbdp_get_option( 'payments-test-mode' ) )
            $html .= '<input type="hidden" name="demo" value="Y" />';

        $n = 0;
        foreach ( $payment->get_items() as $item ) {
            $html .= '<input type="hidden" name="li_' . $n . '_type" value="product" />';
            $html .= sprintf( '<input type="hidden" name="li_%d_name" value="%s" />', $n, esc_attr( $item->description ) );
            $html .= '<input type="hidden" name="li_' . $n . '_quantity" value="1" />';
            $html .= '<input type="hidden" name="li_' . $n . '_tangible" value="N" />';
            $html .= sprintf( '<input type="hidden" name="li_%d_price" value="%s" />', $n, number_format( $item->amount, 2, '.', '' ) );

            $n++;
        }

        $html .= sprintf( '<input type="image" src="%s" border="0" name="submit" alt="%s" />',
                          plugins_url( 'twocheckoutbuynow.gif', __FILE__ ),
                          __( 'Pay with 2Checkout', 'wpbdp-2checkout' )
                        );
        $html .= '</form>';

        return $html;
    }

    public function process( &$payment, $action ) {
        if ( 'process' !== $action || ! $payment->is_pending() )
            return;

        // TODO: use 'key' for validation (see https://www.2checkout.com/documentation/checkout/passback/validation).
        $payment->set_payer_info( 'first_name', trim( wpbdp_getv( $_REQUEST, 'first_name', '' ) ) );
        $payment->set_payer_info( 'last_name', trim( wpbdp_getv( $_REQUEST, 'last_name', '' ) ) );
        $payment->set_payer_info( 'country', trim( wpbdp_getv( $_REQUEST, 'country', '' ) ) );
        $payment->set_payer_info( 'email', trim( wpbdp_getv( $_REQUEST, 'email', '' ) ) );
        $payment->set_payer_info( 'phone', trim( wpbdp_getv( $_REQUEST, 'phone', '' ) ) );
        $payment->set_data( 'gateway_order', trim( wpbdp_getv( $_REQUEST, 'order_number', '' ) ) );

        if ( 'Y' == wpbdp_getv( $_REQUEST, 'credit_card_processed', 'K' ) )
            $payment->set_status( WPBDP_Payment::STATUS_COMPLETED, WPBDP_Payment::HANDLER_GATEWAY );
        else
            $payment->set_status( WPBDP_Payment::STATUS_REJECTED, WPBDP_Payment::HANDLER_GATEWAY );

        $payment->save();

        wp_redirect( $payment->get_redirect_url() );
    }

}
