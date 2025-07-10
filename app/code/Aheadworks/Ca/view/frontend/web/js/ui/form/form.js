define([
    'jquery',
    'underscore',
    'Magento_Ui/js/form/form',
], function ($, _, Component) {
    'use strict';

    return Component.extend({
        defaults: {
            extraFormSelector: ''
        },

        /**
         * @inheritdoc
         */
        initialize: function () {
            this._super()
                ._addFormKeyIfNotSet();

            return this;
        },

        /**
         * Validate and save form.
         *
         * Add fields data from extra form
         *
         * @param {String} redirect
         * @param {Object} data
         */
        save: function (redirect, data) {
            if (this.extraFormSelector) {
                let form = $(this.extraFormSelector),
                    formData = new FormData(form[0]);

                for(let pair of formData.entries()) {
                    this.source.set('data.' + pair[0], pair[1]);
                }
            }

            this._super(redirect, data);
        },

        /**
         * Validates each element and returns true, if all elements are valid.
         */
        validate: function () {
            this._super();
            if (this.extraFormSelector) {
                this.set('additionalInvalid', !$(this.extraFormSelector).valid());
            }
        },

        /**
         * Add form key to window object if form key is not added earlier
         * Used for submit request validation
         *
         * @returns {Form} Chainable
         */
        _addFormKeyIfNotSet: function () {
            if (!window.FORM_KEY) {
                window.FORM_KEY = $.mage.cookies.get('form_key');
            }
            return this;
        }
    });
});
