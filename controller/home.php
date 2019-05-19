<?php

class home extends dashboard{

	public $msg = "";
	public $data = [];
	public $page = 1;
	public $limit = 10;
	
	function __construct(){

	}
	function monitor_item(){
		$db = Database::getInstance();

		$SQL = " 	SELECT 	A.venda_id,
							B.nome,
							A.descricao,
							A.quantidade,
							B.unidade_id,
							A.status,
							A.id
					FROM 	itens_venda A JOIN 
							produto B ON A.produto_id = B.id
					WHERE 	A.status IN ('E','A') 
					ORDER BY A.id, A.venda_id, A.produto_id 
					LIMIT 0,50 ";


	 	$columns = [ 	[	"title" => "Pedido",
							"width" => "10",
							"data" => "venda_id",
							"show"	=> true ],
						[	"title" => "Item",
							"width" => "50",
							"data" => "nome",
							"show"	=> true ],
						[	"title" => "Qnt.",
							"width" => "5",
							"data" => "quantidade",
							"show"	=> true ],
						[	"title" => "Status",
							"width" => "10",
							"data" => "status",
							"show"	=> true ],
						[	"data" => "id",
							"show"	=> false ]
						
					]; // Total Líquido

	 	$result = $db->query( $SQL );
	 	$data = [];
	 	if( $result->num_rows ){

	 		while( $row = $result->fetch_assoc() ){

	 			if( $row['descricao'] ){
	 				$row['nome'] .= " ( ". utf8_decode($row['descricao'])." )";
	 			}

	 			if( $row['unidade_id'] == 1 or 
	 				$row['unidade_id'] == 4){
	 				$row['quantidade'] = (int) $row['quantidade'];
	 			}
	 			$data[] = $row;
	 		}	 		
	 	} 

	 	$this->data['grid'] = array('data'=> $data, 'columns' => $columns);	

		return true;
	}

	function monitor(){

		$db = Database::getInstance();

		$SQL = " 	SELECT 	A.id,
							DATE_FORMAT(A.data, ' %H:%i') AS data,
							@item_C := 	( 	SELECT 	COUNT(*) 
											FROM 	itens_venda X 
											WHERE   X.venda_id = A.id AND 
													X.status = 'C'  ),
							@item_T := 	( 	SELECT 	COUNT(*) 
											FROM 	itens_venda X 
											WHERE   X.venda_id = A.id AND 
													X.status != 'X'  ),
							((@item_C * 100) / @item_T) AS st,
							B.id AS delivery

					FROM 	venda A LEFT JOIN 
							delivery B ON A.id = B.venda_id 
					WHERE 	A.status IN('A','C') AND 
							( CASE WHEN B.id IS NULL THEN 0 ELSE B.status = 'E' END ) AND 
							TIME_TO_SEC(TIMEDIFF( NOW(), A.data ) / 60) < 120
					ORDER BY st DESC, A.data ASC 
					LIMIT 0,50 ";


	 	$columns = [ 	[	"title" => "ID",
							"width" => "5",
							"data" => "id",
							"show"	=> true ],
						[	"title" => "Data",
							"width" => "10",
							"data" => "data",
							"show"	=> true ],
						[	"title" => "Status",
							"width" => "70",
							"data" => "st",
							"show"	=> true ],
						[	"data" => "delivery",
							"show"	=> false ]
						
					]; // Total Líquido

	 	$result = $db->query( $SQL );
	 	$data = [];
	 	if( $result->num_rows ){

	 		while( $row = $result->fetch_assoc() ){
	 			$tmp = '';
	 			$tmp = [];
	 			foreach( $columns as $v ){
	 				//$row['total'] = "R$ ". number_format( $row['total'], 2, ",", "." );
	 				$tmp[$v['data']] = $row[$v['data']];
	 			}
	 			$data[] = $tmp;
	 			
	 		}	 		
	 	} 

	 	$this->data['grid'] = array('data'=> $data, 'columns' => $columns);

		return true;

		
	}

	function monitor_delivery(){

		$db = Database::getInstance();

		$SQL = " 	SELECT 	A.venda_id,
							DATE_FORMAT(A.dt_register, ' %H:%i') AS data,
							CASE WHEN A.dt_saida IS NULL THEN '' ELSE DATE_FORMAT(A.dt_saida, ' %H:%i') END AS saida,
							A.status,
							A.forma,
							A.troco,
							B.nome,
							A.id
					FROM 	delivery A LEFT JOIN 
							funcionario B ON A.ent_1 = B.id
					WHERE 	A.status IN('A', 'T')
					ORDER BY A.dt_register, A.dt_saida ASC 
					LIMIT 0,50 ";


	 	$columns = [ 	[	"title" => "ID",
							"width" => "5",
							"data" => "venda_id",
							"show"	=> true ],
						[	"title" => "Data",
							"width" => "10",
							"data" => "data",
							"show"	=> true ],
						[	"title" => "Saída",
							"width" => "10",
							"data" => "saida",
							"show"	=> true ],
						[	"title" => "Forma",
							"width" => "5",
							"data" => "forma",
							"show"	=> true ],
						[	"title" => "Troco",
							"width" => "15",
							"data" => "troco",
							"show"	=> true ],
						[	"title" => "Entr.",
							"width" => "45",
							"data" => "nome",
							"show"	=> true ],
						[	"title" => "Status",
							"width" => "10",
							"data" => "status",
							"show"	=> true ],
						[	"data" => "id",
							"show"	=> false ]
						
					]; // Total Líquido

	 	$result = $db->query( $SQL );
	 	$data = [];
	 	if( $result->num_rows ){

	 		while( $row = $result->fetch_assoc() ){
	 			$row['troco'] =  number_format( $row['troco'], 2, ",", "." );
	 			$data[] = $row;
	 		}	 		
	 	} 

	 	$this->data['grid'] = array('data'=> $data, 'columns' => $columns);

		return true;		
	}

}