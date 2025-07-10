define([
    'Magento_Ui/js/grid/columns/column',
    'Magento_Ui/js/modal/confirm',
    'mageUtils'
], function (Column, confirm, utils) {
    'use strict';

    return Column.extend({
        defaults: {
            approveUrl: '',
            declineUrl: '',
            confirmationMessage: '',
        },

        /**
         * Approve candidate
         *
         * @param {Object} item
         */
        approveCandidate: function (item) {
            let self = this;

            confirm({
                content: this.confirmationMessage,
                actions: {
                    confirm: function () {
                        self.submit(self.approveUrl, item['id']);
                    }
                }
            });
        },

        /**
         * Reject candidate
         *
         * @param {Object} item
         */
        declineCandidate: function (item) {
            let self = this;

            confirm({
                content: this.confirmationMessage,
                actions: {
                    confirm: function () {
                        self.submit(self.declineUrl, item['id']);
                    }
                }
            });
        },

        /**
         * Submit request
         *
         * @param {String} url
         * @param {String} candidateId
         */
        submit: function (url, candidateId) {
            utils.submit({
                'url': url,
                'data': {
                    candidate_id: candidateId
                }
            });
        }
    });
});
