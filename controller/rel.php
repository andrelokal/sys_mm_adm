<?php

class rel_sale extends dashboard{

	public $msg = "";
	public $data = [];
	public $page = 1;
	public $limit = 10;
	
	public $page_title = "Relatório de Vendas";

	function __construct(  ){


	}

	function get(){

		$table = new produto();
		$table->limit = $this->limit;

		if( $this->search ){
			$table->addFilter('id',$this->search,"=","OR");
			$table->addFilter('nome','%'.$this->search.'%',"like","OR");
			$table->addFilter('codigo','%'.$this->search.'%',"like","OR");
			$table->addFilter('codbar','%'.$this->search.'%',"like","OR");
			$table->addFilter('descricao','%'.$this->search.'%',"like","OR");
		}

		if( $table->success ){

			$table->page = $this->page;

			$table	->select( 'id', 'ID',NULL,NULL,true,5)
					->select( 'codigo', 'Código',NULL,NULL,true,5)
					->select( 'nome', 'Nome',NULL,NULL,true,40)
					->select( 'categoria.nome', 'Categoria','categoria',NULL,true,20)
					->select( 'valor', 'Valor',NULL,'money',true,10)
					->select( 'tem_estoque', 'Tem?',NULL,NULL,true,10," CASE a.tem_estoque WHEN 'Y' THEN 'Sim' WHEN 'N' THEN 'Não' END ")
					->select( 'estoque', 'Estoque',NULL,NULL,true,10)
					->join('categoria', 'categoria_id', 'join', 'id')
					->grid();
			
			$return = $table->output();
			$this->data['grid'] = array('data'=> $return->data, 'columns' => $return->columns);

			return true;


		} else {

			$this->msg = "Erro ao executar grid";
			return false;	
		}

	}

}