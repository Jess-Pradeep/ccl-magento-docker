define([
    'jquery',
    'Aheadworks_Ca/js/action/send-request',
    'Aheadworks_Ca/js/action/show-error-popup',
    'Aheadworks_Ca/js/action/company/domain/reload-grid'
], function ($, sendRequest, showError, reloadGrid) {
    "use strict";

    return {

        /**
         * Save domain
         *
         * @param {Object} data
         * @param {String} saveUrl
         * @return {Deferred}
         */
        saveDomain: function(data, saveUrl) {
            return this._sendRequest(saveUrl, data);
        },

        /**
         * Delete domain
         *
         * @param {Object} data
         * @param {String} deleteUrl
         * @return {Deferred}
         */
        deleteDomain: function(data, deleteUrl) {
            return this._sendRequest(deleteUrl, data);
        },

        /**
         * Send ajax request
         *
         * @param {String} url
         * @param {Object} data
         * @return {Deferred}
         * @private
         */
        _sendRequest: function(url, data) {
            var self = this,
                deferred = $.Deferred();

            $("body").trigger('processStart');
            sendRequest(url, data).done(function(response){
                if (response.error) {
                    self._showError(response.message);
                    deferred.reject();
                } else {
                    reloadGrid();
                    deferred.resolve();
                }
            }).fail(function(response){
                self._showError(response.statusText);
                deferred.reject();
            }).always(function () {
                $("body").trigger('processStop');
            }.bind(this));

            return deferred;
        },

        /**
         * Show error popup
         *
         * @param {String} error
         * @private
         */
        _showError: function(error) {
            showError('We cannot process this request', error);
        }
    };
});
