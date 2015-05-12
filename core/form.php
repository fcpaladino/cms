<?php
/**
 * @versão      2.0
 * @package     App - Filipe Cesar Paladino
 * @autor       Filipe Paladino contato@filipepaladino.com
 * @link        http://cms.filipepaladino.com
 */

class Form {

    // Retorno do Formulario
    static private $_return_form = null;


    // Divs
    static private $_form_group = '<div class="form-group"> __CONTEUDO__ </div> ';
    static private $_help_block = '<span class="help-block"> __CONTEUDO__ </span>';
    static private $_conteudo   = '<div class="col-md-xxx"> __CONTEUDO__ </div>';
    static private $_input_group= '<div class="input-group"> __CONTEUDO__ </div>';


    // classes
    static private $_class_label = 'control-label col-md-2';
    static private $_class_input = 'form-control form-control-inline ';


    static private $_btn_cancel  = '';
    static private $tipoform     = '';

    // instacia
    static private $instance;
    static private $app;


    function __construct($xhtml = true) {
        self::$app = Registro::getInstance();
        self::$_return_form = '';
    }


    public static function open($action = '#', $method = 'post', $attributes = array(), $config = array(), $return = false ){
        $str  = "<hr /><br><form action=\"$action\" method=\"$method\" enctype='multipart/form-data'";
        $str .= $attributes ? self::_atributos( $attributes ) . '>': '>';
        $str .= '<div class="form-body">';

        self::$_btn_cancel  = $config['base'];
        self::$tipoform     = $config['tipoform'];

        if($return){ return $str; }
        self::$_return_form .= $str;
    }

    public static function close( $return = false ){

        if( strtolower(self::$tipoform) == 'editar' ){
            $BtnAcao    =  'Atualizar';
            $IconAcao   =  'fa-refresh';

        } else {
            $BtnAcao    =  'Salvar';
            $IconAcao   =  'fa-check';
        }

        $str = '
        </div>
        <hr />
        <div class="form-actions fluid">
            <div class="row">
                <div class="col-lg-9"></div>
                <div class="col-md-3">
                    <a style="float: right; margin: 5px 10px 0;" href="'.self::$_btn_cancel.'" class="icon-btn" ><i class="fa fa-chevron-left text-muted"></i><div>Voltar</div></a>
                    <button style="float: right;" type="submit" class="icon-btn"><i class="fa '.$IconAcao.' text-muted"></i><div>'.$BtnAcao.'</div></button>
                </div>
            </div>
        </div>

        </form>
        ';

        if($return){ return $str; }
        self::$_return_form .= $str;
    }

    public static function Display(){
        return self::$_return_form;
    }



    public static function IntervaloDatas( $valor, $titulo, $descricao, $atributos = null, $return = false){

        $campo = explode('_to_', self::getParametro('name', $atributos));

        $name_inicio = $campo[0];
        $name_fim    = $campo[1];

        $value_inicio   = null;
        $value_fim      = null;

        if($valor){
            $value_inicio   = $valor[0];
            $value_fim      = $valor[1];
        }

        $input1  = '<span class="input-group-addon">De</span>';
        $input1 .= self::mount_Input( 'text', $value_inicio, array('name'=> $name_inicio, 'class'=> self::getParametro('class', $atributos)) );

        $input2  = '<span class="input-group-addon">até</span>';
        $input2 .= self::mount_Input( 'text', $value_fim, array('name'=> $name_fim, 'class'=> self::getParametro('class', $atributos)) );

        $inputs = self::mount_input_group($input1 . $input2);

        $label = self::mount_Label( $titulo, array( 'class'=> self::$_class_label) );
        $help  = self::mount_help_block( $descricao );

        $conteudo = self::mount_conteudo( $inputs.$help , self::getParametro('tamanho', $atributos) );

        if( self::getParametro('ajuda', $atributos) ){
            $conteudo .= self::mount_help( self::getParametro('ajuda', $atributos) );
        }

        $retorno  = self::mount_form_group( $label.$conteudo );



        if($return){ return $retorno; }
        self::$_return_form .= $retorno;

    }



    public static function InputLabel( $valor, $titulo, $descricao, $atributos = null, $return = false ){
        $input  = self::mount_Input( 'hidden', $valor, $atributos );
        $input .= self::mount_Label_file( $valor, array( 'class'=> 'control-label col-md-12', 'style'=>'text-align: left;') );

        $label = self::mount_Label( $titulo, array( 'class'=> self::$_class_label) );
        $help  = self::mount_help_block( $descricao );

        $conteudo = self::mount_conteudo( $input.$help , self::getParametro('tamanho', $atributos) );


        if( self::getParametro('ajuda', $atributos) ){
            $conteudo .= self::mount_help( self::getParametro('ajuda', $atributos) );
        }

        $retorno  = self::mount_form_group( $label.$conteudo );

        if($return){ return $retorno; }
        self::$_return_form .= $retorno;
    }

    public static function Input( $tipo, $valor, $titulo, $descricao, $atributos = null, $return = false ){
        $input = self::mount_Input( $tipo, $valor, $atributos );
        $label = self::mount_Label( $titulo, array( 'class'=> self::$_class_label) );
        $help  = self::mount_help_block( $descricao );

        $conteudo = self::mount_conteudo( $input.$help , self::getParametro('tamanho', $atributos) );

        if( self::getParametro('ajuda', $atributos) ){
            $conteudo .= self::mount_help( self::getParametro('ajuda', $atributos) );
        }

        $retorno  = self::mount_form_group( $label.$conteudo );

        if($return){ return $retorno; }
        self::$_return_form .= $retorno;
    }

    public static function Select( $titulo, $descricao, $lista = array(), $valor = null, $atributos = null, $return = false ){
        $input = self::mount_Select( $lista, $valor, $atributos );
        $label = self::mount_Label( $titulo, array( 'class'=> self::$_class_label) );
        $help  = self::mount_help_block( $descricao );

        $conteudo = self::mount_conteudo( $input.$help , self::getParametro('tamanho', $atributos) );
        if( self::getParametro('ajuda', $atributos) ){
            $conteudo .= self::mount_help( self::getParametro('ajuda', $atributos) );
        }
        $retorno  = self::mount_form_group( $label.$conteudo );

        if($return){ return $retorno; }
        self::$_return_form .= $retorno;
    }

    public static function Textarea( $titulo, $descricao, $valor = null, $atributos = null, $return = false ){
        $input = self::mount_Textarea( $valor, $atributos );
        $label = self::mount_Label( $titulo, array( 'class'=> self::$_class_label) );
        $help  = self::mount_help_block( $descricao );

        $conteudo = self::mount_conteudo( $input.$help , self::getParametro('tamanho', $atributos) );
        $retorno  = self::mount_form_group( $label.$conteudo );

        if($return){ return $retorno; }
        self::$_return_form .= $retorno;
    }

    public static function Checkbox( $valor, $titulo, $descricao, $atributos = null, $return = false ){

        $_name_     = self::getParametro('name', $atributos);

        $check1 = self::mount_Input('checkbox', 1, array('name' => 'remover_arquivo_' . $_name_, 'id' => $_name_)) . ' Remover &numsp;&numsp; ';
        $input  = '';
        $input .= self::mount_Label_file($check1, array('id'=>$_name_, 'class'=>'') );

        $label = self::mount_Label( $titulo, array( 'class'=> self::$_class_label) );
        $help  = self::mount_help_block( $descricao );

        $conteudo = self::mount_conteudo( $input.$help , self::getParametro('tamanho', $atributos) );
        $retorno  = self::mount_form_group( $label.$conteudo );

        if($return){ return $retorno; }
        self::$_return_form .= $retorno;
    }

    public static function File( $valor, $titulo, $descricao, $atributos = null, $tipo_form = null, $return = false ){

        $_name_     = str_replace('[]', '', self::getParametro('name', $atributos));
        $input      = '';
        $checkboxes = '';
        $classFile  = 'fileinput-new';
        $filetemp   = '';
        $thumbnail  = '';

        if( $tipo_form == 'editar' ) {

            // Adiciona um checkbox para poder remover o arquivo
            $check1 = self::mount_Input('checkbox', 1, array('name' => 'remover_arquivo_' . $_name_, 'id' => $_name_)) . ' Remover &numsp;&numsp; ';
            $checkboxes .= self::mount_Label_file($check1, array('id'=>$_name_, 'class'=>'') );

            // Adiciona um checkbox para poder atualizar o arquivo
            $check2 = self::mount_Input('checkbox', 1, array('name' => 'atualizar_arquivo_' . $_name_, 'id' => $_name_)) . ' Atualizar ';
            $checkboxes .= self::mount_Label_file($check2, array('id'=>$_name_, 'class'=>''));


            $filetemp  = self::mount_Input('hidden', $valor, array('name' => 'temp_db_' . $_name_));
            #$classFile = 'fileinput-exists';

            $filename = array_reverse( explode('/', $valor) );
            $filename = $filename[0];
            $thumbnail = '<i class="fa fa-file fileinput-exists"></i>&nbsp;<span class="fileinput-filename">'.$filename.'</span>';
        }

        $input .= self::mount_Input( 'file', $valor, $atributos );
        $label  = self::mount_Label( $titulo, array( 'class'=> self::$_class_label) );
        $help   = self::mount_help_block( $descricao );

        $inputgroup = '
            <div class="form-control uneditable-input" data-trigger="fileinput"> <div class="tmp-filename">'.$thumbnail.'</div> </div>
            <span class="input-group-addon btn default btn-file">
                <span class="fileinput-new">Selecionar</span>
                <span class="fileinput-exists">Alterar</span>
                '.$input.'
            </span>
            <a href="#" class=" input-group-addon btn red fileinput-exists" data-dismiss="fileinput">Remover</a>
        ';
        $conteudo = self::mount_input_group($inputgroup);
        $conteudo = '<div class="fileinput '.$classFile.'" data-provides="fileinput">'.$conteudo.'</div>';

        $conteudo = self::mount_conteudo( $filetemp.$checkboxes.$conteudo.$help , self::getParametro('tamanho', $atributos) );


        $retorno  = self::mount_form_group( $label.$conteudo );
        if($return){ return $retorno; }
        self::$_return_form .= $retorno;
    }



    ///////////////////////////////////////////////////////////////////
    // Cria INPUT
    ///////////////////////////////////////////////////////////////////
    private function mount_Input($type, $value, $atributos ) {
        $str = "<input type=\"$type\" value=\"$value\" ";

        if ( $atributos )
            $str .= self::_atributos( $atributos );

        $str .= ' />';
        return $str;
    }


    ///////////////////////////////////////////////////////////////////
    // Cria SELECT
    ///////////////////////////////////////////////////////////////////
    private function mount_Select( $lista, $valor = null, $atributos = array() ) {
        $str = "<select ";
        if ($atributos) {
            $str .= self::_atributos( $atributos );
        }
        $str .= ">\n";

        $str .= "  <option value=\"\">Selecione ...</option>\n";


        #echo '<pre>'; print_r($lista); echo '</pre>';
        #echo $valor;
        #die();

        if( is_array($lista) ) {
            foreach ($lista as $val => $text) {

                $str .= " <option value=\"$val\" ";

                if( is_array($valor) ){
                    if( in_array($val,$valor) ){
                        $str .= ' selected="selected"';
                    }

                } else if (isset($valor) && ($valor == $val || $valor == $text)) {
                    $str .= ' selected="selected" ';

                }

                $str .= ">$text</option>\n";
            }
        }
        $str .= "</select>";
        return $str;
    }

    ///////////////////////////////////////////////////////////////////
    // Cria TEXTAREA
    ///////////////////////////////////////////////////////////////////
    private function mount_Textarea( $valor = '', $atributos = array() ) {
        $str = "<textarea ";
        if ($atributos) {
            $str .= self::_atributos( $atributos );
        }
        $str .= ">$valor</textarea>";
        return $str;
    }
    ///////////////////////////////////////////////////////////////////









    private function mount_Label_file( $conteudo, $atributos ) {
        $for = isset($atributos['id']) ? $atributos['id'] : '';
        return "<label for='".$for."' ".self::_atributos( $atributos )." >$conteudo</label>";
    }

    private function mount_Label( $conteudo, $atributos ) {
        $for = isset($atributos['id']) ? $atributos['id'] : '';
        return "<label for='".$for."' class='".self::$_class_label."'>$conteudo</label>";
    }

    private function mount_help( $conteudo ) {
        return '<div class="col-md-1"><a class="popovers" data-trigger="hover" data-container="body" data-placement="left" data-content="'.$conteudo.'" data-original-title="Ajuda"><i style="font-size: 20px; color: #cccccc; margin-top: 10px;" class="fa fa-question"></i> </a></div>';
    }

    private function mount_help_block( $conteudo ) {
        return str_replace( '__CONTEUDO__', $conteudo, self::$_help_block );
    }

    private static function mount_form_group( $conteudo ){
        return str_replace( '__CONTEUDO__', $conteudo, self::$_form_group );
    }

    private static function mount_conteudo( $conteudo, $tamanho ){
        $str = str_replace( '__CONTEUDO__', $conteudo, self::$_conteudo );
        $str = str_replace('xxx' , $tamanho, $str);
        return $str;
    }

    private static function mount_input_group( $conteudo ){
        $str = str_replace( '__CONTEUDO__', $conteudo, self::$_input_group );
        return $str;
    }

    private static function getParametro( $key, $atributos ){
        return isset($atributos[$key]) ? $atributos[$key] : '';
    }





    private function ext($file){
        $t = array_reverse( explode('/', $file) );
        $t = array_reverse( explode('.', $t[0]) );
        return $t[0];
    }


    private function _atributos( $attr_array ) {
        $str = '';
        $min_atts = array('checked', 'disabled', 'readonly', 'required', 'autofocus', 'novalidate', 'formnovalidate'); // html5

        $_not = array('tamanho', 'ajuda');

        foreach( $attr_array as $key=>$val ) {

            if( !in_array($key, $_not)){

                if ( in_array($key, $min_atts) ) {
                    if ( !empty($val) ) {
                        $str .= " $key=\"$key\"";
                    } else {
                        $str .= "$key";
                    }
                } else {
                    $str .= " $key=\"$val\"";
                }
            }

        }
        return $str;
    }










    static
    public function init()
    {
        if (self::$instance === null) {
            self::$instance = new Form();
        }

        return self::$instance;
    }
}