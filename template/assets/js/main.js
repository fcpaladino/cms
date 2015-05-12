mobileTrigger = 992;

function menuWindowResize(){
    var w = $(window).width();
    var menu = $("#up-header, #sub-header");
    if(w > 1200 && menu.is(":hidden")) {
        menu.removeAttr("style");
    }
}

function navigationResize() {
    if($("#acessorios-menu").length){
        $('#menu-cat-nav li.more').before($('#overflow > li'));

        var $navItemMore = $('#menu-cat-nav > li.more'),
            $navItems = $('#menu-cat-nav > li:not(.more)'),
            navItemMoreWidth = navItemWidth = $navItemMore.width(),
            windowWidth = $(window).outerWidth(),
            windowWidthMinus = $("#acessorios-menu").width() - 260,
            navItemMoreLeft, offset, navOverflowWidth;
            //console.log("window" + windowWidth);


        $navItems.each(function() {
            if (windowWidth > mobileTrigger - 8){
                navItemWidth += $(this).width();
                //console.log("nav" + navItemWidth);
            } else{
                navItemWidth = 100 + "%";
                //console.log("nav" + navItemWidth);
            }
        });

        navItemWidth > windowWidthMinus ? $navItemMore.show() : $navItemMore.hide();

        while (navItemWidth > windowWidthMinus) {
            navItemWidth -= $navItems.last().width();
            $navItems.last().prependTo('#overflow');
            $navItems.splice(-1,1);
        }

        navItemMoreLeft = $('#menu-cat-nav .more').offset().left;
        navOverflowWidth = $('#overflow').width();
        offset = navItemMoreLeft + navItemMoreWidth - navOverflowWidth;

        $('#overflow').css({
            'left': offset
        });
    }
}

function acessoriosMenuResize(){
    var w = $(window).width();
    var man = $("#fake-acessorios-menu, #acessorios-menu")
    var menu = $('#menu-cat-nav, #acessorios-search');
    if(w > mobileTrigger) {
        menu.removeAttr('style');
        man.removeClass("stick");
        navigationResize()
    }
}

function acessoriosStickDiv(){
    var windowWidth = $(window).width();
    if ($("#acessorios-menu").length && windowWidth < 992){
        var window_top = $(window).scrollTop();
        var div_top = $('#fake-acessorios-menu').offset().top;
        if (window_top > div_top - 50) {
            $('#acessorios-menu, #fake-acessorios-menu').addClass('stick');
        } else {
            $('#acessorios-menu, #fake-acessorios-menu').removeClass('stick');
        }
    }
}

function vendasTab(){
    var windowWidth = $(window).width();
    if($("#vendas-tabs").length){
        if(windowWidth < 992){
            $("#vendas-tabs-cont .single-cont").removeAttr("style").removeClass("active");
        } else{
            $("#vendas-tabs-cont .single-cont:first-child").css("display", "block").addClass("active");
            $("#vendas-tabs-selector a:first-child").siblings().removeClass("active");
            $("#vendas-tabs-selector a:first-child").addClass("active");
        }
    }
}
function acessoriosMenuMaxHeight(){
    var el = $("#acessorios-menu");
    var windowHeight = $(window).height();
    el.css("max-height", (windowHeight - 50));
}
$(document).ready(function() {

    $(".animsition").animsition({
        inDuration: 1500,
        outDuration: 1000,
        loading: true,
        loadingParentElement: 'body',
        loadingClass: 'animsition-loading',
        overlay               :   false,
        overlayClass          :   'animsition-overlay-slide',
        overlayParentElement  :   'body'
    });

    acessoriosStickDiv();

    navigationResize();

    acessoriosMenuMaxHeight();

    // Aciona o submenu de carros no Header
    $("#carro").on({
        mouseenter: function () {
            var windowWidth = $(window).width();
            if (windowWidth > 1200) {
                $("#carro ul").stop().slideDown(600, "easeOutQuart");
            }
        }, mouseleave: function () {
            var windowWidth = $(window).width();
            if (windowWidth > 1200) {
                $("#carro ul").stop().slideUp(600, "easeInOutQuart");
            }
        }
    });

    //Custom file upload input
    $('#file-upload').on("click", function() {
        $(this).next('input[type="file"]').click();
        $(this).next('input[type="file"]').change( function(){
            var fileName = $(this).val().replace(/C:\\fakepath\\/i, '')
            if(fileName.length > 40) {
                str = fileName.substring(0,40) + '...';
            }else{
                str = fileName;
            }
            $('#file-name').text(str);
            if($('#file-name').text()==''){
                $('#file-name').text('Nenhum arquivo selecionado...');
            }
        });
    });

    // Dropdown normal
    $(".dropdown-trigger").on('click', function (e) {
        e.preventDefault();

        var dropdownCont = $(this).siblings(".dropdown");

        dropdownCont.slideToggle(400);
    });
    $(".dropdown").on('click', 'a', function (e) {
        e.preventDefault();

        var par = $(this).closest(".dropdown-cont");
        var con = $(this).html();
        var tar = $(e.target);

        par.find('.input-dropdown').prop("value", $(this).attr('data-id'));

        $(this).closest(".dropdown").siblings(".dropdown-trigger").find(".cont").fadeOut(300, function () {
            $(this).html(con);
            $(this).fadeIn(300);
        });

        $(this).parent().siblings().removeClass("active");
        $(this).parent().addClass("active");

        $(this).closest(".dropdown").slideToggle(400, "easeInOutQuart");
    });

    // Faz a verificação dos campos no formulário de busca da página inicial
    if($("#main-carousel.home").length) {
        $(".semi-dropdown-trigger").on('click', function (e) {
            e.preventDefault();

            var dropdownCont = $(this).siblings(".semi-dropdown");

            dropdownCont.slideToggle(400);
        });
    }

    // Faz a verificação dos campos no formulário de busca da página inicial
    if($("#main-carousel.home").length) {
        $(".semi-dropdown").on('click', 'a', function (e) {
            e.preventDefault();

            var par = $(this).closest(".semi-dropdown-cont");
            var con = $(this).html();
            var tar = $(e.target);

            $(this).closest(".semi-dropdown").siblings(".semi-dropdown-trigger").find(".cont").fadeOut(300, function () {
                $(this).html(con);
                $(this).fadeIn(300);
            });

            $(this).parent().siblings().removeClass("active");
            $(this).parent().addClass("active");

            $(this).closest(".semi-dropdown").slideToggle(400, "easeInOutQuart");

            if (par.hasClass("empre")) {
                if (!tar.closest("li").hasClass("all")) {
                    $(".semi-dropdown-cont.model").slideDown(400, "easeInOutQuart");
                } else {
                    $(".semi-dropdown-cont.model").slideUp(400, "easeInOutQuart");
                }
            }
        });
    }

    // Busca de semi-novos
    if($("#seminovos-search").length) {
        $(".semi-dropdown-trigger").on('click', function (e) {
            e.preventDefault();

            target = $(e.target);
            parTar = target.closest(".semi-dropdown-cont");

            if(!parTar.hasClass("disabled")){
                var dropdownCont = $(this).siblings(".semi-dropdown");

                dropdownCont.slideToggle(400);
            }

        });
    }

    // Busca de semi-novos
    if($("#seminovos-search").length) {
        $(".semi-dropdown").on('click', 'a', function (e) {
            e.preventDefault();

            var par = $(this).closest(".semi-dropdown-cont");
            var con = $(this).html();
            var tar = $(e.target);

            $(this).closest(".semi-dropdown").siblings(".semi-dropdown-trigger").find(".cont").fadeOut(300, function () {
                $(this).html(con);
                $(this).fadeIn(300);
            });

            $(this).parent().siblings().removeClass("active");
            $(this).parent().addClass("active");

            $(this).closest(".semi-dropdown").slideToggle(400, "easeInOutQuart");

            if (par.hasClass("empre")) {
                if (!tar.closest("li").hasClass("all")) {
                    $(".semi-dropdown-cont.disable").removeClass("disabled");
                } else {
                    $(".semi-dropdown-cont.disable").addClass("disabled");

                    $(".semi-dropdown-cont.disable .cont").fadeOut(300, function () {
                        $(this).html("Selecione o modelo");
                        $(this).fadeIn(300);
                    });
                    $(".semi-dropdown-cont.disable ul li").removeClass("active");
                }
            }
        });
    }

    // Cria o carrosel no header
    if($("#main-carousel.header").length){
        $('#main-carousel.header').slick({
            autoplay: true,
            autoplaySpeed: 10000,
            dots: false,
            arrows: true,
            pauseOnHover: true,
            draggable: false
        });
    }

    // Cria o carrosel da página inicial
    if($("#main-carousel.home").length){
        $('#main-carousel-cont').slick({
            autoplay: true,
            autoplaySpeed: 10000,
            dots: true,
            arrows: false,
            pauseOnHover: false
        });
    }

    // Cria o carrosel da página de novos produtos
    if($("#main-carousel.novos").length){
        $('#main-carousel-cont').slick({
            autoplay: true,
            autoplaySpeed: 10000,
            dots: true,
            arrows: true,
            pauseOnHover: false
        });
    }

    // Cria o carrosel nas demais páginas
    if($("#main-carousel.acessorios-int").length || $("#main-carousel.novos-int").length || $("#main-carousel.multimarcas-int").length){
        $('#main-carousel-cont').slick({
            autoplay: true,
            autoplaySpeed: 10000,
            dots: true,
            arrows: true,
            pauseOnHover: true
        });
    }

    // Gerencia as tabs da página de vendas
    if($("#vendas-tabs").length){

        var tabs = $("#vendas-tabs");
        var tabsCont = $("#vendas-tabs-cont");

        tabs.prepend("<div id=\"vendas-tabs-selector\"></div>");

        var tabsSelector = $("#vendas-tabs-selector");

        // Cria os elementos restantes da página de vendas
        tabsCont.children().each(function(){
            var dataId = $(this).attr("id");
            var dataTitle = $(this).attr("data-title");

            tabsSelector.append("<a href=\"#" + dataId + "\">" + dataTitle + "</a>\n");
            $(this).prepend("<div class=\"title\">" + dataTitle + "</div>");
        });

        tabsSelector.children("a:first-child").addClass("active");
        tabsCont.children("div:first-child").addClass("active").css("display", "block");

        tabsSelector.on('click', 'a', function(e){
            e.preventDefault();

            var tabId = $(e.target).attr("href");

            var tabEl = $(tabId);

            tabsSelector.children(".active").removeClass("active");
            $(e.target).addClass("active");

            tabEl.siblings(".active").removeClass('active').slideUp(400, "easeInOutQuart", function(){
                tabEl.addClass('active').slideToggle(400, "easeInOutQuart");
            });
        });
    }

    // Inicializa o isotope no site inteiro
    if ($("#footer").length){
        if ($("#main.acessorios-int").length){
            var $container = $("#up-footer-cont, #relacionados-cont").imagesLoaded(function () {
                $container.isotope({ layoutMode: 'fitRows' });
            });
        } else if ($("#main.novos-int").length){
            var $container = $("#up-footer-cont, #modelos-cont, #inter-cont").imagesLoaded(function () {
                $container.isotope({ layoutMode: 'fitRows' });
            });
        } else if($("#main.acessorios").length || $("#main.novos").length || $("#main.multimarcas").length){
            var $container = $("#up-footer-cont, #main-cont").imagesLoaded(function () {
                $container.isotope({ layoutMode: 'fitRows' });
            });
        } else if($("#main.servicos-financeiros").length){
            var $container = $("#up-footer-cont, #main-cont").imagesLoaded(function () {
                $container.isotope();
            });
        } else if($("#main.busca").length){
            var $container = $("#up-footer-cont, .single-busca-cont .single-cont").imagesLoaded(function () {
                $container.isotope();
            });
        } else{
            var $container = $("#up-footer-cont").imagesLoaded(function () {
                $container.isotope({ layoutMode: 'fitRows' });
            });
        }
    }

    if( $("#acessorios-menu.busca").length ){
        $("#menu-cat-nav, .single-cont-title-cont .right").on('click', function(e){
            e.preventDefault();

            var el = $(e.target);
            var elName = el.attr("class");
            console.log(elName);

            $("#menu-cat-nav li").removeClass("active");
            $("#menu-cat-nav li").find("a." + elName).parent().addClass("active");

            if ( elName == "todos" ){

                var elTarget = $(".single-busca-cont");

                elTarget.addClass("active").slideDown(700, "easeInOutQuart", function(){
                    $(this).animate({opacity: 1}, 300, "easeInOutQuart");
                });

                $(".single-cont-title-cont .right").fadeIn(300, "easeInOutQuart");

            } else{

                var elTarget = $("#busca-" + elName + "-cont");

                elTarget.siblings(".active").removeClass("active").animate({opacity: 0}, 300, "easeInOutQuart", function(){
                    $(this).slideUp(700, "easeInOutQuart");
                    elTarget.addClass("active").slideDown(700, "easeInOutQuart", function(){
                        $(this).animate({opacity: 1}, 300, "easeInOutQuart", function(){
                            var $container = $(".single-busca-cont .single-cont").imagesLoaded(function () {
                                $container.isotope('reloadItems');
                            });
                        });
                    });
                });

                $(".single-cont-title-cont .right").fadeOut(300, "easeInOutQuart");
            }

            var divTop = $('#fake-acessorios-menu').offset().top - 45;
            $('html, body').animate({scrollTop: divTop}, 500, "easeInOutQuart");

        });
    }

    $("html, body").on('mousewheel DOMMouseScroll', function(){
        $(this).stop();
    });

    $("#menu-pull").on('click', function(e){
        e.preventDefault();

        var acessoriosMenu = $("#menu-cat-nav");
        var acessoriosSearch = $("#acessorios-search");
        var prodMenu = $("#up-header");
        var prodMenuD = $("#sub-header");

        function bumbum(){
            prodMenu.slideToggle(300, "easeInOutQuart");
            prodMenuD.slideToggle(600, "easeInOutQuart");
        }

        if( acessoriosMenu.is(":visible") ){
            acessoriosMenu.slideToggle(700, "easeInOutQuart", function(){
                bumbum();
            });
        } else if( acessoriosSearch.is(":visible") ){
            acessoriosSearch.slideToggle(300, "easeInOutQuart", function(){
                bumbum();
            });
        } else{
            bumbum();
        }


    });

    // Menu da página de acessórios
    $("#acessorios-menu-cat").on('click', function(e){
        e.preventDefault();

        var acessoriosMenu = $("#menu-cat-nav");
        var acessoriosSearch = $("#acessorios-search");

        if($(e.target).is("#menu-cat-search")){
            if(acessoriosMenu.is(":visible")){
                acessoriosMenu.slideUp(700, "easeInOutQuart", function(){
                    acessoriosSearch.slideToggle(250, "easeInOutQuart", function(){});
                });
            } else{
                if(acessoriosSearch.is(":hidden")){
                    acessoriosSearch.slideDown(250, "easeInOutQuart");
                } else{
                    acessoriosSearch.slideUp(250, "easeInOutQuart");
                }
            }
        } else{
            if(acessoriosSearch.is(":visible")){
                acessoriosSearch.slideUp(250, "easeInOutQuart", function(){
                    acessoriosMenu.slideToggle(700, "easeInOutQuart", function(){});
                });
            } else{
                if(acessoriosMenu.is(":hidden")){
                    acessoriosMenu.slideDown(700, "easeInOutQuart");
                } else{
                    acessoriosMenu.slideUp(700, "easeInOutQuart");
                }
            }
        }
    });

    // Menu da página de acessórios
    $("#menu-cat-nav").on('click', 'a', function(e){
        if($(window).width() < mobileTrigger) {
            e.preventDefault();

            var target = $(e.target);
            var message = target.html();

            target.closest("li").addClass("active").siblings().removeClass("active");
            setTimeout(function () {
                $("#menu-cat-cont span").html(message);
                $("#menu-cat-nav").slideUp(500, "easeOutQuart");
            }, 500);
        }
    });

    // Gerencia a linha do tempo
    $("#time-line-selector-cont").on('click', 'a', function (e) {
        e.preventDefault();

        function timelineCenter(){
            var el = $("#time-line");
            var elOffset = el.offset().top;
            var elHeight = el.outerHeight();
            var windowHeight = $(window).height();
            var offset;

            if (elHeight < windowHeight) {
                offset = elOffset - ((windowHeight / 2) - (elHeight / 2));
            }
            else {
                offset = elOffset;
            }
            $('html, body').animate({scrollTop:offset}, 500);
        }

        $("#time-line-selector-cont li").removeClass("active");

        $(this).parent().addClass("active");

        var itemText = $(this).attr("data-text");

        var timelineItem = $("#time-line").find(".time-line-item");

        var timelineItemDesc = timelineItem.find(".hist-desc");

        if(timelineItem.is(":hidden")){
            timelineItemDesc.html(itemText);
            timelineItem.slideDown(600, "easeOutQuart", function(){
                timelineItemDesc.animate({opacity : 1}, 200, "easeInQuart", function(){
                    timelineCenter();
                });
            });
        } else{
            timelineItemDesc.animate({opacity : 0}, 300, "easeOutQuart", function(){
                $(this).slideUp(800, "easeInOutQuart", function(){
                    $(this).html(itemText);
                    $(this).slideDown(800, "easeOutQuart", function(){
                        timelineItemDesc.animate({opacity : 1}, 200, "easeInQuart", function(){
                            timelineCenter();
                        });
                    });
                });
            });
        }

        $("#time-line-selector").stop().animate({
            scrollLeft: $(this).attr("data-id-selector") * $(this).parent().width() - $("#time-line-selector").width()/2 - $(this).parent().width()/2
        }, 1000, "easeOutQuart");
    });

});

$(window).scroll(function(){
    acessoriosStickDiv();
});

$(window).resize(function(){
    navigationResize();
    acessoriosMenuResize();
    vendasTab();
    acessoriosMenuMaxHeight();
    menuWindowResize();
});