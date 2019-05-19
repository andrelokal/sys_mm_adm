<?php

class persons{

	public $msg = "";
	public $data = [];
	public $page = 1;
	public $limit = 10;
	public $order = 'id';
	public $order_active = ['person_id','name'];
	public $order_dir = "asc";
	public $search = '';
	public $idValue = 'person_id';
	
	function __construct(){

	}

	function get(){

		$table = new person();
		//$table->addFilter( 'CategoriaID', $this->category);
		$table->limit = $this->limit;

		if( $this->search ){
			$table->addFilter('name','%'.$this->search.'%',"like","OR");
			$table->addFilter('phone','%'.$this->search.'%',"like","OR");
			$table->addFilter('email','%'.$this->search.'%',"like","OR");
		}

		if( $table->success ){

			$table->page = $this->page;

			$table	->select( 'person_id', 'ID',NULL,NULL,true,5)
					->select( 'name', 'Nome',NULL,NULL,true,25)
					->select( 'phone', 'Telefone',NULL,NULL,true,20)
					->select( 'email', 'E-mail',NULL,NULL,true,20)					
					->select( 'dt_register', 'Cadastro',NULL,'date_format',true,20)			
					->grid();
			
			$return = $table->output();
			$this->data['grid'] = array('data'		=> $return->data, 
										'columns' 	=> $return->columns );

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
		return parent::delete_($id);
	}

	function form( $id = NULL ){
		
		$table = new person( $id );

		if( $table->success ){
			$table->input('person_id')->hide()
				  ->input('name',"Nome")
				  ->input('nickname',"Apelido")
				  ->input('gender',"Sexo")->inputSelect( array( array('value'=>'M','text'=>'Masculino'), array('value'=>'F','text'=>'Feminino') ) )
				  ->input('birthday',"Data Nascimento","date_format")->setType('date')
				  ->input('email',"E-mail")
				  ->input('phone',"Telefone")->setType('telefone')
				  ->input('adress',"Endereço")
				  ->form('Cadastro Aluno');

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

		$table = new person();
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