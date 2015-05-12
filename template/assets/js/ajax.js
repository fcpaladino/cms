$(document).ready(function() {
    var notification = null;


    $("#FormEnviaAjax").submit(function(e) {
        e.preventDefault();

        var element      = $(this);
        var action       = element.attr('action');
        var title        = element.attr('data-title');

        $.ajax({
            type: "POST",
            url: action,
            data: new FormData( this ),
            processData: false,
            contentType: false,
            beforeSend: function(){
                notification = Notificacao.send({ title: title, text: 'Enviando...'}, notification);
            },
            success: function(){
                notification = Notificacao.sucesso({ title: title, text: 'Enviado com sucesso.'}, notification);
                element.trigger("reset");
            },
            error: function(){
                notification = Notificacao.erro({ title: title, text: 'Erro ao enviar a mensagem. Tente novamente.'}, notification);

            }
        });
        return false;
    });

});