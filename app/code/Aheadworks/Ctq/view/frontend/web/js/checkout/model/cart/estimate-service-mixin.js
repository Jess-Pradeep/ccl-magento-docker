define([
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/cart/cache',
], function(wrapper, cartCache) {
    'use strict';

    return function () {
        let tag = window.checkoutConfig['isQuoteList'] === true ? 'quoteList' : 'cart';
        let currentRates = cartCache.get('rates'), beforeRates = cartCache.get(tag + 'rates');
        if (Array.isArray(currentRates)) {
            currentRates.sort();
        }
        if (Array.isArray(beforeRates)) {
            beforeRates.sort();
        }
        if (JSON.stringify(currentRates) !== JSON.stringify(beforeRates)) {
            cartCache.set(tag + 'rates', cartCache.get('rates'))
            cartCache.clear('rates')
        }
    };
});
