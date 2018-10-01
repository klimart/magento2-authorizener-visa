define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (Component, rendererList) {
        'use strict';

        rendererList.push({
            type: 'codiluck_authorizenet',
            component: 'CodiLuck_AuthorizenetVisa/js/view/payment/method-renderer/visa-checkout'
        });

        return Component.extend({});
    }
);