<?php

class stock extends dashboard{

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

		$this->table = 'entrada_produto';

	}

	function get(){

		$table = new $this->table();
		$table->limit = $this->limit;

		if( $this->search ){
			$table->addFilter('id',$this->search,"=","OR");
		}

		if( $table->success ){

			$table->page = $this->page;

			$table	->select( 'id', 'ID',NULL,NULL,true,5)
					->select( 'dt_lancamento', 'Data Lanç.',NULL,'date_format',true,15)
					->select( 'produto.nome', 'Produto',NULL,NULL,true,30)
					->select( 'quantidade', 'Quantidade',NULL,NULL,true,10)
					->select( 'dt_compra', 'Data Compra',NULL,NULL,true,15)	
					->select( 'valor_unitario', 'Valor Unit.',NULL,'money',true,10)
					->select( 'valor_total', 'Valor Total',NULL,'money',true,15)					
					->join('produto', 'produto_id', 'join', 'id')
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

	/*function edit( $id ){
		
	}*/

	function form( $id = NULL ){
		
		$table = new entrada_produto( $id );

		$produto = new produto();
		$produto	->select( 'id', '','value')
					->select( 'nome', '','text');
		$retProd = $produto->grid()->result;

		$fornecedor = new juridica();
		$fornecedor	->select( 'id', '','value')
					->select( 'razao_social', '','text');
		$retFornec = $fornecedor->grid()->result;

		if( $table->success ){
			$table->input('id')->hide()
				  ->input('tipo',"Tipo")->inputSelect( [ ["text"=>"Entrada","value"=>"E"], ["text"=>"Saída","value"=>"S"] ] )
				  ->input('produto_id','Produto')->inputSelect( $retProd )->setType('select-range')
				  ->input('quantidade',"Quantidade")->setType('number')
				  ->input('fornecedor_id',"Fornecedor")->inputSelect( $retFornec )->setType('select-range')				  
				  ->input('dt_compra',"Data Compra")->setType('date')
				  ->input('dt_validade',"Data Validade")->setType('date')
				  ->input('valor_unitario',"Valor Unitário")->setType('money')
				  ->input('valor_total',"Valor Total")->setType('money')
				  ->form('Cadastro Fornecedor');

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
			return true;

		}, 
		function( $obj = NULL ) // AFTER
		{	
			
			switch ($obj->tipo) {
				case 'E':
				case 'EX':
					
					if( $obj->sqlExec(" UPDATE produto
	                                	SET estoque = (CASE WHEN estoque IS NULL THEN ".$obj->quantidade." ELSE (estoque + ".$obj->quantidade.") END)
	                                	WHERE id = '".$obj->produto_id."' ")){
	                	return true;
	                }  

					break;
				
				case 'S':
					
					$produto = new produto( $obj->produto_id );
	                if( ( $produto->estoque - $obj->quantidade ) >= 0 ){
	                    if( $obj->sqlExec(" UPDATE produto
	                                    	SET estoque = (estoque - ".$obj->quantidade.") 
	                                    	WHERE id = '".$obj->produto_id."' ") ){
	                    	return true;
	                    }  

	                } else {
	                    $this->msg = "Estoque insuficiente !";
	                    return false;
	                }

					break;
			}
	
		});


	}	

}