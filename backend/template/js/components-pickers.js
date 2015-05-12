var ComponentsPickers = function () {


    return {
        //main function to initiate the module
        init: function () {

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

    };

}();