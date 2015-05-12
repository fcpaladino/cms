<?php
defined('Application') || die('<h1>Sem acesso direto</h1>');

class mod_ultimoscontatos extends Modelo {

	public function __construct(){

		parent::__construct();

	}

	public function renderiza( $config = null ) {

		$this->tpl = $this->CarregarTemplateModulo( get_class($this), 'index.tpl.php' );


        $TotalNew = $this->App->BancoDeDados->query("
                                                SELECT
                                                       COUNT(item.id)
                                                FROM
                                                    ".AppConfig::$PrefixoDB."contato as item
                                                WHERE
                                                    item._status          = 'A'
                                                    AND item._deletado   is null
                                                    AND item.aberto       = '0'
                                            ")->fetchColumn();


        $Itens = $this->App->BancoDeDados->query("
                                                SELECT
                                                       item.nome
                                                      ,item.id
                                                      ,item.mensagem
                                                      ,item.aberto
                                                      ,item._criado
                                                FROM
                                                    ".AppConfig::$PrefixoDB."contato as item
                                                WHERE
                                                    item._status          = 'A'
                                                    AND item._deletado   is null

                                                ORDER BY
                                                    item._criado DESC, item.aberto ASC

                                                LIMIT 10
                                            ")->fetchAll( PDO::FETCH_OBJ );

        foreach ($Itens as $item) {

            $data   = $this->Sistema->ConverterData($this->Sistema->ConverterData($item->_criado));

            $horas  = $this->Sistema->DiffDate( $data, date('Y-m-d'), 'H');
            $dias   = $this->Sistema->DiffDate( $data, date('Y-m-d'), 'D');
            $meses  = $this->Sistema->DiffDate( $data, date('Y-m-d'), 'M');

            if( (int)$horas <= 2 ){
                $msg_data = $horas . 'h atrás';

            }else if( (int)$dias <= 28 ) {
                $msg_data = $dias . ' dias atrás';

            } else {
                $msg_data = $this->Sistema->ConverterData($item->_criado);
            }


            $this->tpl->atribuir('codigo',          $item->id);
            $this->tpl->atribuir('nome',            $item->nome);
            $this->tpl->atribuir('mensagem',        $this->Sistema->limitaCaracteres($item->mensagem, 100) . '...');
            $this->tpl->atribuir('data',            $msg_data );

            $this->tpl->block('LISTA');

            $this->tpl->limpa('nome');
            $this->tpl->limpa('mensagem');
            $this->tpl->limpa('data');
            $this->tpl->limpa('codigo');
        }


        if( (int)$TotalNew > 0 ){
            $this->tpl->atribuir('totalMensagens',             $TotalNew);
            $this->tpl->block('QTDE_NOVA_MENSAGEM');
        }

        return $this->tpl->salva();

	}

}

?>