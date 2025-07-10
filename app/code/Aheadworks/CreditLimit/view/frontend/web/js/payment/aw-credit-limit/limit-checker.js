define([
    'Magento_Checkout/js/model/totals',
    'Magento_Checkout/js/model/quote'
], function (totals, quote) {
    "use strict";

    return {

        /**
         * Check if available limit is exceeded
         *
         * @return {Boolean}
         */
        isExceeded: function (paymentData) {
            return this.getGrandTotal() > paymentData.credit_available;
        },

        /**
         * Get exceeded amount
         *
         * @return {Number}
         */
        getExceededAmount: function (paymentData) {
            return this.getGrandTotal() - paymentData.credit_available;
        },

        /**
         * Retrieve grand total
         */
        getGrandTotal: function() {
            var grandTotal = 0;

            if (quote.getTotals()) {
                grandTotal = totals.getSegment('grand_total').value;
            }

            return grandTotal;
        }
    }
});
