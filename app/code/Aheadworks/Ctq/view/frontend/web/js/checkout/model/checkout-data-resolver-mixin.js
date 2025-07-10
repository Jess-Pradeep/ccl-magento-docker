define([
    'mage/utils/wrapper',
    'Magento_Customer/js/model/address-list',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/checkout-data',
    'Magento_Checkout/js/action/create-shipping-address',
], function(wrapper, addressList, quote, checkoutData, createShippingAddress) {
    'use strict';

    return function (dataResolver) {

        dataResolver.resolveShippingAddress = wrapper.wrap(dataResolver.resolveShippingAddress, function(original) {
            if (window.checkoutConfig.isShippingAddressOverridden) {
                var newShippingAddress;

                if (addressList().length !== 0) {
                    newShippingAddress = createShippingAddress(window.checkoutConfig.awCtqShippingAddressFromData);
                    checkoutData.setNewCustomerShippingAddress(window.checkoutConfig.awCtqShippingAddressFromData);
                    checkoutData.setShippingAddressFromData(window.checkoutConfig.awCtqShippingAddressFromData);
                    checkoutData.setSelectedShippingAddress(newShippingAddress.getKey())
                } else {
                    checkoutData.setShippingAddressFromData(window.checkoutConfig.awCtqShippingAddressFromData);
                }
            }
            return original();
        });

        return dataResolver;
    };
});
