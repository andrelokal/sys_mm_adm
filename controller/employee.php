<?php

class employee extends dashboard{

	public $msg = "";
	public $data = [];
	public $page = 1;
	public $limit = 10;
	public $order = 'id';
	public $order_active = ['id','cnpj','pessoa.nome'];
	public $order_dir = "asc";
	public $search = '';
	public $idValue = 'id';

	function __construct(){

		$this->table = 'funcionario';

	}

	function get(){

		$table = new $this->table();
		$table->limit = $this->limit;

		if( $this->search ){
			$table->addFilter('id',$this->search,"=","OR");
			$table->addFilter('nome','%'.$this->search.'%',"like","OR");
			$table->addFilter('login','%'.$this->search.'%',"like","OR");
		}

		if( $table->success ){

			$table->page = $this->page;

			$table	->select( 'id', 'ID',NULL,NULL,true,5)
					->select( 'nome', 'Nome',NULL,NULL,true,40)
					->select( 'login', 'Login',NULL,NULL,true,40)
					->where( " a.tipo_us = 'N' " )					
					->grid();
			
			$return = $table->output();
			$this->data['grid'] = array('data'=> $return->data, 'columns' => $return->columns);

			return true;


		} else {

			$this->msg = "Erro ao executar grid";
			return false;	
		}

	}

	/*function active( $id ){

	}*/

	function edit( $id ){
		
	}

	function form( $id = NULL ){
		
		$table = new funcionario( $id );

		if( $table->success ){
			$table->input('id')->hide()
				  ->input('nome',"Nome")				  
				  ->input('login',"Login")
				  ->input('delivery',"Entregador")->inputSelect( [ ["text"=>"Sim","value"=>"Y"], ["text"=>"Não","value"=>"N"] ] )
				  ->form('Cadastro Funcionário');

			$return = $table->output();
			$this->data = $return->data ;
			return true;

		} else {
			$table->output();	
		}

		//echo SqlFormatter::format($table->query);		
	}

	function save( $request = NULL, $before = NULL, $after = NULL ){
	
		return parent::save( $request, 
		function( $post = NULL ) // BEFORE
		{	
			$table = new pessoa();
			$table->status = 'AT';
			$table->tipo = 'F';
			$success = $table->save();
			
			if( $success ){
				$post['pessoa_id'] = $table->last_insert_id;
				return $post;
			} 

			$this->msg = $table->msg;	
			return true;

		}, 
		function( $post = NULL ) // AFTER
		{	
			
			return true;	
		});


	}

	function delete( $id ){
		return parent::delete_($id);
	}



	

}