{HTMLHeader}


<div class="menu-toggler sidebar-toggler">
</div>

<div class="logo">
    <a href="#">
        <!--<img src="{PATH_IMG}/logo.png" alt=""/>-->
    </a>
</div>

<div class="content">
    <input type="hidden" name="token" id="token" value="">

    <form class="login-form formulario-validar" action="{BASE_URL}login/validar" method="post">
        <h3 class="form-title">Área Restrita</h3>
        {ModuloMensagensAviso}
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">Usúario ou e-mail</label>
            <input class="form-control form-control-solid placeholder-no-fix" type="text" autocomplete="off" value="" placeholder="Usúario ou e-mail" name="frm_login_user"/>
        </div>
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">Senha</label>
            <input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" value="" placeholder="Senha" name="frm_login_pass"/>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary col-md-4 uppercase">Login</button>
            <!--<a href="#" id="" class="esqueceu-sua-senha">esqueci minha senha</a>-->
        </div>
    </form>


    <form class="forget-form  formulario-email formulario-valida" action="{BASE_URL}login/esqueci-minha-senha" method="post"  data-codigo="false"
        <h3>Esqueci a Senha ?</h3>
        <p>Digite seu endereço de e-mail abaixo para redefinir sua senha.</p>

        <div class="form-group  campo-email">
            <input class="form-control placeholder-no-fix campo-email-codigo" value="" type="text" autocomplete="off" placeholder="E-mail" name="frm_email_codigoVerificador"/>
        </div>

        <div class="form-actions">
            <button type="button" id="back-btn" class="form-email-cancelar button button-rounded button-flat">Voltar</button>
            <button type="submit" class="form-email-enviar button button-rounded button-flat-primary uppercase pull-right">Enviar</button>
        </div>
    </form>


    <form class="login-form formulario-alterar-senha" action="{BASE_URL}login/alterar-senha" method="post">
        <h3 class="form-title">Alteração da senha</h3>

        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">Senha</label>
            <input class="form-control form-control-solid placeholder-no-fix" type="password" autocomplete="off" value="" placeholder="Senha" name="frm_senha"/>
        </div>
        <div class="form-group">
            <label class="control-label visible-ie8 visible-ie9">Confirma a senha</label>
            <input class="form-control form-control-solid placeholder-no-fix senha senha_confirmar" type="password" autocomplete="off" value="" placeholder="Confirma a senha" name="frm_senha_confirmar"/>
        </div>
        <div class="form-actions">
            <button type="button" id="back-btn" class="form-alterar-senha-cancelar button button-rounded button-flat">Voltar</button>
            <button type="submit" class="form-alterar-senha-alterar button button-rounded button-flat-primary uppercase pull-right">Alterar</button>
        </div>
    </form>





</div>
<div class="copyright">
    <!--2014 © Metronic. Admin Dashboard Template.-->
</div>
{HTMLFooter}