define([
    'Magento_Ui/js/grid/columns/column',
    'mage/utils/objects'
], function (Column, utils) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Aheadworks_Ca/ui/grid/columns/company/domain/cells/edit-link-action',
            modules: {
                modalDataProvider: '${ $.modalDataProvider }',
                modalComponent: '${ $.modalComponent }'
            }
        },

        /**
         * Edit item
         *
         * @param {Object} item
         */
        editItem: function (item) {
            this.modalDataProvider().set('data.domain', utils.copy(item));
            this.modalComponent().toggleModal();
        }
    });
});
