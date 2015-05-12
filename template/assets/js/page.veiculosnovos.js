$(document).ready(function(){
    $(document).ajaxStart(function () {

        if( $('#more-posts').length > 0 ) {
            $('#more-posts').fadeToggle(400);
        }

    }).ajaxStop(function () {

        if( $('#more-posts').length > 0 ) {
            setTimeout(function () { $('#more-posts').fadeToggle(400); }, 1000);
        }

    });
})



$(document).scroll(function () {

    if  ($(window).scrollTop() == $(document).height() - $(window).height()) {

        var $container = $('#main-cont');
        $container.isotope({itemSelector: '.single-new'});

        var url             = $('#url');
        var page            = $('#page');
        var itensPorPagina  = $('#itensPorPagina');
        var categoria       = $('#categoria');
        var conteudo        = $('#main-cont');

        var action          = url.val();
        var pagina          = parseInt( page.val() );
        var soma            = parseInt( itensPorPagina.val() );
        var proxima         = pagina + soma;

        page.val(proxima);

        $.ajax({
            type: "POST",
            url: action,
            data: { page: proxima },
            dataType: 'json',
            success: function(data){

                setTimeout(function() {
                    var $elems  = $(data.conteudo);
                    $container.append($elems).isotope('appended', $elems);
                }, 1000);


            }
        });

    }


});