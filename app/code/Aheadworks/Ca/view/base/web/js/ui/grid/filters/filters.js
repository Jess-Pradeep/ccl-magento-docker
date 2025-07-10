define([
    'Magento_Ui/js/grid/filters/filters',
    'mageUtils',
    'underscore'
], function (Filters, utils, _) {
    'use strict';

    /**
     * Removes empty properties from the provided object.
     *
     * @param {Object} data - Object to be processed.
     * @returns {Object}
     */
    function removeEmpty(data) {
        var result = utils.mapRecursive(data, utils.removeEmptyValues.bind(utils));

        return utils.mapRecursive(result, function (value) {
            return _.isString(value) ? value.trim() : value;
        });
    }

    return Filters.extend({

        /**
         * @inheritdoc
         */
        apply: function () {
            this.set('applied', removeEmpty(this.filters));

            return this;
        }
    });
});
