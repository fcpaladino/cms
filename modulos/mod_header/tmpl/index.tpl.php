<?php defined('Application') || die('<h1>Sem acesso direto</h1>'); ?>
<div id="header">
    <a id="menu-pull" href="#"></a>
    <div id="up-header">
        <div id="head-up-search">
            <input class="search-input" type="text" data-value="Buscar..." title="Faça uma busca no site">
            <a class="animsition-link search-submit" href="#" title="Buscar"></a>
        </div>
        <ul class="left">
            {MenuInstitucional}
        </ul>
        <ul class="right">
            {MenuLinks}
        </ul>
    </div>

    <div id="sub-header">
        <div id="sub-header-cont">
            <a class="animsition-link" id="logo-menu" href="{BASE}" title="Página inicial">
                <img src="{IMG}logo-menu.png" alt="Logotipo Norpave"/>
            </a>
            {MenuNavegacao}
            <div id="head-search">
                <input class="search-input" type="text" data-value="Buscar..." title="Faça uma busca no site">
                <a class="animsition-link search-submit" href="#" title="Buscar"></a>
            </div>
        </div>
    </div>


</div>