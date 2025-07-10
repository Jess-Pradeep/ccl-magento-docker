define([
    "jquery",
    "mage/translate"
], function($){
    "use strict";

    $.widget('mage.awCreditLimitEntityImport', {
        options: {
            importInputSelector: '#entity_import_file',
            formKeySelector: 'input[name=form_key]',
            validateMessageSelector: '#entity_import_file_validate_message'
        },

        /**
         * Initialize widget
         */
        _create: function() {
            $(this.options.validateMessageSelector).hide();
            this._bind();
            this.replaceSampleFileUrl();
        },

        /**
         * Event binding
         */
        _bind: function () {
            var parent = this.element.parents('tbody');
            this._on({'click [data-role=import-button]': function () {this._sendForm();}});
            parent.on('change', '#aw_credit_limit_entity_import_entity', function() {
                this.replaceSampleFileUrl();
            }.bind(this));
        },

        /**
         * Replace Sample File Url
         */
        replaceSampleFileUrl: function() {
            var sampleFileSpan = jQuery('#sample-file-span'),
                value = $('#aw_credit_limit_entity_import_entity').val();
            if (value) {
                var sampleFileLink = this.options.sampleFilesBaseUrl.replace('entity-name', value);
                jQuery('#sample-file-link').attr('href', sampleFileLink);
            } else {
                sampleFileSpan.hide();
            }
        },

        /**
         * Send request
         */
        _sendForm: function () {
            if (this.isValid()) {
                var form = this.createForm();
                $('body').append(form);
                form.submit();
            }
        },

        /**
         * Is import input valid
         *
         * @returns {boolean}
         */
        isValid: function () {
            var importInput = $(this.options.importInputSelector);
            var messageBlock = $(this.options.validateMessageSelector);
            if (importInput.get(0).files.length === 0) {
                messageBlock.text($.mage.__('This is required field'));
                messageBlock.show();
                messageBlock.focus();
                return false;
            }
            return true;
        },

        /**
         * Create Form
         *
         * @returns {form}
         */
        createForm: function () {
            var form = $("<form>");
            form = this.prepareFormData(form);

            return form;
        },

        /**
         * Prepare Form Data
         *
         * @param form
         * @returns form
         */
        prepareFormData(form)
        {
            var parent = this.element.parents('tbody'),
                inputs = parent.find('[name*="[entity_import][fields]"]'),
                formKeyInput = $(this.options.formKeySelector),
                fileInput = parent.find(this.options.importInputSelector),
                name,
                input;

            form.attr(this.options.form);

            inputs.each(function(index, select) {
                name = this.convertSelectName(select);
                input = $('<input>').attr('type','hidden').attr('name',name).val($(select).val());

                form.append(input);
            }.bind(this));

            form.append(formKeyInput);
            form.append(fileInput);

            return form;
        },

        /**
         * Convert select name
         *
         * @param select
         * @returns {string}
         */
        convertSelectName: function (select) {
            var name = $(select).attr('name');

            return name.split("[")
                .join("")
                .split("]")
                .slice(2,-2)
                .join("");
        }
    });

    return $.mage.awCreditLimitEntityImport;
});
