define([
    'jquery',
    'Magento_Ui/js/modal/confirm'
], function($, confirmation) {
    "use strict";

    $.widget('mage.awClAddDueDateRestartModal', {

        /**
         * Initialize widget
         */
        _create: function () {
            this.modalProcess();
        },

        /**
         * Reset due date value
         */
        resetDueDateVal: function () {
            $("input[name='aw_credit_limit[due_date]']").val('').change();
        },

        /**
         * Modal process
         */
        modalProcess: function () {
            var self = this,
                buttonSelector = '.page-actions';

            $("#save").hide();
            $("#save_and_continue").hide();

            if ($('.page-actions-buttons').length > 0) {
                buttonSelector = 'page-actions-buttons'
            }
            $("#pre_save_continue_popup_display").appendTo(buttonSelector);
            $("#pre_save_continue_popup_display").show();
            $("#pre_save_popup_display").appendTo(buttonSelector);
            $("#pre_save_popup_display").show();

            $("#pre_save_popup_display").click(function () {
                displayModal("#save");
            });

            $("#pre_save_continue_popup_display").click(function () {
                displayModal("#save_and_continue");
            });

            function displayModal(replaceButtonId) {
                var paymentPeriod = $("input[name='aw_credit_limit[payment_period]']").val();
                var updateBalanceAmount = $("input[name='aw_credit_limit[amount]']").val();
                var dueDate = $("input[name='aw_credit_limit[due_date]']").val();
                var creditBalance = $("input[name='aw_credit_limit[credit_balance]']").val();
                var futureBalance = Number(updateBalanceAmount) + Number(creditBalance);
                if (paymentPeriod && updateBalanceAmount && dueDate && (futureBalance < 0)) {
                    confirmation({
                        title: $.mage.__('Attention!'),
                        content: $.mage.__('Do you want to restart credit days counting?'),
                        actions: {
                            confirm: function () {
                                self.resetDueDateVal();
                                $(replaceButtonId).click();
                            },
                            cancel: function (event) {
                                if (event && !$(event.target).hasClass('action-close')) {
                                    $(replaceButtonId).click();
                                }
                            },
                            always: function () {
                                return false;
                            }
                        },
                        buttons: [{
                            text: $.mage.__('No'),
                            class: 'action-secondary action-dismiss',
                            click: function (event) {
                                this.closeModal(event);
                            }
                        }, {
                            text: $.mage.__('Yes'),
                            class: 'action-primary action-accept',
                            click: function (event) {
                                this.closeModal(event, true);
                            }
                        }]
                    });
                } else if (dueDate && (futureBalance >= 0)) {
                    self.resetDueDateVal();
                    $(replaceButtonId).click();
                } else {
                    $(replaceButtonId).click();
                }
            }
        },
    });

    return $.mage.awClAddDueDateRestartModal;
});
