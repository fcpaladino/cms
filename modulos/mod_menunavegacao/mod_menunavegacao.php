<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class mod_menunavegacao extends Modelo {

	public function __construct(){

		parent::__construct();

	}

	public function renderiza( $config = null ) {

		$this->tpl = $this->CarregarTemplateModulo( get_class($this), 'index.tpl.php' );
        $this->tpl->atribuir('iecompatibility',    $this->Modulos->carrega('iecompatibility'));

        $Controle	= '';
        if( isset($this->router[0]) ) {
            $Controle	= str_replace('-', '', strtolower(trim($this->router[0])));
        }


        switch($Controle){
            case "veiculosnovos":
                $this->tpl->atribuir('veiculosnovosactive',                 'active');
                break;

            case "multimarcas":
                $this->tpl->atribuir('multimarcasactive',                   'active');
                break;

            case "revisoes":
                $this->tpl->atribuir('revisoesactive',                      'active');
                break;

            case "acessoriosepecas":
                $this->tpl->atribuir('acessoriosepecasactive',              'active');
                break;

            case "servicosfinanceiros":
                $this->tpl->atribuir('servicosfinanceirosactive',           'active');
                break;
        }




        $Veiculos = $this->App->BancoDeDados->query("
                                                SELECT
                                                       item.nome
                                                      ,item.url
                                                      ,item.resumo
                                                      ,item.id
                                                      ,(
                                                        SELECT img.arquivo
                                                        FROM ".AppConfig::$PrefixoDB."veiculos_novos_fotos as img
                                                        WHERE img.item_id = item.id AND img._status = 'A' AND img._deletado is null
                                                        ORDER BY img.destaque DESC, img.ordem ASC
                                                        LIMIT 1
                                                      ) as imagem
                                                      ,(
                                                        SELECT img.legenda
                                                        FROM ".AppConfig::$PrefixoDB."veiculos_novos_fotos as img
                                                        WHERE img.item_id = item.id AND img._status = 'A' AND img._deletado is null
                                                        ORDER BY img.destaque DESC, img.ordem ASC
                                                        LIMIT 1
                                                      ) as legenda
                                                FROM
                                                    ".AppConfig::$PrefixoDB."veiculos_novos as item

                                                WHERE
                                                    item._status          = 'A'
                                                    AND item._deletado   is null

                                            ")->fetchAll( PDO::FETCH_OBJ );


        for( $i = 0; $i < count($Veiculos); $i++){
            $item = $Veiculos[$i];

            $this->tpl->atribuir('veiculo_nome',          $item->nome);
            $this->tpl->atribuir('veiculo_imagem',        BASE . $item->imagem);
            $this->tpl->atribuir('veiculo_legenda',       $item->legenda);
            $this->tpl->atribuir('veiculo_url',           $item->url);

            $this->tpl->block('VEICULOS');

            if( $i % 8 == 0 or $i == 0){
                $this->tpl->block('BLOCO_VEICULOS');
            }

            $this->tpl->limpa('veiculo_nome');
            $this->tpl->limpa('veiculo_imagem');
            $this->tpl->limpa('veiculo_legenda');
            $this->tpl->limpa('veiculo_url');
        }


		return $this->tpl->salva();

	}

}

?>