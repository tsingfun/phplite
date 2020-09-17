// 限制只能输入数字
$.fn.onlyNum = function () {
    $(this).keypress(function (event) {
        var eventObj = event || e;
        var keyCode = eventObj.keyCode || eventObj.which;
        // valid the input value (according to the regex)
        return regNum(this.value + String.fromCharCode(keyCode)+0);
    }).focus(function () {
    //禁用输入法
        this.style.imeMode = 'disabled';
    });
};
/* 正则校验 */
function regNum(number) {
    if (/^[0-9]+(\.[0-9]+)?$/.test(number))
        return true;
    else
        return false;
}

/*应用*/
$(document).ready( function(){
	$("input.num").onlyNum();
});