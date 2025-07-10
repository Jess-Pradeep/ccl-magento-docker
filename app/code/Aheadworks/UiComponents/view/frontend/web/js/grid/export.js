/**
 * @api
 */
define([
    'jquery',
    'underscore',
    'Magento_Ui/js/grid/export'
], function ($, _, Element) {
    'use strict';

    return Element.extend({
        /**
         * Compose params object that will be added to request.
         *
         * @returns {Object}
         */
        getParams: function () {
            var result = this._super();

            if (this.exportUiElement) {
                result.exportUiElement = this.exportUiElement
            }

            return result;
        }
    });
});
