define([
    'uiElement',
    'mage/apply/main',
], function (Element, mage) {
    'use strict';

    return Element.extend({
        defaults: {
            formSelector: '.aw-ca__extra-form'
        },

        /**
         * Form are getting initialized once this component is rendered
         */
        onFormRender: function () {
            mage.applyFor(this.formSelector, {}, 'validation');
        }
    });
});