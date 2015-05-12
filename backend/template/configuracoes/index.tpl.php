{HTMLHeader}
{SiteTopo}

<div class="row">
    <div class="col-md-12">


        <div class="row">
            <div class="col-md-12">

                <ul class="nav nav-tabs">
                    <li class="active"><a href="#geral" data-toggle="tab" aria-expanded="true">Geral</a></li>
                    <li><a href="#suporteNavegador" data-toggle="tab" aria-expanded="false">Suporte de navagador </a></li>
                    <li><a href="#redeSocial" data-toggle="tab" aria-expanded="false">Rede social </a></li>
                    <li><a href="#notificacoes" data-toggle="tab" aria-expanded="false">E-mails Notificações </a></li>
                    <li><a href="#configuracaoSmtp" data-toggle="tab" aria-expanded="false">Configuração SMTP </a></li>
                    <li><a href="#analises" data-toggle="tab" aria-expanded="false">Análises </a></li>
                </ul>
                <form action="{componenteUrl}/index-post" method="post" class="form-horizontal" id="formPrincipal">
                <div class="tab-content">

                    <div class="tab-pane fade active in" id="geral">
                        <!-- geral -->
                        <h3>Geral</h3>
                        <div class="form-body">
                            <div class="form-group">
                                <label for="" class="control-label col-md-2">Nome do site <span class="">*</span></label>
                                <div class="col-md-5">
                                    <input type="text"   maxlength="30" value="<?php echo $this->App->config->nome_empresa; ?>" name="frm_config[nome_empresa]" class="form-control ">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="" class="control-label col-md-2">Título do site<span class="">*</span></label>
                                <div class="col-md-5">
                                    <input type="text"  maxlength="100" value="<?php echo $this->App->config->site_seo_titulo; ?>" name="frm_config[site_seo_titulo]" class="form-control ">
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="col-md-2 control-label">Palavras chaves:</label>
                                <div class="col-md-10">
                                    <input type="text" value="<?php echo $this->App->config->site_seo_palavrachave; ?>" name="frm_config[site_seo_palavrachave]" class="input-tag">
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-md-2 control-label">Descrição*:</label>
                                <div class="col-md-10">
                                    <textarea class="form-control maxlength-handler" rows="8" name="frm_config[site_seo_descricao]" maxlength="255"><?php echo $this->App->config->site_seo_descricao; ?></textarea>
									<span class="help-block">max 255 caracteres </span>
                                </div>
                            </div>
                        </div>

                        <!-- geral -->
                    </div>
                    <div class="tab-pane fade" id="suporteNavegador">
                        <h3>Suporte de navagador</h3>
                        <p>Ativar mensagem pra avisar que o navegador não é suportado.</p>
                        <?php
                        $ativaSim = $this->App->config->ativa_compatibilidade == 1 ? 'checked' : '';
                        $ativaNao = $this->App->config->ativa_compatibilidade == 0 ? 'checked' : '';

                        $tipoSim = $this->App->config->tipo_compatibilidade == 1 ? 'checked' : '';
                        $tipoNao = $this->App->config->tipo_compatibilidade == 0 ? 'checked' : '';
                        ?>
                        <div class="form-group">
                            <label class="control-label col-md-2">Ativar ?</label>
                            <div class="col-md-10">
                                <label> <input type="radio" name="frm_config[ativa_compatibilidade]" value="1" <?php echo $ativaSim;?> > Sim </label>
                                <label> <input type="radio" name="frm_config[ativa_compatibilidade]" value="0"  <?php echo $ativaNao;?>> Não </label>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="control-label col-md-2">Tipo</label>
                            <div class="col-md-10">
                                <label> <input type="radio" name="frm_config[tipo_compatibilidade]" value="1" <?php echo $tipoSim;?> style="padding: 0!important;" > Fixo </label>
                                <label> <input type="radio" name="frm_config[tipo_compatibilidade]" value="0" <?php echo $tipoNao;?> > Não Fixo </label>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="redeSocial">
                        <h3>Rede social</h3>
                        <p>Informe os links das suas páginas nas redes sociais.</p>
                        <div class="form-group">
                            <label for="" class="control-label col-md-2">Facebook</label>
                            <div class="col-md-5">
                                <input type="text"  maxlength="255" value="<?php echo $this->App->config->social_facebook; ?>" name="frm_config[social_facebook]" class="form-control ">
                                <p class="help-block">Ex.: https://www.facebook.com/FacebookBrasil</p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label col-md-2">Instagram</label>
                            <div class="col-md-5">
                                <input type="text"  maxlength="255" value="<?php echo $this->App->config->social_instagram; ?>" name="frm_config[social_instagram]" class="form-control ">
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="notificacoes">
                        <h3>E-mails para receber notificações do site</h3>
                        <h4>Contato</h4>
                        <div class="form-group">
                            <label for="" class="control-label col-md-2">Para</label>
                            <div class="col-md-5">
                                <input type="text"  maxlength="255" value="<?php echo $this->App->config->contato_email; ?>" name="frm_config[contato_email]" class="form-control ">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label col-md-2">Cópia</label>
                            <div class="col-md-5">
                                <input type="text"  maxlength="255" value="<?php echo $this->App->config->contato_emailcopia; ?>" name="frm_config[contato_emailcopia]" class="form-control ">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label col-md-2">Cópia oculta</label>
                            <div class="col-md-5">
                                <input type="text"  maxlength="255" value="<?php echo $this->App->config->contato_emailcopiaoculta; ?>" name="frm_config[contato_emailcopiaoculta]" class="form-control ">
                            </div>
                        </div>

                        <h4>Trabalhe Conosco</h4>
                        <div class="form-group">
                            <label for="" class="control-label col-md-2">Para</label>
                            <div class="col-md-5">
                                <input type="text"  maxlength="255" value="<?php echo $this->App->config->trabalheconosco_email; ?>" name="frm_config[trabalheconosco_email]" class="form-control ">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label col-md-2">Cópia</label>
                            <div class="col-md-5">
                                <input type="text"  maxlength="255" value="<?php echo $this->App->config->trabalheconosco_emailcopia; ?>" name="frm_config[trabalheconosco_emailcopia]" class="form-control ">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label col-md-2">Cópia oculta</label>
                            <div class="col-md-5">
                                <input type="text"  maxlength="255" value="<?php echo $this->App->config->trabalheconosco_emailcopiaoculta; ?>" name="frm_config[trabalheconosco_emailcopiaoculta]" class="form-control ">
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="configuracaoSmtp">
                        <h3>Configuração SMTP</h3>
                        <div class="form-group">
                            <label for="" class="control-label col-md-2">Servidor</label>
                            <div class="col-md-5">
                                <input type="text"  maxlength="255" value="<?php echo $this->App->config->smtp_servidor; ?>" name="frm_config[smtp_servidor]" class="form-control smtp_servidor">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label col-md-2">Porta</label>
                            <div class="col-md-5">
                                <input type="text"  maxlength="255" value="<?php echo $this->App->config->smtp_porta; ?>" name="frm_config[smtp_porta]" class="form-control smtp_porta">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label col-md-2">Segurança</label>
                            <div class="col-md-2">
                                <select name="frm_config[smtp_seguranca]" class="form-control smtp_seguranca">
                                    <option value="" <?php echo ($this->App->config->smtp_seguranca == "") ? "selected" : ""; ?>>Nenhuma</option>
                                    <option value="ssl" <?php echo ($this->App->config->smtp_seguranca == "ssl") ? "selected" : ""; ?>>SSL</option>
                                    <option value="tls" <?php echo ($this->App->config->smtp_seguranca == "tls") ? "selected" : ""; ?>>TLS</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label col-md-2">E-mail</label>
                            <div class="col-md-5">
                                <input type="text"  maxlength="255" value="<?php echo $this->App->config->smtp_email; ?>" name="frm_config[smtp_email]" class="form-control smtp_email">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label col-md-2">Senha</label>
                            <div class="col-md-5">
                                <input type="text"  maxlength="255" value="<?php echo $this->App->config->smtp_senha; ?>" name="frm_config[smtp_senha]" class="form-control smtp_senha">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label col-md-2">Responder para</label>
                            <div class="col-md-5">
                                <input type="text"  maxlength="255" value="<?php echo $this->App->config->smtp_responder_para; ?>" name="frm_config[smtp_responder_para]" class="form-control smtp_responder_para">
                                <p class="help-block">E-mail que irá receber a resposta caso o cliente tente responder um e-mail enviado pelo site.</p>
                            </div>
                            <div class="col-md-3">
                                <button type="button" data-action="{componenteUrl}" class="btn blue btn-block btn-smtp-teste"> Testar configurações de SMTP</button>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="" class="control-label col-md-2"></label>
                            <div class="col-md-5">
                                <div class="smtp-teste-resposta label"></div>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane fade" id="analises">
                        <h3>Análises</h3>
                        <div class="form-group">
                            <label for="" class="control-label col-md-2"> Google analytics </label>
                            <div class="col-md-5">
                                <textarea name="frm_config[analises_google_analytics]" rows="7" style="width: 100%;"><?php echo $this->App->config->analises_google_analytics; ?></textarea>
                                <p class="help-block">Insira o código fornecido pelo google.</p>
                            </div>
                        </div>
                    </div>

                </div>


                </form>


            </div>
        </div>
























    </div>
</div>

{SiteRodape}
{HTMLFooter}
