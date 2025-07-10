define([
    'ko',
    'Magento_Ui/js/grid/columns/multiselect'
], function (ko, MultiSelect) {
    'use strict';

    return MultiSelect.extend({
        defaults: {
            headerTmpl: 'Aheadworks_Ca/ui/grid/columns/empty-header',
            bodyTmpl: 'Aheadworks_Ca/ui/grid/columns/cells/single-select',
            preserveSelectionsOnFilter: true
        },

        /**
         * Initializes observable properties of instance
         *
         * @returns {Object} Chainable
         */
        initObservable: function () {
            this._super();

            this.singleSelected = ko.pureComputed({
                read: function () {
                    let selected = this.selected();
                    return selected.length > 0 ? selected[0] : undefined;
                },

                /**
                 * Validates input field prior to updating 'qty' property
                 */
                write: function (value) {
                    this.selected([value]);
                },

                owner: this
            });

            return this;
        }
    });
});
