define([
    'Magento_Catalog/js/price-utils',
    'Aheadworks_CreditLimit/js/payment/aw-credit-limit/placing-order-availability-checker',
    'Aheadworks_CreditLimit/js/payment/aw-credit-limit/limit-checker',
], function (priceUtils, canPlaceOrder, limitChecker) {
    "use strict";

    /**
     * Checkout configuration data
     */
    var paymentData = window.checkoutConfig.payment.aw_credit_limit,
        priceFormat = window.checkoutConfig.priceFormat;

    return {

        /**
         * Get customer available credit balance
         *
         * @return {Number}
         */
        getAvailableBalancePure: function () {
            return paymentData.credit_available
        },

        /**
         * Get payment period
         *
         * @return {Number}
         */
        getPaymentPeriod: function () {
            return paymentData.payment_period
        },

        /**
         * Is payment period expired
         *
         * @return {Boolean}
         */
        isPaymentPeriodExpired: function () {
            return paymentData.is_payment_period_expired
        },

        /**
         * Check if customer can place order
         *
         * @return {Number}
         */
        canPayForOrder: function () {
            return canPlaceOrder(paymentData);
        },

        /**
         * Check if credit limit can be exceeded
         *
         * @return {Boolean}
         */
        canExceedCreditLimit: function () {
            return paymentData.is_allowed_to_exceed
        },

        /**
         * Check if credit limit exceeded
         *
         * @return {Boolean}
         */
        isCreditLimitExceeded: function () {
            return limitChecker.isExceeded(paymentData);
        },

        /**
         * Get credit limit exceeded amount
         *
         * @return {Boolean}
         */
        getCreditLimitExceededAmountPure: function () {
            return limitChecker.getExceededAmount(paymentData);
        },

        /**
         * Get credit limit exceeded amount formatted
         *
         * @return {String}
         */
        getCreditLimitExceededAmountFormatted: function () {
            return this.formatPrice(this.getCreditLimitExceededAmountPure())
        },

        /**
         * Check if customer can place multishipping orders
         *
         * @return {Number}
         */
        canPayForMultishippingOrders: function (checkoutTotal) {
            return canPlaceOrder(paymentData, checkoutTotal);
        },

        /**
         * Get customer available credit balance formatted
         *
         * @return {String}
         */
        getAvailableBalanceFormatted: function () {
            return this.formatPrice(this.getAvailableBalancePure())
        },

        /**
         * Format price
         *
         * @return {String}
         */
        formatPrice: function(price) {
            return priceUtils.formatPrice(price, priceFormat);
        }
    }
});
