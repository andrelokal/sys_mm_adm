<?php

class tables extends dashboard{

	public $msg = "";
	public $data = [];
	public $page = 1;
	public $limit = 10;
	public $order = 'id';
	public $order_active = ['id'];
	public $order_dir = "asc";
	public $search = '';
	public $idValue = 'id';

	public $page_title = "Mesas";
	public $page_class = "tables";
	
	function __construct(){
		$this->table = 'comanda_mesa';
	}

	function get(){

		$table = new $this->table();
		
		//$table->addFilter( 'CategoriaID', $this->category);
		$table->limit = $this->limit;

		if( $this->search ){
			$table->addFilter('codigo','%'.$this->search.'%',"like","OR");
		}

		if( $table->success ){

			$table->page = $this->page;

			$table	->select( 'id', 'ID',NULL,NULL,true,5)
					->select( 'codigo', 'Código',NULL,NULL,true,80)
					->grid();
			
			$return = $table->output();
			$this->data['grid'] = array('data'=> $return->data, 'columns' => $return->columns);

			return true;


		} else {

			$this->msg = "Erro ao executar grid";
			return false;	
		}

	}

	function form( $id = NULL ){
		
		$table = new $this->table( $id );

		if( $table->success ){
			
			$table->input('id')->hide()
				  ->input('codigo',"Código")
				  ->form('Cadastro de Mesa');

			$return = $table->output();
			$this->data = $return->data ;
			return true;

		} else {
			$table->output();	
		}

		//echo SqlFormatter::format($table->query);		
	}


	/*function active( $id ){

	}*/

	function edit( $id ){
		
	}

	/*
	function delete( $id ){
		$this->msg = "Eita";
		return false;
	}
	*/

	

	
	

}