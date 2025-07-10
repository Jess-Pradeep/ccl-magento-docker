define([
    'jquery',
    'Magento_Ui/js/modal/alert',
    'mage/translate'
], function ($, alert, $t) {
    'use strict';

    $.widget('mage.awCaSharedRootAddress', {
        options: {
            checkboxSelector: '#aw_ca_shared_address_checkbox',
            url: ''
        },

        /**
         * Initialize widget
         */
        _create: function() {
            this._initEventHandlers();
        },

        /**
         * Init event handlers
         *
         * @private
         */
        _initEventHandlers: function () {
            var me = this;
            $(me.options.checkboxSelector).on('click', function () {
                var isChecked = $(this).is(':checked');

                $.ajax({
                    url: me.options.url,
                    type: 'GET',
                    async: true,
                    data: {isChecked: isChecked},

                    beforeSend: function () {
                        $('body').trigger('processStart');
                    },

                    success: function (response) {
                        if (response) {
                            $("body").trigger('processStop');
                        }
                    },

                    error: function (jqXHR, status, error) {
                        $("body").trigger('processStop');
                        alert({
                            content: $t('Sorry, something went wrong. Please try again later.')
                        });
                    },

                    complete: function () {
                        $('body').trigger('processStop');
                    }
                });
            });
        }
    });

    return $.mage.awCaSharedRootAddress;
});
