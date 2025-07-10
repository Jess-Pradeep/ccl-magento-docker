define([
    'Magento_Checkout/js/model/totals',
    'underscore'
], function (totals, _) {
    "use strict";

    return function (paymentData, checkoutTotal) {
        var grandTotal = !_.isUndefined(checkoutTotal)
            ? checkoutTotal
            : totals.getSegment('grand_total').value;

        if (paymentData.is_payment_period_expired) {
            return false;
        }

        if (paymentData.is_allowed_to_exceed) {
            return true;
        }

        return paymentData.credit_available >= grandTotal;
    }
});
