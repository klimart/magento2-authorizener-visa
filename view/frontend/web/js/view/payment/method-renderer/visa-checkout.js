define([
    'Magento_Checkout/js/view/payment/default',
    'CodiLuck_AuthorizenetVisa/js/action/complete',
    'Magento_Checkout/js/model/full-screen-loader'
], function(Component, completeAction, fullScreenLoader) {
    'use strict';

    var config = window.checkoutConfig.payment.codiluck_authorizenet;

    return Component.extend({
        defaults: {
            template: 'CodiLuck_AuthorizenetVisa/payment/visa-checkout',
            code: 'codiluck_authorizenet',
            isClicked: false,
            responseData: null,
            redirectAfterPlaceOrder: false
        },

        initialize: function() {
            this._super();

            var self = this;

            window.onVisaCheckoutReady = function() {
                V.init(self.getVisaCheckoutInitialSettings());
                V.on("payment.success", function(payment) {
                    fullScreenLoader.startLoader();
                    self.responseData = payment;
                    self.placeOrder();
                });
                V.on("payment.cancel", self.onPaymentCancel.bind(this));
                V.on("payment.error", self.onPaymentError.bind(this));
            };
            require([config.sdkUrl]);
        },

        getData: function() {
            return {
                'method': this.getCode(),
                'additional_data': null
            }
        },

        onPaymentCancel: function(self) {
            console.log('inside Payment Cancel method');
            console.log(self);
        },

        onPaymentError: function(self) {
            console.log('inside Payment Error method');
            console.log(self);
        },

        getTitle: function() {
            return config.title;
        },

        getCode: function() {
            return this.code;
        },

        isActive: function() {
            return this.getCode() === this.isChecked();
        },

        getPaymentCardSrc: function() {
            return config.paymentCardSrc;
        },

        getVisaCheckoutButtonSrc: function() {
            return config.visaCheckoutButtonSrc;
        },

        getVisaCheckoutInitialSettings: function() {
            return config.visaCheckoutInitialSettings;
        },

        afterPlaceOrder: function() {
            completeAction.execute(this.responseData, this.messageContainer);
        }
    });
});