// Function to get URL parameter from the current script's URL
function highriskshopcryptogateway_getScriptParameter(name) {
    let highriskshopcryptogateway_scripts = document.getElementsByTagName('script');
    for (let highriskshopcryptogateway_script of highriskshopcryptogateway_scripts) {
        if (highriskshopcryptogateway_script.src.includes('highriskshopcryptogateway-payment-status-check.js')) {
            let highriskshopcryptogateway_params = new URL(highriskshopcryptogateway_script.src).searchParams;
            return highriskshopcryptogateway_params.get(name);
        }
    }
    return null;
}

jQuery(document).ready(function($) {
    function highriskshopcryptogateway_payment_status() {
        let highriskshopcryptogateway_order_id = highriskshopcryptogateway_getScriptParameter('order_id');
        let highriskshopcryptogateway_nonce = highriskshopcryptogateway_getScriptParameter('nonce');
        let highriskshopcryptogateway_tickerstring = highriskshopcryptogateway_getScriptParameter('tickerstring');

        $.ajax({
            url: '/wp-json/highriskshopcryptogateway/v1/highriskshopcryptogateway-check-order-status-' + highriskshopcryptogateway_tickerstring + '/',
            method: 'GET',
            data: {
                order_id: highriskshopcryptogateway_order_id,
                nonce: highriskshopcryptogateway_nonce
            },
            success: function(response) {
                if (response.status === 'processing' || response.status === 'completed') {
                    $('#highriskshop-payment-status-message').text('Payment received')
                        .removeClass('highriskshopcryptogateway-unpaid')
                        .addClass('highriskshopcryptogateway-paid');
                    $('#highriskshopcryptogateway-wrapper').remove();
                } else if (response.status === 'failed') {
                    $('#highriskshop-payment-status-message').text('Payment failed, you may have sent incorrect amount or token. Contact support')
                        .removeClass('highriskshopcryptogateway-unpaid')
                        .addClass('highriskshopcryptogateway-failed');
                    $('#highriskshopcryptogateway-wrapper').remove();
                } else {
                    $('#highriskshop-payment-status-message').text('Waiting for payment');
                }
            },
            error: function() {
                $('#highriskshop-payment-status-message').text('Error checking payment status. Please refresh the page.');
            }
        });
    }

    setInterval(highriskshopcryptogateway_payment_status, 60000);
});
