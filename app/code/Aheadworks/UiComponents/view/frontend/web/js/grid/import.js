/**
 * @api
 */
define([
    'jquery',
    'underscore',
    'uiCollection'
], function ($, _, Element) {
    return Element.extend({
        defaults: {
            template: 'Aheadworks_UiComponents/ui/grid/importButton',
            modules: {
                formContainer: '${ $.formContainer }'
            },
            listens: {
                '${ $.name }.import_container.import_fieldset.form_container.file:value': 'setDisabled'
            },
        },

        /**
         * init observable component
         */
        initObservable: function () {
            this._super()
                .observe([
                    'disable'
                ]);

            return this;
        },

        /**
         * set disabled component
         *
         * @param data
         */
        setDisabled: function (data) {
            this.disable(!data.length);
        },

        /**
         * apply action
         */
        applyOption: function () {
            this.formContainer().apply();
        }
    })
})
