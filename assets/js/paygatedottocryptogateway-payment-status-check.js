// Function to get URL parameter from the current script's URL
function paygatedottocryptogateway_getScriptParameter(name) {
    let paygatedottocryptogateway_scripts = document.getElementsByTagName('script');
    for (let paygatedottocryptogateway_script of paygatedottocryptogateway_scripts) {
        if (paygatedottocryptogateway_script.src.includes('paygatedottocryptogateway-payment-status-check.js')) {
            let paygatedottocryptogateway_params = new URL(paygatedottocryptogateway_script.src).searchParams;
            return paygatedottocryptogateway_params.get(name);
        }
    }
    return null;
}

jQuery(document).ready(function($) {
    function paygatedottocryptogateway_payment_status() {
        let paygatedottocryptogateway_order_id = paygatedottocryptogateway_getScriptParameter('order_id');
        let paygatedottocryptogateway_nonce = paygatedottocryptogateway_getScriptParameter('nonce');
        let paygatedottocryptogateway_tickerstring = paygatedottocryptogateway_getScriptParameter('tickerstring');

        $.ajax({
            url: '/wp-json/paygatedottocryptogateway/v1/paygatedottocryptogateway-check-order-status-' + paygatedottocryptogateway_tickerstring + '/',
            method: 'GET',
            data: {
                order_id: paygatedottocryptogateway_order_id,
                nonce: paygatedottocryptogateway_nonce
            },
            success: function(response) {
                if (response.status === 'processing' || response.status === 'completed') {
                    $('#paygatedotto-payment-status-message').text('Payment received')
                        .removeClass('paygatedottocryptogateway-unpaid')
                        .addClass('paygatedottocryptogateway-paid');
                    $('#paygatedottocryptogateway-wrapper').remove();
                } else if (response.status === 'failed') {
                    $('#paygatedotto-payment-status-message').text('Payment failed, you may have sent incorrect amount or token. Contact support')
                        .removeClass('paygatedottocryptogateway-unpaid')
                        .addClass('paygatedottocryptogateway-failed');
                    $('#paygatedottocryptogateway-wrapper').remove();
                } else {
                    $('#paygatedotto-payment-status-message').text('Waiting for payment');
                }
            },
            error: function() {
                $('#paygatedotto-payment-status-message').text('Error checking payment status. Please refresh the page.');
            }
        });
    }

    setInterval(paygatedottocryptogateway_payment_status, 60000);
});
