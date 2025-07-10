define([
    'Magento_Ui/js/form/components/button',
    'mage/url'  
], function (Element,urlBuilder) {
    'use strict';

    return Element.extend({
        defaults: {
            urlToRedirect: ''
        },

        /**
         * Performs configured actions
         */
        action: function () {
            var redirectLink = urlBuilder.build(this.urlToRedirect);
            window.location.href = redirectLink;
        }
    });
});
