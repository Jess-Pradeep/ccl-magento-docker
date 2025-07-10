require([
    'jquery',
    'Magento_Ui/js/lib/view/utils/dom-observer',
    'mage/translate'
], function ($, domObserver) {
    $(document).ready(function(){
        addNotice('payment_period');
    });

    /**
     * Add custom notice
     *
     * @param {String} fieldName
     */
    function addNotice(fieldName) {
        domObserver.get("input[name='aw_credit_limit["+ fieldName +"]']", function(elem){
            var fieldId = $(elem).attr('id');
            var noticeElem = $("#notice-"+ fieldId);
            var noticeText = noticeElem.children().text();
            $('label[for='+ fieldId +']').append(
                "<div class='aw_cl_custom_notice_field'>"+ $.mage.__(noticeText) +"</div>"
            );
            noticeElem.remove();
        });
    }
});
