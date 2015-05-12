<?php

defined('Application') || die('<h1>Sem acesso direto</h1>');

class indexControle extends Controle {

	public function __construct(){

		parent::__construct();

		$this->classModelo = $this->App->nomeControle . 'Modelo';
		$this->Modelo = new $this->classModelo();

	}

	public function index( ) {


        $Banner = $this->App->BancoDeDados->query("
                                                SELECT
                                                       item.legenda
                                                      ,item.arquivo
                                                FROM
                                                    ".AppConfig::$PrefixoDB."slides as item

                                                WHERE
                                                    item._status          = 'A'
                                                    AND item._deletado    is null
                                                    AND item.data_inicio  <= '".date('Y-m-d H:i:s')."'
											        AND item.data_fim     >= '".date('Y-m-d H:i:s')."'

                                                ORDER BY item.ordem ASC
                                            ")->fetchAll( PDO::FETCH_OBJ );

        return $this->Modelo->index($Banner);
	}

}

?>