define([
    'jquery',
    'jquery/validate',
    'mage/translate'
], function($){
    'use strict';
    return function() {
        $.validator.addMethod(
            "zero-or-greather-and-equal-to-one",
            function(value, element) {
                return value == 0 || value >= 1 ? true : false;
            },
            $.mage.__("Enter 0 OR 1 and greater value")
        );

        return $.mage.validation;
    }
});