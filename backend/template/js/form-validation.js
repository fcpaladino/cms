var FormValidation = function () {

    return {
        //main function to initiate the module
        init: function () {

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
                        success1.show();
                        error1.hide();
                    }
                });
            }

        }

    };

}();