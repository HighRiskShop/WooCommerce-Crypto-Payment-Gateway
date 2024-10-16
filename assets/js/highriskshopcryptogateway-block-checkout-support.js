( function( blocks, i18n, element, components, editor ) {
    const { registerPaymentMethod } = wc.wcBlocksRegistry;
    // Use the localized data from PHP
    const highriskshopcryptogateways = highriskshopcryptogatewayData || [];
    highriskshopcryptogateways.forEach( ( highriskshopcryptogateway ) => {
        registerPaymentMethod({
            name: highriskshopcryptogateway.id,
            label: highriskshopcryptogateway.label,
            ariaLabel: highriskshopcryptogateway.label,
            content: element.createElement(
                'div',
                { className: 'highriskshopcryptogateway-method-wrapper' },
                element.createElement( 
                    'div', 
                    { className: 'highriskshopcryptogateway-method-label' },
                    '' + highriskshopcryptogateway.description 
                ),
                highriskshopcryptogateway.icon_url ? element.createElement(
                    'img', 
                    { 
                        src: highriskshopcryptogateway.icon_url,
                        alt: highriskshopcryptogateway.label,
                        className: 'highriskshopcryptogateway-method-icon'
                    }
                ) : null
            ),
            edit: element.createElement(
                'div',
                { className: 'highriskshopcryptogateway-method-wrapper' },
                element.createElement( 
                    'div', 
                    { className: 'highriskshopcryptogateway-method-label' },
                    '' + highriskshopcryptogateway.description 
                ),
                highriskshopcryptogateway.icon_url ? element.createElement(
                    'img', 
                    { 
                        src: highriskshopcryptogateway.icon_url,
                        alt: highriskshopcryptogateway.label,
                        className: 'highriskshopcryptogateway-method-icon'
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