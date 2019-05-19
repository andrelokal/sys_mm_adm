<?php

class buyer extends dashboard{

	public $msg = "";
	public $data = [];
	public $page = 1;
	public $limit = 10;
	public $order = 'id';
	public $order_active = ['id','cpf','nome'];
	public $order_dir = "asc";
	public $search = '';
	public $idValue = 'id';

	function __construct(){

		$this->table = 'fisica';

	}

	function get(){

		$table = new $this->table();
		$table->limit = $this->limit;

		if( $this->search ){
			$table->addFilter('id',$this->search,"=","OR");
			$table->addFilter('nome','%'.$this->search.'%',"like","OR");
			$table->addFilter('cpf','%'.$this->search.'%',"like","OR");
		}

		if( $table->success ){

			$table->page = $this->page;

			$table	->select( 'id', 'ID',NULL,NULL,true,5)
					->select( 'nome', 'Nome',NULL,NULL,true,30)
					->select( 'cpf', 'CPF',NULL,NULL,true,15)
					->select( 'dt_nascimento', 'Data Nasc.',NULL,'date_format',true,20)
					->select( 'sexo', 'Sexo',NULL,NULL,true,10," CASE a.sexo WHEN 'H' THEN 'Homem' WHEN 'M' THEN 'Mulher' END ")
					->select( 'tem_conta', 'Tem Conta',NULL,NULL,true,10," CASE a.tem_conta WHEN 'y' THEN 'Sim' WHEN 'n' THEN 'Não' END ")
					->select( 'limite_conta', 'Limite Conta',NULL,'money',true,10)
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
		
		$table = new fisica( $id );

		if( $table->success ){
			$table->input('id')->hide()
				  ->input('nome',"Nome")
				  ->input('cpf',"CPF")->setType('cpf')
				  ->input('dt_nascimento',"Nascimento")
				  ->input('tem_conta',"Tem conta ?")->inputSelect( [ ["text"=>"Sim","value"=>"y"], ["text"=>"Não","value"=>"n"] ] )
				  ->input('limite_conta',"Limite Conta")->setType('money')
				  ->input('sexo',"Sexo")->inputSelect( [ ["text"=>"Homem","value"=>"H"], ["text"=>"Mulher","value"=>"M"] ] )
				  ->input('dia_mes_pagto',"Dia Pagto ?")->setType('number')
				  ->form('Cadastro Cliente');

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

		if( !$validRequest->validation( [ 	"cpf" => "required|cpf",
											"dia_mes_pagto" => "number"	 ] )  ){
			$this->msg = $validRequest->errors;
			return false;
		}

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