<?php

class profile extends dashboard{

	public $msg = "";
	public $data = [];
	public $page = 1;
	public $limit = 10;
	public $order = 'id';
	public $order_active = [];
	public $order_dir = "asc";
	public $search = '';
	public $idValue = 'id';

	function __construct(){

		$this->table = 'config';

	}

	/*function active( $id ){

	}*/

	/*function edit( $id ){
		
	}*/

	function form( $id = NULL ){
		
		$table = new config( 1 );

		if( $table->success ){
			$table->input('id')->hide()
				  ->input('nome_empresa',"Nome da Empresa")
				  ->input('valor_entregador_delivery',"Valor Entregador")->setType('money')
				  ->form('Editar Perfil');

			$return = $table->output();
			$this->data = $return->data ;
			return true;

		} else {
			$table->output();	
		}

		//echo SqlFormatter::format($table->query);		
	}

	function save( $request = NULL, $before = NULL, $after = NULL){

		$request = json_decode($request, true);	
		$request['id'] = 1;
		return parent::save( $request );

	}

}