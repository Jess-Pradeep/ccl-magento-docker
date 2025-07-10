define([
    'mage/utils/wrapper',
    'Magento_Customer/js/model/customer'
], function(wrapper, customer) {
    'use strict';

    return function (urlManager) {

        urlManager.getUrlForEstimationShippingMethodsForNewAddress = wrapper.wrap(urlManager.getUrlForEstimationShippingMethodsForNewAddress, function(original, quote) {
            if (customer.isLoggedIn() && window.checkoutConfig['isQuoteList'] === true) {
                let urls = {
                    'customer': '/awCtqQuoteList/carts/mine/estimate-shipping-methods'
                };
                return urlManager.getUrl(urls, {});
            }
            return original(quote);
        });

        urlManager.getUrlForTotalsEstimationForNewAddress = wrapper.wrap(urlManager.getUrlForTotalsEstimationForNewAddress, function(original, quote) {
            if (customer.isLoggedIn() && window.checkoutConfig['isQuoteList'] === true) {
                let urls = {
                    'customer': '/awCtqQuoteList/carts/mine/totals-information'
                };
                return urlManager.getUrl(urls, {});
            }
            return original(quote);
        });

        return urlManager;
    };
});
