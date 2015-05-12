var Notificacao = function(){

    var icones = {
         info      : '<i style="color:#e4f022;" class="fa fa-exclamation"></i>'
        ,success   : '<i style="color:#47e229;" class="fa fa-check-circle"></i>'
        ,error     : '<i style="color:#e24329;" class="fa fa-warning"></i>'
        ,send      : '<i style="color:#dddddd;" class="fa fa-sign-in"></i>'
    };

    return {

        add: function( options, id ){
            if(id){
                if (this.exists(id)) {
                    return this.edit(id, options);
                }else {
                    return Snarl.addNotification(options);
                }

            } else {
                return Snarl.addNotification(options);
            }
        },

        exists: function(id){
            return Snarl.exists(id);
        },

        edit: function(id, options){
            return Snarl.editNotification(id, options);
        },



        sucesso: function( options, id){
            options['icon'] = icones.success;
            return this.add( options, id );
        },

        erro: function( options, id){
            options['icon'] = icones.error;
            return this.add( options, id );
        },

        info: function( options, id){
            options['icon'] = icones.info;
            return this.add( options, id );
        },

        send: function( options, id){
            options['icon'] = icones.send;
            return this.add( options, id );
        }







    }



}();