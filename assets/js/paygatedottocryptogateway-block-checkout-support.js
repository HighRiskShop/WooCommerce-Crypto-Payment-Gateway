( function( blocks, i18n, element, components, editor ) {
    const { registerPaymentMethod } = wc.wcBlocksRegistry;
    // Use the localized data from PHP
    const paygatedottocryptogateways = paygatedottocryptogatewayData || [];
    paygatedottocryptogateways.forEach( ( paygatedottocryptogateway ) => {
        registerPaymentMethod({
            name: paygatedottocryptogateway.id,
            label: paygatedottocryptogateway.label,
            ariaLabel: paygatedottocryptogateway.label,
            content: element.createElement(
                'div',
                { className: 'paygatedottocryptogateway-method-wrapper' },
                element.createElement( 
                    'div', 
                    { className: 'paygatedottocryptogateway-method-label' },
                    '' + paygatedottocryptogateway.description 
                ),
                paygatedottocryptogateway.icon_url ? element.createElement(
                    'img', 
                    { 
                        src: paygatedottocryptogateway.icon_url,
                        alt: paygatedottocryptogateway.label,
                        className: 'paygatedottocryptogateway-method-icon'
                    }
                ) : null
            ),
            edit: element.createElement(
                'div',
                { className: 'paygatedottocryptogateway-method-wrapper' },
                element.createElement( 
                    'div', 
                    { className: 'paygatedottocryptogateway-method-label' },
                    '' + paygatedottocryptogateway.description 
                ),
                paygatedottocryptogateway.icon_url ? element.createElement(
                    'img', 
                    { 
                        src: paygatedottocryptogateway.icon_url,
                        alt: paygatedottocryptogateway.label,
                        className: 'paygatedottocryptogateway-method-icon'
                    }
                ) : null
            ),
            canMakePayment: () => true,
        });
    });
} )(
    window.wp.blocks,
    window.wp.i18n,
    window.wp.element,
    window.wp.components,
    window.wp.blockEditor
);