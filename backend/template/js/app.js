var notificacaoID = null;

var App = function () {

    var handleMultiSelect = function(){
        $('.multiSelect').multiSelect({
            selectableHeader: "<input type='text' class='search-input form-control' autocomplete='off' placeholder='Pesquisar ...' style='margin-bottom: 5px;'>",
            selectionHeader: "<input type='text' class='search-input form-control' autocomplete='off' placeholder='Pesquisar ...' style='margin-bottom: 5px;'>",
            afterInit: function(ms){
                var that = this,
                    $selectableSearch = that.$selectableUl.prev(),
                    $selectionSearch = that.$selectionUl.prev(),
                    selectableSearchString = '#'+that.$container.attr('id')+' .ms-elem-selectable:not(.ms-selected)',
                    selectionSearchString = '#'+that.$container.attr('id')+' .ms-elem-selection.ms-selected';

                that.qs1 = $selectableSearch.quicksearch(selectableSearchString)
                    .on('keydown', function(e){
                        if (e.which === 40){
                            that.$selectableUl.focus();
                            return false;
                        }
                    });

                that.qs2 = $selectionSearch.quicksearch(selectionSearchString)
                    .on('keydown', function(e){
                        if (e.which == 40){
                            that.$selectionUl.focus();
                            return false;
                        }
                    });
            },
            afterSelect: function(){
                this.qs1.cache();
                this.qs2.cache();
            },
            afterDeselect: function(){
                this.qs1.cache();
                this.qs2.cache();
            }
        });
    }


    return {


        pageConfig: function(){

            $(document).on("click", ".btn-smtp-teste", function(e){
                e.preventDefault();

                // Recupera o objeto e as configurações
                var obj				= $(this);
                var servidor		= $(".smtp_servidor").val();
                var porta			= $(".smtp_porta").val();
                var seguranca		= $(".smtp_seguranca").val();
                var email			= $(".smtp_email").val();
                var senha			= $(".smtp_senha").val();
                var responder_para	= $(".smtp_responder_para").val();
                var caixa_resposta	= $(".smtp-teste-resposta");


                // Remove as classes de retorno
                caixa_resposta.removeClass("label-important").removeClass("label-success");

                // Texto carregando
                obj.removeClass('blue').addClass('yellow').empty().html('<i class="fa fa-spinner fa-spin"></i> Testando...');

                $.ajax({
                    type: "POST",
                    url: obj.attr('data-action') + '/smtp-teste',
                    data: {servidor:servidor, porta:porta, seguranca:seguranca, email:email, senha:senha, responder_para:responder_para},
                    dataType: 'json',
                    success: function(data){

                        obj.addClass('blue').removeClass('yellow').empty().html('Testar configurações de SMTP');

                        // Se houve erro
                        if( data.valido == 0 ) {
                            caixa_resposta.addClass("label-important").html(data.erro_msg);
                        } else {
                            caixa_resposta.addClass("label-success").html(data.sucesso_msg);
                        }

                    }
                });


            });



        },



        initmodalacao: function( element ){

            var acao            = element.attr('data-acao');
            var title           = element.attr('title');
            var acao_msg        = acao;
            var mostraModal     = element.attr('data-modal');

            if( title ){ acao_msg = title; }

            var msg1		    = "Tem certeza que deseja " + acao_msg + " este item ?";
            var msg2		    = "Tem certeza que deseja " + acao_msg + " os itens selecionados ?";
            var pergunta        = msg1;

            var checkbox		= '.checkboxitens';
            var codigo          = element.attr('data-id');

            var link_codigo     = '';

            if( mostraModal == 'false' || !mostraModal ){

                link_codigo = '/?ids[]=' + codigo;

                if( acao == 'editar' || acao == 'ver' ){
                    link_codigo = '/' + codigo;
                }


                //console.log(element.attr('data-componente') + acao + link_codigo + ((element.attr('data-parametro')) ? '&'+element.attr('data-parametro') : ''));
                window.location = element.attr('data-componente') + acao + link_codigo + ((element.attr('data-parametro')) ? '&'+element.attr('data-parametro') : '');

            } else {

                var callbackParam = [];

                if (!codigo) {
                    $(checkbox).each(function () {
                        if ($(this).is(":checked")) {
                            callbackParam.push("ids[]=" + $(this).val());
                        }
                    });

                } else {
                    callbackParam.push("ids[]=" + codigo);
                }


                if (callbackParam.length === 0) {
                    bootbox.alert('Você precisa selecionar ao menos 1 item.');
                    return false;
                }

                if (callbackParam.length > 1) {
                    pergunta = msg2;
                }

                bootbox.confirm(pergunta, function (result) {
                    if (result) {
                        //console.log(element.attr('data-componente') + acao + '/?' + callbackParam.join("&") + ((element.attr('data-parametro')) ? element.attr('data-parametro') : ''));
                        window.location = element.attr('data-componente') + acao + '/?' + callbackParam.join("&") + ((element.attr('data-parametro')) ? element.attr('data-parametro') : '');
                    }

                });

            }

        },



        //main function to initiate template pages
        init: function () {

            Metronic.init();
            Layout.init();
            QuickSidebar.init();
            FormSamples.init();
            UIAlertDialogApi.init();

            this.campoUrl();
            this.ListaBotaoAcao();

            $(document).on('click', '#button-submit-form', function(){
                if( $('#formPrincipal').length ) {
                    $('#formPrincipal').submit();
                }
            })


            $(document).on('click', '.has-sub a:first-child', function(){
                element = $(this);

                if( element.parent().hasClass('open') ){
                    element.children('i:first-child').removeClass('fa-folder-o').addClass('fa-folder-open-o');
                    element.children('i:last-child').removeClass('fa-chevron-left').addClass('fa-chevron-down');

                } else {
                    element.children('i:first-child').addClass('fa-folder-o').removeClass('fa-folder-open-o');
                    element.children('i:last-child').addClass('fa-chevron-left').removeClass('fa-chevron-down');
                }

            });




            $(document).on('change', '.group-checkbox', function(){
                var set     = $(this).attr('data-set');
                var checked = $(this).is(':checked');

                $(set).each(function () {
                    if (checked) {
                        $(this).parent().addClass('checked');
                    } else {
                        $(this).parent().removeClass('checked');
                    }
                });

            });




            $(document).on('change', '.select-menu-checkbox', function(){

                var set     = $(this).attr('data-menu');
                var regras  = $(this).attr('data-regra');

                var total   = 0;

                $(regras).each(function () {
                    if ( $(this).is(':checked') ) {
                        total += 1;
                    }
                });

                if( total > 0 ){
                    $(set).parent().addClass('checked');
                    $(set).attr('checked', true);
                    console.log('IF');

                } else {
                    $(set).parent().removeClass('checked');
                    $(set).attr('checked', false);
                    console.log('ELSE');
                }


            });

        },


        multSelect: function(){
            handleMultiSelect();
        },

        initLogin: function(){

            /////////////////////////////////////////////////////
            $(".formulario-validar").submit(function(e) {
                e.preventDefault();

                var element     = $(this);
                var action      = element.attr('action');

                $.ajax({
                    type: "POST",
                    url: action,
                    data: new FormData( this ),
                    processData: false,
                    contentType: false,
                    beforeSend: function(){
                        notificacaoID = Notificacao.send( { title: 'Login', text: 'Validando os dados...'} ,notificacaoID);

                    },
                    success: function( data ){
                        setTimeout(function() {

                            if( data.type && data.type == 'success' ){
                                notificacaoID = Notificacao.sucesso( { title: 'Login', text: data.msg} ,notificacaoID);

                            } else if( data.type && data.type == 'error' ) {
                                notificacaoID = Notificacao.erro( { title: 'Login', text: data.msg} ,notificacaoID);
                            }

                            if( data.redirect && data.redirect != false ){
                                window.location = data.redirect;
                            }

                        }, 2000);

                    },
                    error: function(){
                        notificacaoID = Notificacao.erro( { title: 'Erro', text: 'Tente novamente.'} ,notificacaoID);
                    }
                });


            });

            $(document).on("click", ".esqueceu-sua-senha", function() {
                $('.formulario-validar').fadeToggle(function(){
                    $('.formulario-email').fadeToggle();
                });
                return false;
            });

            $(document).on("click", ".form-email-cancelar", function() {
                $('.formulario-email').fadeToggle(function(){
                    $('.formulario-validar').fadeToggle();
                });
                return false;
            });

            $('.formulario-email').submit(function(){
                var element     = $(this);
                var action      = element.attr('action');

                notificacaoID = null;

                if( element.attr('data-codigo') == 'false' ) {

                    $.ajax({
                        type: "POST",
                        url: action,
                        data: new FormData(this),
                        processData: false,
                        contentType: false,
                        beforeSend: function () {
                            notificacaoID = Notificacao.send( { title: 'Recuperação de Senha', text: 'Aguarde! Verificando o e-mail cadastrado ...'} ,notificacaoID);

                        },
                        success: function (data) {
                            setTimeout(function () {

                                if( data.type && data.type == 'success' ){
                                    notificacaoID = Notificacao.sucesso( { title: 'Recuperação de Senha', text: data.msg} ,notificacaoID);

                                } else if( data.type && data.type == 'error' ) {
                                    notificacaoID = Notificacao.erro( { title: 'Recuperação de Senha', text: data.msg} ,notificacaoID);
                                }


                                if (data.action) {
                                    element.attr('action', data.action);
                                    element.attr('data-codigo', 'true');
                                }

                                $('#token').val(data.token);

                                $('.formulario-email .alert-info span').empty().html('Código enviado para o email <b> ' + data.email + ' </b>');

                                $('.campo-email-codigo').val('');
                                $('.campo-email-codigo').attr('placeholder', 'Digite o código');

                            }, 1000);

                        },
                        error: function () {
                            notificacaoID = Notificacao.erro( { title: 'Erro', text: 'Tente novamente.'} ,notificacaoID);
                        }
                    });

                }

                return false;
            });


            $('.formulario-valida').submit(function() {
                var element     = $(this);
                var action      = element.attr('action');

                var codigo      = $('.campo-email-codigo').val();
                var token       = $('#token').val();

                notificacaoID  = null;

                if( element.attr('data-codigo') == 'true' ) {

                    $.ajax({
                        type: "POST",
                        url: action,
                        data: { codigo: codigo, token: token },
                        beforeSend: function () {
                            notificacaoID = Notificacao.send( { title: 'Recuperação de Senha', text: 'Aguarde! Validando código ...'} ,notificacaoID);

                        },
                        success: function (data) {
                            setTimeout(function () {

                                if( data.type && data.type == 'success' ){
                                    notificacaoID = Notificacao.sucesso( { title: 'Recuperação de Senha', text: data.msg} ,notificacaoID);

                                } else if( data.type && data.type == 'error' ) {
                                    notificacaoID = Notificacao.erro( { title: 'Recuperação de Senha', text: data.msg} ,notificacaoID);
                                }

                                if (data.type == 'success') {
                                    $('.formulario-email').fadeToggle(function () {
                                        $('.formulario-alterar-senha').fadeToggle();
                                    });
                                }

                            }, 1000);

                        },
                        error: function () {
                            notificacaoID = Notificacao.erro( { title: 'Erro', text: 'Tente novamente.'} ,notificacaoID);
                        }
                    });
                }
                return false;
            });


            $('.formulario-alterar-senha').submit(function() {
                var element     = $(this);
                var action      = element.attr('action');

                notificacaoID  = null;

                $.ajax({
                    type: "POST",
                    url: action,
                    data: { senha: $('.senha').val(), senha_confirmar: $('.senha_confirmar').val(), token: $('#token').val() },
                    beforeSend: function () {
                        notificacaoID = Notificacao.send( { title: 'Recuperação de Senha', text: 'Aguarde! alterando a senha ...'} ,notificacaoID);

                    },
                    success: function (data) {
                        setTimeout(function () {

                            if( data.type && data.type == 'success' ){
                                notificacaoID = Notificacao.sucesso( { title: 'Recuperação de Senha', text: data.msg} ,notificacaoID);

                            } else if( data.type && data.type == 'error' ) {
                                notificacaoID = Notificacao.erro( { title: 'Recuperação de Senha', text: data.msg} ,notificacaoID);
                            }

                            if (data.type == 'success') {
                                $('#token').val('');

                                $('.formulario-alterar-senha').fadeToggle(function(){
                                    $('.formulario-validar').fadeToggle();
                                });
                            }

                        }, 1000);

                    },
                    error: function () {
                        notificacaoID = Notificacao.erro( { title: 'Erro', text: 'Tente novamente.'} ,notificacaoID);
                    }
                });

                return false;
            });


            $(document).on("click", ".form-alterar-senha-cancelar", function() {
                $('.formulario-alterar-senha').fadeToggle(function(){
                    $('.formulario-login').fadeToggle();
                });
                return false;
            });
            /////////////////////////////////////////////////////
        },

        ListaBotaoAcao: function(){
            $(document).on('click', '.modal-acao-lista-tabela', function(event){
                event.preventDefault();
                App.initmodalacao( $(this) );
            });
        },

        campoUrl: function(){
            jQuery(document).on("keyup", ".campo-define-url, .campo-url", function(e) {

                // Se for o campo de url
                if( jQuery(this).hasClass("campo-url") ) {

                    // Ja retorna o valor tratado no campo
                    jQuery(this).val(criaUrl(jQuery(this).val()));

                    return;

                }

                // Inicia a url final
                var urlFinal = '';

                // Faz o loop nos campos do form que irão gerar a url final
                jQuery(this).closest("form").find(".campo-define-url").each(function(i, obj){

                    // Se não for o primeiro então adiciona o hífen pra separar
                    if( urlFinal !== '' ) {
                        urlFinal += '-';
                    }

                    // Adiciona na url o valor do campo tratado
                    urlFinal += criaUrl(jQuery(obj).val());

                });

                // Adiciona a url no campo do mesmo form com a classe campo-url
                jQuery(this).closest("form").find(".campo-url").val(urlFinal);

            });

            /*
             * Funcao que cria a url(slug)
             */
            function criaUrl( slugcontent ) {

                // Converte para Letras Minúsculas (Importante: desde a etapa seguinte caracteres especiais são definidas em letras minúsculas apenas)
                slugcontent = slugcontent.toLowerCase();

                // Converte Caracteres Especiais
                var accents = {a: /(\u00e1|\u00e0|\u00e2|\u00e3|\u00e4)/g, e: /(\u00e9|\u00e8|\u00ea|\u00ea)/g, i: /(\u00ed|\u00ec|\u00ee|\u00ef)/g, o: /(\u00f3|\u00f2|\u00f4|\u00f5|\u00f6)/g, u: /(\u00fa|\u00f9|\u00fb|\u00fc)/g, n: /\u00f1/g, c: /\u00e7/g}
                for( var i in accents ) slugcontent = slugcontent.replace(accents[i], i);

                var slugcontent_hyphens = slugcontent.replace(/\s/g, '-');

                var finishedslug = slugcontent_hyphens.replace(/[^a-zA-Z0-9\-]/g, '');
                finishedslug = finishedslug.toLowerCase();
                finishedslug = finishedslug.replace(/-+/g, '-'); // Remove multiplos ‘-’
                finishedslug = finishedslug.replace(/(^-)|(-$)/g, ''); // Remover caráter primeiro e último da seqüência, se é ‘-’

                return finishedslug;

            }

        }

    };

}();



