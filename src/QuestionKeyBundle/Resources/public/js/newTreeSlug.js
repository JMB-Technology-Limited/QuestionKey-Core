/** Based on https://github.com/OpenACalendar/OpenACalendar-Web-Core/blob/master/core/theme/default/js/index/createSite.js from http://ican.openacalendar.org/  **/
var hasUserEditedPublicId = false;
var titleObj;
var keyObj;

var titleAdminFunc = function() {
    if (!hasUserEditedPublicId) {
        slug = titleObj.val().replace(/\W/g, '').toLowerCase();
        keyObj.val( slug  );
    }
};

var publicIdFunc = function() {
    hasUserEditedPublicId = true;
};

$(document).ready(function() {

    titleObj = $('#NewTreeFormFieldTitleAdminWrapper input');
    titleObj.change(titleAdminFunc);
    titleObj.keyup(titleAdminFunc);

    keyObj = $('#NewTreeFormFieldPublicIdWrapper input')
    keyObj.keyup(publicIdFunc);
});

