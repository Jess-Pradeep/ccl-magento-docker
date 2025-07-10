define([
    'Aheadworks_Ctq/js/customer/quote/totals/default'
], function (Component) {
    "use strict";

    return Component.extend({
        options: {
            isNeedPriceRender: false
        },

        /**
         * Is display shipping totals
         *
         * @return {Boolean}
         */
        isDisplayed: function() {
            return this.isNeedPriceRender;
        }
    });
});
