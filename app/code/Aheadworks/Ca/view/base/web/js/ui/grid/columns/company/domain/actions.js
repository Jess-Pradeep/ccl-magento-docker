define([
    'Magento_Ui/js/grid/columns/column',
    'Magento_Ui/js/modal/confirm',
    'Aheadworks_Ca/js/model/company/domain/management',
], function (Column, confirm, domainManagement) {
    'use strict';

    return Column.extend({
        defaults: {
            deleteUrl: '',
            saveUrl: '',
            deleteConfirmationMessage: '',
            adminType: 'backend'
        },

        /**
         * Delete item
         *
         * @param {Object} item
         */
        deleteItem: function (item) {
            var self = this;

            confirm({
                content: this.deleteConfirmationMessage,
                actions: {
                    confirm: function () {
                        domainManagement.deleteDomain(item, self.deleteUrl);
                    }
                }
            });
        },

        /**
         * Check whether status action is visible
         *
         * @param {Object} item
         * @return {Boolean}
         */
        isStatusActionVisible: function(item) {
            return this.adminType === 'frontend' && item.status !== 'pending';
        },

        /**
         * Change status
         *
         * @param {Object} item
         * @param {String} status
         */
        changeStatus: function (item, status) {
            var self = this;

            item.status = status;
            domainManagement.saveDomain(item, self.saveUrl)
        },

        /**
         * Check whether domain can be enabled
         *
         * @param {Object} item
         * @return {Boolean}
         */
        isAllowedToActivate: function(item) {
            return item.status === 'inactive';
        },

        /**
         * Check whether domain can be disabled
         *
         * @param {Object} item
         * @return {Boolean}
         */
        isAllowedToDeactivate: function(item) {
            return item.status === 'active';
        }
    });
});
