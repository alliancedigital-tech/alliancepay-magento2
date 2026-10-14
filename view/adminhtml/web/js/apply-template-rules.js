define([
    'jquery',
    'uiElement'
], function ($, Element) {
    'use strict';

    return Element.extend({
        defaults: {
            templateId: '',
            productId: '',
            url: '',
            priceUrl: '',
            productPrice: null,
            rulesDataScope: 'data.rules',
            visible: false,
            listens: {
                templateId: 'applyTemplateRules',
                productId: 'applyProductPrice'
            },
            imports: {
                templateId: '${ $.provider }:data.template_id',
                productId: '${ $.provider }:data.product_id'
            }
        },

        applyTemplateRules: function (templateId) {
            if (!templateId) {
                return;
            }

            $.getJSON(this.url, {id: templateId}).done(function (response) {
                var rules = (response.rules_template || []).map(function (rule) {
                    return {
                        paymentNumberFrom: rule.paymentNumberFrom,
                        paymentNumberTo: rule.paymentNumberTo,
                        intervalValue: rule.intervalValue,
                        intervalUnit: rule.intervalUnit,
                        coinAmount: this.productPrice !== null ? this.productPrice : '',
                        currency: '980'
                    };
                }.bind(this));

                this.source.set(this.rulesDataScope, rules);
            }.bind(this));
        },

        applyProductPrice: function (productId) {
            if (!productId) {
                this.productPrice = null;
                return;
            }

            $.getJSON(this.priceUrl, {id: productId}).done(function (response) {
                var rules;

                if (response.price === null || typeof response.price === 'undefined') {
                    return;
                }

                this.productPrice = Math.round(response.price * 100);
                rules = this.source.get(this.rulesDataScope) || [];

                this.source.set(this.rulesDataScope, rules.map(function (rule) {
                    return $.extend({}, rule, {coinAmount: this.productPrice});
                }.bind(this)));
            }.bind(this));
        }
    });
});
