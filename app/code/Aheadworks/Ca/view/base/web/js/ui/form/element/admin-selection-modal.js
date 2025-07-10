define([
    'jquery',
    'Magento_Ui/js/modal/modal-component',
    'Aheadworks_Ca/js/action/show-error-popup',
    'Magento_Ui/js/modal/confirm',
    'mageUtils'
], function ($, Modal, showError, confirm, utils) {
    "use strict";

    return Modal.extend({
        defaults: {
            changeUrl: '',
            changeConfirmationMessage: ''
        },

        /**
         * Initialize component
         */
        initialize: function () {
            this._super()
                ._addFormKeyIfNotSet();

            return this;
        },

        /**
         * Add form key to window object if form key is not added earlier
         * Used for submit request validation
         *
         * @returns {Object} Chainable
         */
        _addFormKeyIfNotSet: function () {
            if (!window.FORM_KEY) {
                window.FORM_KEY = $.mage.cookies.get('form_key');
            }
            return this;
        },

        /**
         * Select action handler
         */
        actionSelect: function () {
            let newAdminSelected =  this.source.get('data.new_admin_selected'),
                self = this;

            if (newAdminSelected.length !== 1) {
                showError('Please, select new company admin');
            } else {
                confirm({
                    content: this.changeConfirmationMessage,
                    actions: {
                        confirm: function () {
                            self.closeModal();
                            utils.submit({
                                'url': self.changeUrl,
                                'data': {
                                    company_id: self.source.get('data.company.id'),
                                    new_company_admin_id: newAdminSelected[0].entity_id,
                                }
                            });
                        }
                    }
                });
            }
        }
    });
});
