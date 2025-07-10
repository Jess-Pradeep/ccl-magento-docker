define([
    'jquery',
    'uiCollection',
], function ($, Collection) {
    'use strict';

    return Collection.extend({
        defaults: {
            modules: {
                formId: '',
                mainForm: 'awCaForm'
            }
        },

        /**
         * Submit extra form
         *
         * Since main form is UI, we have to create real extra form
         * with explicit form tag to include reCaptcha fields
         */
        submit: function () {
            var form = $('#' + this.formId);
            form.submit();
        },

        /**
         * On submit handler
         *
         * Delegate submit to main form
         */
        onSubmit: function () {
            this.mainForm().save();
        }
    });
});