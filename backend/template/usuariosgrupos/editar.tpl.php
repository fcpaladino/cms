{HTMLHeader}
{SiteTopo}

<div class="row">
    <div class="col-md-12 form">

        <form id="formPrincipal" action="{componenteUrl}" method="post">
            <input type="hidden" name="id" value="{id}"/>

        <div class="col-lg-12 margin-bottom-20">
            <hr/>
            <div class="row">
                <div class="col-md-6">
                    <input type="text" name="nome_grupo" value="{NomeGrupo}" placeholder="Nome do Grupo" class="form-control">
                </div>
            </div>
        </div>

        <div class="col-lg-12">
            <h3 class="form-section">Permissões</h3>
            <div class="row">
                <div class="col-md-12">

                    <table class="table table-striped table-bordered table-hover" id="tableListagem">
                        <thead>
                        <tr>
                            <th style="text-align: center;">Menu</th>
                            <!-- BEGIN REGRA_TITULO -->
                            <th style="text-align: center;">{RegraTitulo}</th>
                            <!-- END REGRA_TITULO -->
                            <th style="text-align: center;">Todos</th>
                        </tr>
                        </thead>
                        <!-- BEGIN LISTA -->
                        <tr>
                            <td width="31%"><label class="checkbox" for="menu{MenuId}"><input id="menu{MenuId}" name="menu[]" value="{MenuId}" {MenuSelected} class="group-checkable-{MenuId} group-menu-{MenuId}" type="checkbox"> &numsp;{MenuTitulo}</label></td>
                            <!-- BEGIN REGRA_LISTA -->
                            <td width="15%" align="center"><label class="checkbox" for="regra{RegraId}{MenuId}"><input id="regra{RegraId}{MenuId}" {RegraSelected} name="regra[{MenuId}][]" value="{RegraId}" class="group-checkable-{MenuId} select-menu-checkbox"  data-regra=".group-checkable-{MenuId}" data-menu=".group-menu-{MenuId}" type="checkbox"></label></td>
                            <!-- END REGRA_LISTA -->
                            <td width="7%" align="center"><label class="checkbox" for="todos{MenuId}"><input id="todos{MenuId}" name="" value="" {MarcarTodos} class="group-checkbox" data-set=".group-checkable-{MenuId}" type="checkbox"></label></td>
                        </tr>
                        <!-- END LISTA -->
                        <tbody>
                        </tbody>
                    </table>

                </div>
            </div>
        </div>


        </form>
    </div>
</div>


{SiteRodape}
{HTMLFooter}