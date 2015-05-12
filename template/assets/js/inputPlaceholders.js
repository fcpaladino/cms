$.fn.inputPlaceholders = function(){

    var el = this;

    $.each(el, function(){

        var value = $(this).attr("data-value");

        if(!!value) {
            $(this).val(value);

            $(this).on({focus: function(){
                if (this.value == value) {
                    //console.log("enter");
                    $(this).val("");
                }
            }, blur: function(){
                if (this.value == '') {
                    //console.log("leave");
                    $(this).val(value);
                }
            }});
        }
    });
}

$(document).ready(function() {

    $("input[type=text], textarea").inputPlaceholders();
});