define(['uiComponent'], function (Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_Ctq/components/message'
        },

        /**
         * @param {Object} items
         * @return {null}
         */
        getMessage: function (item) {
            return item.minQuoteSubtotalMessage;
        }
    });
});