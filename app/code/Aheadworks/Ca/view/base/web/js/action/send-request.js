define([
    'jquery',
    'mageUtils'
], function ($, utils) {
    "use strict";

    return function (url, params) {
        var setup = {
            url: url,
            type: "POST",
            dataType: 'json',
            data: utils.serialize(params)
        };

        return $.ajax(setup);
    }
});
