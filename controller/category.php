<?php

class category{

	public $msg = "";
	public $data = [];
	public $page = 1;
	public $limit = 10;
	public $order = 'id';
	public $order_active = ['id','name'];
	public $order_dir = "asc";
	public $search = '';
	public $idValue = 'id';
	
	function __construct(){

	}

	function get(){

		$table = new categoria();
		$table->limit = $this->limit;

		if( $this->search ){
			$table->addFilter('name','%'.$this->search.'%',"like","OR");
		}

		if( $table->success ){

			$table->page = $this->page;

			$table	->select( 'id', 'ID',NULL,NULL,true,5)
					->select( 'nome', 'Nome',NULL,NULL,true,80)
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

	function delete( $id ){

		$db = Database::getInstance();
		$db->begin_transaction();

		$table = new categoria($id);

		// ver se não tem nanhum vinculo em produtos
		$produtos = new produto();
		$produtos->addFilter('categoria_id',$id);	
		$produtos->select('*')->grid();
		if( $produtos->num_rows ){
			$this->msg = "Esse registro está vinculado a um Produto";
			return false;
		}


		if( $table->delete() ){
			$db->commit();
			$this->msg = "Data apagada com sucesso!";
			return true;
		} else {
			$db->rollback();
			$this->msg = $table->msg;
			return false;
		}
	}

	function form( $id = NULL ){
		
		$table = new categoria( $id );

		if( $table->success ){
			$table->input('id')->hide()
				  ->input('nome',"Nome")
				  ->form('Cadastro Categoria');

			$return = $table->output();
			$this->data = $return->data ;
			return true;

		} else {
			$table->output();	
		}

		//echo SqlFormatter::format($table->query);		
	}

	function save( $request = NULL){

		$post = json_decode($request, true);            

		$table = new categoria();
		$success = $table->save($post);
		
		if( $success ){
			if( $this->idValue ) $this->data = [ $this->idValue => $table->last_insert_id ];	
			$this->msg = "Sucesso ao gravar registro!";	
		} else {
			$this->msg = $table->msg;	
		}		


		return $success;

	}

	

}