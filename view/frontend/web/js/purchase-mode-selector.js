define([
    'jquery'
], function ($) {
    'use strict';

    var storageKey = 'alliance_purchase_mode';

    /**
     * @param {String} mode
     */
    function applyPaymentMethodHint(mode) {
        require(['Magento_Checkout/js/checkout-data'], function (checkoutData) {
            checkoutData.setSelectedPaymentMethod(mode === 'subscribe' ? 'alliance_pay_subscription' : 'alliance_pay');
        });
    }

    /**
     * @returns {String}
     */
    function readStoredMode() {
        try {
            return localStorage.getItem(storageKey) || 'onetime';
        } catch (e) {
            return 'onetime';
        }
    }

    /**
     * @param {String} mode
     */
    function storeMode(mode) {
        try {
            localStorage.setItem(storageKey, mode);
        } catch (e) {
            return;
        }
    }

    $.widget('mage.alliancePurchaseModeSelector', {
        _create: function () {
            var storedMode = readStoredMode(),
                radio = this.element.find('input[name="alliance_purchase_mode"][value="' + storedMode + '"]');

            if (radio.length) {
                radio.prop('checked', true);
            }

            this._togglePlanField(storedMode);
            applyPaymentMethodHint(storedMode);

            this.element.on('change', 'input[name="alliance_purchase_mode"]', function (event) {
                var mode = $(event.target).val();

                storeMode(mode);
                this._togglePlanField(mode);
                applyPaymentMethodHint(mode);
            }.bind(this));
        },

        _togglePlanField: function (mode) {
            var planField = this.element.find('select[name="alliance_plan_id"]'),
                planContainer = planField.closest('.alliance-purchase-plan');

            if (!planField.length) {
                return;
            }

            planField.prop('disabled', mode !== 'subscribe');
            planContainer.toggle(mode === 'subscribe');
        }
    });

    return $.mage.alliancePurchaseModeSelector;
});
