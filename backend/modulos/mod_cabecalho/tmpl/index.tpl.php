<?php defined('Application') || die('<h1>Sem acesso direto</h1>'); ?>

<div class="page-header-inner">
    <div class="page-logo">
        <a target="_blank" class="brand" href="{FRONDEND}">{NomeEmpresa}</a>
        <div class="menu-toggler sidebar-toggler hide">
        </div>
    </div>
    <a href="javascript:;" class="menu-toggler responsive-toggler" data-toggle="collapse" data-target=".navbar-collapse">
    </a>
    <div class="top-menu">
        <ul class="nav navbar-nav pull-right">

            {ModuloUltimosContatos}


            <!-- BEGIN ADMINISTRADOR -->
            <li class="dropdown dropdown-user">
                <a href="{BASE}configuracoes" class="dropdown-toggle">
                    <span class="username username-hide-on-mobile"> Configurações </span>
                    &numsp;
                </a>
            </li>

            <li class="dropdown dropdown-user">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                    <span class="username username-hide-on-mobile"> Administradores </span>
                    <i class="fa fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-default">
                    <li><a href="{BASE}usuarios"><i class="fa fa-user"></i> Usúarios </a></li>
                    <li><a href="{BASE}usuarios-grupos"><i class="fa fa-users"></i> Grupos </a></li>
                </ul>
            </li>
            <!-- END ADMINISTRADOR -->

            <li class="dropdown dropdown-user">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-close-others="true">
                    <img alt="" class="img-circle" src="{Avatar}"/>
                    <span class="username username-hide-on-mobile"> {NomeUsuario} </span>
                    <i class="fa fa-angle-down"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-default">
                    <li><a href="{BASE}login/Sair"><i class="fa fa-sign-out"></i> Sair </a></li>
                </ul>
            </li>




        </ul>
    </div>
</div>
