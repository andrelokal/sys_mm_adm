<?php

class company extends dashboard{

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

		$this->table = 'juridica';

	}

	function get(){

		$table = new $this->table();
		$table->limit = $this->limit;

		if( $this->search ){
			$table->addFilter('id',$this->search,"=","OR");
			$table->addFilter('pessoa.nome','%'.$this->search.'%',"like","OR");
			$table->addFilter('cnpj','%'.$this->search.'%',"like","OR");
			$table->addFilter('razao_social','%'.$this->search.'%',"like","OR");
			$table->addFilter('nome_fantasia','%'.$this->search.'%',"like","OR");
		}

		if( $table->success ){

			$table->page = $this->page;

			$table	->select( 'id', 'ID',NULL,NULL,true,5)
					->select( 'cnpj', 'CNPJ',NULL,NULL,true,20)
					->select( 'razao_social', 'Razão Social',NULL,NULL,true,40)
					//->select( 'tem_estoque', 'Tem?',NULL,NULL,true,10," CASE a.tem_estoque WHEN 'Y' THEN 'Sim' WHEN 'N' THEN 'Não' END ")
					//->join('categoria', 'categoria_id', 'join', 'id')
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
		
		$table = new juridica( $id );

		if( $table->success ){
			$table->input('id')->hide()
				  ->input('razao_social',"Razão Social")
				  ->input('cnpj',"CNPJ")->setType('cnpj')				  
				  ->input('nome_fantasia',"Nome Fantasia")
				  ->form('Cadastro Empresa');

			$return = $table->output();
			$this->data = $return->data ;
			return true;

		} else {
			$table->output();	
		}

		//echo SqlFormatter::format($table->query);		
	}

	function save( $request = NULL, $before = NULL, $after = NULL ){

		$post = json_decode($request, true);
		$validRequest = new request( $post );

		if( !$validRequest->validation( [ "cnpj" => "required|cnpj" ] )  ){
			$this->msg = $validRequest->errors;
			return false;
		}

		
		return parent::save( $request, 
		function( $post = NULL ) // BEFORE
		{	
			$table = new pessoa();
			$table->status = 'AT';
			$table->tipo = 'J';
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