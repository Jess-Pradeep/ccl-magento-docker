define([
    'jquery',
    'uiComponent'
], function ($, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            template: 'Aheadworks_Ctq/components/buyer/request-quote-link',
            minQuoteSubtotalMessage: null,
            isAllowed: true,
            modules: {
                awCtqRequestQuote: 'awCtqRequestQuoteParent.awCtqRequestQuote'
            }
        },

        /**
         * Open modal action
         */
        openModal: function () {
            this.awCtqRequestQuote().openModal();
        }
    });
});