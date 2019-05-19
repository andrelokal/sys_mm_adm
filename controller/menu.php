<?php

class menu{

	public $msg = "";
	public $data = [];
	public $page = 1;
	
	function __construct(){

	}

	function get(){

		$table = new module();
		//$table->addFilter( 'CategoriaID', $this->category);

		if( $table->success ){

			$table->page = $this->page;

			$table	->select( '*')
					->order('order')
					->grid();
			
			$return = $table->output();
			$this->data = $return->data;
			return true;


		} else {

			$table->output();	
		}

	}

}