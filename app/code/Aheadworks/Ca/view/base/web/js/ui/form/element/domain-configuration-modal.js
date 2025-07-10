define([
    'jquery',
    'Magento_Ui/js/modal/modal-component',
    'Aheadworks_Ca/js/model/company/domain/management'
], function ($, Modal, domainManagement) {
    "use strict";

    return Modal.extend({
        defaults: {
            saveUrl: '',
            defaultStatus: 'pending'
        },

        /**
         * Save action handler
         */
        actionSave: function () {
            var domainData, deferred, self = this;

            this.source.set('params.invalid', false);
            this.source.trigger('data.validate');
            if (!this.source.get('params.invalid')) {
                domainData = this.source.get('data.domain');
                domainData.company_id = this.source.get('data.company.id');
                domainData.status = this._prepareDomainStatus();
                deferred = domainManagement.saveDomain(domainData, this.saveUrl);
                $.when(deferred).done(function () {
                    self.closeModal();
                });
            }
        },

        /**
         * @inheritdoc
         */
        closeModal: function () {
            this._super();
            this.resetData();
        },

        /**
         * Reset data from provider
         */
        resetData: function () {
            this.elems().forEach(function(childElem) {
                if (childElem.elementType === 'fieldset') {
                    childElem.elems().forEach(function (field) {
                        field.reset();
                    }, this);
                }
            }, this);
        },

        /**
         * Prepare domain status
         *
         * @return {String}
         * @private
         */
        _prepareDomainStatus: function () {
            return !this.source.get('data.domain.status')
                ? this.defaultStatus
                : this.source.get('data.domain.status')
        }
    });
});
