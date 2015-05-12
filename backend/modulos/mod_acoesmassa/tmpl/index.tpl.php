<!-- BEGIN BOTAO_VOLTAR -->
<a href="javascript:;" class="icon-btn" onclick="self.location='{componentePai}'"><i class="fa fa-chevron-left text-muted"></i><div>Voltar</div></a>
<!-- END BOTAO_VOLTAR -->

<!-- BEGIN EXTERNO -->
<a href="javascript:;" class="{class}" onclick=" self.location ='{componenteUrl}{acao}{url_compl}'">{icon}<div>{title}</div></a>
<!-- END EXTERNO -->

<!-- BEGIN BLOCO_ACOES -->
<a href="#" class="icon-btn dropdown-toggle" data-toggle="dropdown" data-hover="dropdown" data-delay="1000" data-close-others="true">
    <i class="fa fa-cogs text-muted"></i>
    <div>Ações</div>
</a>
<ul class="dropdown-menu pull-right" role="menu">
    <!-- BEGIN INTERNO -->
    <li><a class="modal-acao-lista-tabela" data-modal="{modal}" data-acao="{acao}" data-componente="{componenteUrl}" data-parametro="{url_compl}" href="#">{icon} &numsp; {title}</a></li>
    <!-- END INTERNO -->
</ul>
<!-- END BLOCO_ACOES -->
