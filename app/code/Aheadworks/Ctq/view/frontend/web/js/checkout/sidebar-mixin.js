define([
    'jquery',
    'underscore',
    'Magento_Customer/js/customer-data'
], function($, _, customerData) {
    'use strict';

    return function (targetWidget) {
        return $.widget('mage.sidebar', $.mage.sidebar, {
            options: {
                quoteListUrl: ''
            },

            /**
             * Resolve cart type and return product ID
             *
             * @param {Number} productId
             * @returns {Object|undefined}
             * @private
             */
            _getProductById: function (productId) {
                if (this.options.quoteListUrl) {
                    return _.find(customerData.get('quote-list')().items, function (item) {
                        return productId === Number(item['item_id']);
                    });
                } else {
                    return this._super(productId);
                }
            },

            /**
             * Resolve cart and update content after update qty
             *
             * @param {Object} elem
             */
            _updateItemQtyAfter: function (elem) {
                if (this.options.quoteListUrl) {
                    var product = this._getProductById(Number(elem.data('cart-item')));
                    if (!_.isUndefined(product) && window.location.href === this.options.quoteListUrl) {
                        window.location.reload(false);
                    }
                    this._hideItemButton(elem);
                } else {
                    this._super(elem);
                }
            },

            /**
             * Resolve cart and update content after item remove
             *
             * @param {Object} elem
             * @private
             */
            _removeItemAfter: function (elem) {
                if (this.options.quoteListUrl) {
                    var product = this._getProductById(Number(elem.data('cart-item')));
                    if (!_.isUndefined(product) && window.location.href.indexOf(this.options.quoteListUrl) === 0) {
                        window.location.reload(false);
                    }
                } else {
                    this._super(elem);
                }
            }
        });
    }
});