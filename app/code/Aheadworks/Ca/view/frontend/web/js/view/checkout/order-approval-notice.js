define([
    'uiComponent',
    'Magento_SalesRule/js/action/set-coupon-code',
    'Magento_SalesRule/js/action/cancel-coupon',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/url-builder',
    'mage/storage'
], function (
    Component,
    setCouponCodeAction,
    cancelCouponAction,
    quote,
    urlBuilder,
    storage
) {
    'use strict';

    var checkoutConfig = window.checkoutConfig,
        awCaConfig = checkoutConfig ? checkoutConfig.awCompanyAccounts : {};

    return Component.extend({
        defaults: {
            template: 'Aheadworks_Ca/checkout/order-approval-notice'
        },
        notice: '',

        /**
         * @inheritdoc
         */
        initialize: function () {
            var self = this;

            this._super();
            if (checkoutConfig && checkoutConfig.awCompanyAccounts) {
                setCouponCodeAction.registerSuccessCallback(function () {
                    self.updateVisibility();
                });
                cancelCouponAction.registerSuccessCallback(function () {
                    self.updateVisibility();
                });
            }

            return this;
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .observe({
                    isVisible: checkoutConfig.awCompanyAccounts && awCaConfig.isNoticeVisible
                });

            return this;
        },

        /**
         * Update notice visibility
         */
        updateVisibility: function () {
            var serviceUrl = urlBuilder.createUrl('/awCaCompany/orderApproval/mine/isApproveRequiredForCart', {}),
                payload = {
                    cartId: quote.getQuoteId()
                },
                self = this;

            storage.get(
                serviceUrl, JSON.stringify(payload)
            ).done(
                function (response) {
                    self.isVisible(response);
                }
            );
        }
    });
});
