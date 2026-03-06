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
                type: 'alliance_pay',
                component: 'Alliance_AlliancePay/js/view/payment/method-renderer/alliancePay',
            },
        );
        return Component.extend({});
    }
);
