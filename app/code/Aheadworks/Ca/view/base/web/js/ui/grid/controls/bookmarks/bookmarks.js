define([
    'Magento_Ui/js/grid/controls/bookmarks/bookmarks'
], function (Bookmarks) {
    'use strict';

    return Bookmarks.extend({

        /**
         * @inheritDoc
         */
        saveView: function (index) {
            this.updateViewLabel(index)
                .endEdit(index)
                .checkState();

            return this;
        },

        /**
         * @inheritDoc
         */
        saveState: function () {
            return this;
        }
    });
});
