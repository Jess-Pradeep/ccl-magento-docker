define([
    'jquery'
], function ($) {
    'use strict';

    var awGiftCardPreviewWidgetMixin = {
        /**
         * Initialize widget
         */
        _create: function () {
            if ($('body').hasClass('aw_quick_order-index-index')) {
                this.options.formSelector = '#aw-qo-configure-item-form';
            }

            this._super();
        }
    };

    return function (targetWidget) {
        $.widget('mage.awGiftCardPreview', targetWidget, awGiftCardPreviewWidgetMixin);

        return $.mage.awGiftCardPreview;
    };
});