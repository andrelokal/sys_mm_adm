<?php

class delivery_ctl extends dashboard{

	public $msg = "";
	public $data = [];
	public $page = 1;
	public $limit = 10;
	public $idValue = 'id';
	public $venda_id = '';
	
	function __construct(){

		$this->table = 'delivery';

	}
	
	function form( $id = NULL ){
		
		$table = new delivery( $id );

		if( $table->success ){
			$func = new funcionario();
			$func	->select( 'id', '','value')
					->select( 'nome', '','text')
					->where(" delivery = 'Y' ");
			$retFunc = $func->grid()->result;

			$table->input('id')->hide()
				  ->input('venda_id')->setValue( $this->venda_id )->hide()
				  ->input('ent_1',"Entregador")->inputSelect( $retFunc )->setType('select-range')->required('yes')
				  ->form('Cadastro Delivrey');

			$return = $table->output();
			$this->data = $return->data ;
			return true;

		} else {
			$table->output();	
		}
		//echo SqlFormatter::format($table->query);		
	}

	function save( $request = NULL, $before = NULL, $after = NULL){

		$post = json_decode($request, true);

		if( !$post['ent_1'] ){
			$this->msg = "Entregador Obrigatório!";
			return false;
		}	

		return parent::save( $request );

	}
	

	

}