define
([
    'jquery',
    'jquery/ui',
    'Aheadworks_Ctq/js/quote/edit/form'
], function ($) {
    'use strict';

    $.widget('mage.awCtqCustomShippingSelector', {

        options: {
            errorMessage: '',
        },

        /**
         * Create widget
         */
        _create: function () {
            var self = this,
                radioElement = this.element,
                inputElement = $('#aw_cqt_shipping_price');
                if (radioElement.is(':checked')) {
                    inputElement.show();
                }

                radioElement.on('change', self.onRadioButtonChange.bind(self, radioElement, inputElement));
                inputElement.on('change', self.applyPrice.bind(self, radioElement, inputElement));
        },

        /**
         * Callback handler on radio button change
         *
         * @param {Object} radioElement
         * @param {Object} inputElement
         * @public
         */
        onRadioButtonChange: function (radioElement, inputElement) {
            inputElement.val('')
            inputElement.show();
        },

        /**
         * Callback handler on price change complete to apply price
         *
         * @public
         * @param radioElement
         * @param inputElement
         */
        applyPrice: function (radioElement, inputElement) {
            if (!this._validateField(inputElement)) {
                quote.setShippingMethod(
                    radioElement.val(),
                    inputElement.val()
                )
            }
        },

        /**
         * Validate field
         *
         * @param {Object} inputElement
         * @returns {Boolean}
         * @private
         */
        _validateField: function (inputElement) {
            var value = inputElement.val(),
                result;

            result = this._validateValue(value);
            if (result) {
                this.options.errorMessage = this.options.errorText.price;
                this.showErrorMessage(this.options.errorMessage);
            }
            return result;
        },

        /**
         * Validate value
         *
         * @param {String} value
         * @returns {Boolean}
         * @private
         */
        _validateValue: function (value) {
            var number = parseFloat(value);

            return isNaN(number) || number < 0;
        },

        /**
         * Show error message
         *
         * @param {string} text
         */
        showErrorMessage: function (text) {
            $('#awctq-error-text').html(text);
            $('#awctq-error-message').show();
        },
    });

    return $.mage.awCtqCustomShippingSelector;
});
