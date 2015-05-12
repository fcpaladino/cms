<?php defined('Application') || die('<h1>Sem acesso direto</h1>'); ?>
<ul>
    <li class="hidden-l home">
        <a class="animsition-link" href="{BASE}index" title="Página inicial">
            <div class="single-item">
                <div class="name">Principal</div>
            </div>
        </a>
    </li>
    <li id="carro" class="novos {veiculosnovosactive}">
        <a class="animsition-link" href="{BASE}veiculos-novos" title="Conheça nossos carros novos">
            <div class="single-item">
                <div class="name">Novos</div>
            </div>
        </a>
        <ul>
            <div id="main-carousel" class="header">
                <!-- BEGIN BLOCO_VEICULOS -->
                <div class="single-item">
                    <!-- BEGIN VEICULOS -->
                    <li>
                        <a class="animsition-link" href="{BASE}veiculos-novos/{veiculo_url}" title="{veiculo_nome}">
                            <div class="single-item-cont">
                                <img class="stab-img-r" src="{veiculo_imagem}" alt="{veiculo_legenda}"/>
                                <div class="name">{veiculo_nome}</div>
                            </div>
                        </a>
                    </li>
                    <!-- END VEICULOS -->
                </div>
                <!-- END BLOCO_VEICULOS -->
            </div>
        </ul>
    </li>
    <li class="multimarcas {multimarcasactive}">
        <a class="animsition-link" href="{BASE}multimarcas" title="Conheça nossos seminovos">
            <div class="single-item">
                <div class="name">Multimarcas</div>
            </div>
        </a>
    </li>
    <li class="revisoes {revisoesactive}">
        <a class="animsition-link" href="{BASE}revisoes" title="Marque uma revisão">
            <div class="single-item">
                <div class="name">Revisões</div>
            </div>
        </a>
    </li>
    <li class="acessorios {acessoriosepecasactive}">
        <a class="animsition-link" href="{BASE}acessorios-e-pecas" title="Procure por acessórios e peças">
            <div class="single-item">
                <div class="name">Acessórios e peças</div>
            </div>
        </a>
    </li>
    <li class="servicos {servicosfinanceirosactive}">
        <a class="animsition-link" href="{BASE}servicos-financeiros" title="Conheça nossos serviços">
            <div class="single-item">
                <div class="name">Serviços Financeiros</div>
            </div>
        </a>
    </li>




    <li class="hidden-l institucional">
        <a class="animsition-link" href="{BASE}institucional" title="Conheça nossa história">
            <div class="single-item">
                <div class="name">Institucional</div>
            </div>
        </a>
    </li>
    <li class="hidden-l vendas">
        <a class="animsition-link" href="{BASE}vendas-especiais" title="">
            <div class="single-item">
                <div class="name">Vendas Especiais</div>
            </div>
        </a>
    </li>
    <li class="hidden-l test">
        <a class="animsition-link" href="{BASE}test-drive" title="Agende um test drive">
            <div class="single-item">
                <div class="name">Test Drive</div>
            </div>
        </a>
    </li>
    <li class="hidden-l trabalhe">
        <a class="animsition-link" href="{BASE}trabalhe-conosco" title="Faça parte da nossa equipe">
            <div class="single-item">
                <div class="name">Trabalhe Conosco</div>
            </div>
        </a>
    </li>
    <li class="hidden-l contato">
        <a class="animsition-link" href="{BASE}contato" title="Entre em contato conosco">
            <div class="single-item">
                <div class="name">Contato</div>
            </div>
        </a>
    </li>
    <li class="hidden-l volks">
        <a class="animsition-link" href="{BASE}http://www.vw.com.br/pt.html" target="_blank" title="Conheça a Volkswagem Brasil">
            <div class="single-item">
                <div class="name">Volkswagen Brasil</div>
            </div>
        </a>
    </li>
    <!--<li class="hidden-l margin-t youtube">
        <a class="animsition-link" href="{BASE}http://www.youtube.com.br" target="_blank" title="Se inscreva no nosso canal do Youtube">
            <div class="single-item">
                <div class="name">Youtube</div>
            </div>
        </a>
    </li>
    <li class="hidden-l facebook">
        <a class="animsition-link" href="{BASE}http://www.facebook.com.br" target="_blank" title="Curta nossa página do Facebook">
            <div class="single-item">
                <div class="name">Facebook</div>
            </div>
        </a>
    </li>
    <li class="hidden-l twitter">
        <a class="animsition-link" href="{BASE}http://www.twitter.com.br" target="_blank" title="Nos siga no Twitter">
            <div class="single-item">
                <div class="name">Twitter</div>
            </div>
        </a>
    </li>-->
</ul>