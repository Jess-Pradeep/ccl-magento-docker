var config = {
    map: {
        '*': {
            awCtqButtonControl: 'Aheadworks_Ctq/js/button-control',
            awCtqAddToQuoteListButton: 'Aheadworks_Ctq/js/product/quote-list/add',
            awCtqSorting: 'Aheadworks_Ctq/js/customer/quote/items/sorting',
        }
    },
    config: {
        mixins: {
            'Magento_Checkout/js/model/checkout-data-resolver': {
                'Aheadworks_Ctq/js/checkout/model/checkout-data-resolver-mixin': true
            },
            'Magento_Checkout/js/sidebar': {
                'Aheadworks_Ctq/js/checkout/sidebar-mixin': true
            },
            'mage/validation': {
                'Aheadworks_Ctq/js/validation-mixin': true
            },
            'Magento_Checkout/js/model/resource-url-manager': {
                'Aheadworks_Ctq/js/checkout/model/resource-url-manager-mixin': true
            },
            'Magento_Checkout/js/model/cart/estimate-service': {
                'Aheadworks_Ctq/js/checkout/model/cart/estimate-service-mixin': true
            }
        }
    },
};
