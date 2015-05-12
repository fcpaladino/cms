var Plugins = function () {

    var MultiSelect = function(){
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


    var Editor = function(){
        var dd = 1;
        $(".editor").each(function(){
            $(this).attr("id","editor" + dd);
            CKEDITOR.replace( "editor" + dd , {
                height:"400px"
                ,toolbar: [
                    { name: "document", items: [ "Source" ] }
                    ,{ name: "clipboard", items: [ "Paste", "PasteText", "PasteFromWord", "-", "Undo", "Redo" ] }
                    ,{ name: "basicstyles", items: [ "Bold", "Italic", "Underline", "Strike", "RemoveFormat" ] }
                    ,{ name: "paragraph", items: [ "NumberedList", "BulletedList", "Blockquote", "JustifyLeft", "JustifyCenter", "JustifyRight", "JustifyBlock" ] }
                    ,{ name: "links", items: [ "Link", "Unlink" ]  }
                    ,{ name: "insert", items: [ "Image", "Table" ]}
                    ,{ name: "style", items: [ "FontSize", "TextColor", "BGColor" ] }
                ]
            });
            dd = dd + 1;
        });
    }


    var MaxLength = function(){
        $('.maxlength-handler').maxlength({
            limitReachedClass: 'label label-danger',
            alwaysShow: true,
            threshold: 5
        });
    }


    var DataHora = function(){
        $(".datetime").datetimepicker({
            isRTL: Metronic.isRTL(),
            format: "dd/mm/yyyy HH:ii:ss",
            autoclose: true,
            pickerPosition: (Metronic.isRTL() ? "bottom-right" : "bottom-left"),
            todayBtn: true,
            language: 'pt-BR'
        });

        $(".date").datepicker({
            isRTL: Metronic.isRTL(),
            format: "dd/mm/yyyy",
            autoclose: true,
            pickerPosition: (Metronic.isRTL() ? "bottom-right" : "bottom-left"),
            todayBtn: true,
            language: 'pt-BR'
        });

        $('.time').timepicker({
            autoclose: true,
            minuteStep: 5,
            showSeconds: false,
            showMeridian: false,
            autoclose: true
        });
    }


    var FormValidation = function(){
        if( $("#formPrincipal").length ) {

            var form1 = $('#formPrincipal');
            var error1 = $('.alert-danger', form1);
            var success1 = $('.alert-success', form1);

            form1.validate({
                errorElement: "span"
                ,errorClass: "help-block help-block-error"
                ,focusInvalid: false
                ,ignore: ""
                ,invalidHandler: function (event, validator) { //display error alert on form submit
                    success1.hide();
                    error1.show();
                    Metronic.scrollTo(error1, -200);
                },

                highlight: function (element) { // hightlight error inputs
                    $(element)
                        .closest('.form-group').addClass('has-error'); // set error class to the control group
                },

                unhighlight: function (element) { // revert the change done by hightlight
                    $(element)
                        .closest('.form-group').removeClass('has-error'); // set error class to the control group
                },

                success: function (label) {
                    label
                        .closest('.form-group').removeClass('has-error'); // set success class to the control group
                },

                submitHandler: function (form) {
                    form.submit();
                }
            });
        }

    }


    var Tags = function(){
        $(".input-tag").each(function(){
            $(this).tagsInput({
                width: 'auto',
                'onAddTag': function () {}
            });
        });


    }


    var Mascara = function(){
        $.extend($.inputmask.defaults, {
            'autounmask': true
        });

        $(".mascara").each(function(){
            var el      = $(this);
            var mask    = $(this).attr('data-mask');

            if( mask != ''){
                el.inputmask(mask);
            }
        });

    }


    var NivelSenha = function(){
        var x = 1;
        $(".nivel_senha").each(function(){

            var el          = $(this);
            var action      = '.password' + x;
            var classe      = el.attr('data-action', action);

            el.addClass('password' + x);

            $(classe).keydown(function () {

                var datainit    = el.attr('data-init');
                var input       = $(el.attr('data-action'));

                if (datainit == 'false') {
                    // set base options
                    input.pwstrength({
                        raisePower: 1.4,
                        verdicts: ["Fraco", "Normal", "Medio", "Forte", "Muito forte"],
                        scores: [17, 26, 40, 50, 60]
                    });

                    // add your own rule to calculate the password strength
                    input.pwstrength("addRule", "demoRule", function (options, word, score) {
                        return word.match(/[a-z].[0-9]/) && score;
                    }, 10, true);

                    // set as initialized
                    input.attr('data-init', 'true');
                }
            });

            x = x + 1;
        });


    }


    var FileMultiple = function(){

    }

    return {

        //main function to initiate template pages
        init: function () {
        },


        multSelect: function(){
            MultiSelect();
        },

        editor: function(){
            Editor();
        },

        maxlength: function(){
            MaxLength();
        },

        datahora: function(){
            DataHora();
        },

        tags: function(){
            Tags();
        },

        validation: function(){
            FormValidation();
        },

        inputmask: function(){
            Mascara();
        },

        nivelsenha: function(){
            NivelSenha();
        },


        file: function(){


        }



    };

}();



