<?php

class product extends dashboard{

	public $msg = "";
	public $data = [];
	public $page = 1;
	public $limit = 10;
	public $order = 'id';
	public $order_active = ['id','name'];
	public $order_dir = "asc";
	public $search = '';
	public $idValue = 'id';
	public $categoria_id = null;

	function __construct(){

		$this->table = 'produto';

	}

	function get(){

		$table = new $this->table();
		$table->limit = $this->limit;

		if( $this->search ){
			$table->addFilter('id',$this->search,"=","OR");
			$table->addFilter('nome','%'.$this->search.'%',"like","OR");
			$table->addFilter('codigo','%'.$this->search.'%',"like","OR");
			$table->addFilter('codbar','%'.$this->search.'%',"like","OR");
			//$table->addFilter('descricao','%'.$this->search.'%',"like","OR");
		}

		if( $table->success ){

			$table->page = $this->page;

			$wh = '';
			if( $this->categoria_id ){
				$wh = " AND a.categoria_id = ".$this->categoria_id;
			}

			$table	->select( 'id', 'ID',NULL,NULL,true,7)
					->select( 'codigo', 'Código',NULL,NULL,true,5)
					->select( 'nome', 'Nome',NULL,NULL,true,40)
					->select( 'categoria.nome', 'Categoria','categoria',NULL,true,20)
					->select( 'valor', 'Valor',NULL,'money',true,10)
					->select( 'tem_estoque', 'Tem?',NULL,NULL,true,10," CASE a.tem_estoque WHEN 'Y' THEN 'Sim' WHEN 'N' THEN 'Não' END ")
					->select( 'estoque', 'Estoque',NULL,NULL,true,10, " CASE a.tem_estoque WHEN 'Y' THEN a.estoque WHEN 'N' THEN '--' END ")
					->select( 'unidade_id', '',NULL,NULL,false,0)
					->join('categoria', 'categoria_id', 'join', 'id')
					->where( " active = 'y' ".$wh )
					->order( " id " )
					->grid();
			
			$return = $table->output();
			$this->data['grid'] = array('data'=> $return->data, 
										'columns' => $return->columns,
										'reg_total'=> $return->reg_total,
										'buttons'	=> [ [  "name" => "copy",
															"icon" => "fa-copy",
															"title" => "Duplicar" ] ]);

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
		
		$table = new produto( $id );

		$categoria = new categoria();
		$categoria	->select( 'id', '','value')
					->select( 'nome', '','text');
		$retCateg = $categoria->grid()->result;

		$unidade = new unidade();
		$unidade	->select( 'id', '','value')
					->select( 'descricao', '','text');
		$retUnid = $unidade->grid()->result;

		if( $table->success ){

			$db = Database::getInstance();


			/*
			$produto_tamanho = new produto_tamanho();
			$tamnhos = [];
			if( $id ){
				$response = $db->query( "SELECT * FROM produto_tamanho WHERE produto_id = '". $id ."' " );
				
				while( $row_c = $response->fetch_assoc() ){
					$tamnhos[] = $row_c;
				}
			}
			*/

			$table->input('id')->hide()
				  ->input('nome_vinculo')->hide()
				  ->input('nome',"Nome")
				  ->input('categoria_id','Categoria')->inputSelect( $retCateg )->setType('select-range')
				  ->input('prod_vinculado','Vincluar Produto')->setType('hidden2')
				  ->input('valor',"Valor")->setType('money')
				  ->input('codigo',"Código")
				  ->input('codbar',"Código de Barra")				  
				  ->input('descricao',"Descrição")				  
				  ->input('unidade_id',"Unidade Medida")->inputSelect( $retUnid )//->setType('select-range')
				  ->input('tem_estoque',"Tem estoque?")->inputSelect( [ ["text"=>"Sim","value"=>"y"], ["text"=>"Não","value"=>"n"] ] )
				  ->input('estoque_min',"Estoque Mínimo")
				  ->input('status_inicial',"Status Inicial")->inputSelect( [ ["text"=>"Enviado","value"=>"E"], 
				  															 ["text"=>"Em Andamento","value"=>"A"],
				  															 ["text"=>"Concluído","value"=>"C"]
				  															  ] )
				  ->input('active',"Ativo")->inputSelect( [ ["text"=>"Sim","value"=>"y"], ["text"=>"Não","value"=>"n"] ] )
				  /*->input('tamanhos',"Valor por tamanho")	->subForm( 0, $produto_tamanho->input('id')->hide()
															 							  ->input('produto_id')->hide()
																						  ->input('descricao',"Tamanho")
																						  ->input('valor',"Valor")->setType('money')
																						  ->form('', true) )->setValue( json_encode($tamnhos) )*/
				  ->form('Cadastro Produto');

			$return = $table->output();
			$this->data = $return->data ;
			return true;

		} else {
			$table->output();	
		}

		//echo SqlFormatter::format($table->query);		
	}

	function delete( $id ){
		return parent::delete_($id);
	}

	function save( $request = NULL, $before = NULL, $after = NULL){

		$db = Database::getInstance();
		$db->begin_transaction();

		$post = json_decode($request, true); 
		//$post['prod_vinculado'] = $post['sel_prod_vinculado'] ;
		unset( $post['sel_prod_vinculado'] );
		unset( $post['nome_vinculo_tmp'] );

		$wherePCN = "";
		if( $post['id'] ){
			$wherePCN = " AND id != '". $post['id'] ."' ";
		}

		if( $post['prod_vinculado'] ){

			if( !$post['nome_vinculo'] ){
				$db->rollback();
				$this->msg = "Nome do vinculo é obrigatório!";	
				return false;
			}

		} else {
			// Verifica se o nome do produto da mesma categoria é igual a outro
			$resPCN = $db->query( "	SELECT 	1 
									FROM 	produto 
									WHERE 	nome = '". trim( $post['nome'] ) ."' AND 
											categoria_id = '". $post['categoria_id'] ."' ".$wherePCN );
			if( $resPCN->num_rows ){
				$db->rollback();
				$this->msg = "Nome do produto já existe para essa categoria!";	
				return false;
			}	
		}
		

		// Verifica código duplicado
		$resCDG = $db->query( "	SELECT 	1 
								FROM 	produto 
								WHERE 	codigo = '". trim( $post['codigo'] ) ."' ".$wherePCN );
		if( $resCDG->num_rows ){
			$db->rollback();
			$this->msg = "Código já existe!";	
			return false;
		}


		/*$prepTam = $this->prepareMultiPost( $post, "tamanhos", "tam" );
		$post = $prepTam->new_post;
		$tamanhos = $prepTam->data;*/

		// 
		$table = new produto();
		$success = $table->save($post);

		if( $success ){

			$lastId = $table->last_insert_id;

			/*if( $tamanhos ){

				if( !$this->saveMulitPost( $tamanhos, $lastId, "produto_tamanho", "produto_id", "id" ) ){
					$db->rollback();
					return false;
				}
				
			}  */
			
			$db->commit();	
			if( $this->idValue ) $this->data = [ $this->idValue => $table->last_insert_id ];			
			$this->msg = "Sucesso ao gravar registro!";	
		} else {
			$db->rollback();
			$this->msg = $table->msg;	
		}

		return $success;

	}


	

}