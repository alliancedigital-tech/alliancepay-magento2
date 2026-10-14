define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/payment/renderer-list'
    ],
    function (
        Component,
        rendererList
    ) {
        'use strict';
        rendererList.push(
            {
                type: 'alliance_pay_subscription',
                component: 'Alliance_AlliancePay/js/view/payment/method-renderer/alliancePaySubscription',
            },
        );
        return Component.extend({});
    }
);
