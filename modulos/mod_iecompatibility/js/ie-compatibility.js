$(window).load(function() {

    explorerAlert = 8;

    var setCountDown = setInterval(function(){
        $(".count-down .number").html(explorerAlert);
        explorerAlert = explorerAlert - 1;

        if(explorerAlert == 0){
            window.clearInterval(setCountDown);
        }

    }, 1000);

    $("#internet-explorer-alert").delay(explorerAlert + '000').animate({'opacity': '0'}, 500, function(){
        $("#internet-explorer-alert").remove();
        $("#internet-explorer-top-alert, #internet-explorer-top-alert-int").css('opacity','0').animate({'height':'140','opacity':'1'}, 500);
    });
});