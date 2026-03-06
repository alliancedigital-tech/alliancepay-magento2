define([
    'Magento_Checkout/js/view/payment/default',
    'Magento_Checkout/js/action/redirect-on-success',
], function (Component, redirectOnSuccessAction) {
    'use strict';
    return Component.extend({
        defaults: {
            template: 'Alliance_AlliancePay/payment/alliance_pay'
        },

        afterPlaceOrder: function () {
            redirectOnSuccessAction.redirectUrl = window.checkoutConfig.payment[this.getCode()].redirectUrl;
            this.redirectAfterPlaceOrder = true;
        },
    });
});
