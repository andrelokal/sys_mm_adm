<?php

class rel_delivery extends dashboard{

	public $msg = "";
	public $data = [];
	public $page = 1;
	public $limit = 10;
	
	public $find_group = "";
	public $find_from = "";
	public $find_to = "";

	public $page_title = "Relatório de Entregas Delivery";

	public $find_include = "internals/find-delivery.html";

	function __construct(){

	}

	function get(){

		$db = Database::getInstance();
		$nmlz = new normalize();
		$resValorEnt = $db->query( "SELECT  valor_entregador_delivery FROM config WHERE id = 1 " );
		$rowValorEnt = $resValorEnt->fetch_assoc();
		$ved = $rowValorEnt['valor_entregador_delivery'];

		$form =  $nmlz->date_mysql($this->find_from) ?  : date('Y-m-01')  ;
		$to = $nmlz->date_mysql($this->find_to) ?  : date('Y-m-t') ;
		$produto_id = NULL;

		$group =  $this->find_group ;
		$otherJoins = "";
		$otherSelects = "";
		$columns = [];
		
		if( $group == 'func' ){
			$otherSelects .= "  B.nome AS func , ";
			$columns['func'] = [ "title" => "Entregador",
		 						 "width" => "40",
		 					     "show"  => true ];
		}

		$columns['data_br'] = [ "title" => "Data",
								"width" => "20",
								"show"	=> true ];

		$SQL = " 	SELECT 	{$otherSelects}
							DATE_FORMAT(A.dt_register, '%d/%m/%Y') AS data_br,
							SUM(A.id) AS total,
							SUM('".$ved."') AS total_liquido

	 				FROM 	delivery A JOIN
	 						funcionario B ON A.ent_1 = B.id JOIN 
	 						venda C ON A.venda_id = C.id ". $otherJoins ." 
	 				WHERE 	1=1	 ";
		
		$SQL .= "  AND A.dt_register BETWEEN '".$form." 00:00:00' AND '".$to." 23:59:59' ";

		if( $group == 'func' ) {
			if( $this->search ) $SQL .= "  AND B.nome LIKE '%".$this->search."%' ";
		}

	 	// Agrupar
		switch ($group) {
	 		case 'month': // Mês
	 			$columns['data_dm'] = $columns['data_br']; 
	 			unset($columns['data_br']);
	 			$SQL .= "	GROUP BY MONTH( A.dt_register ) ";
	 			break;

	 		case 'func': // Produto
	 			$SQL .= "	GROUP BY A.ent_1 ";
	 			break;
	 		default: // Dia - padarão
	 			$SQL .= "	GROUP BY data_br ";
	 			break;
	 	}	

	 	// Ordenar por data decrescente
	 	$SQL .= " ORDER BY A.dt_register ASC LIMIT 0,50 ";
	 	//echo $SQL;
	 	$columns['total'] = [ 	"title" => "Qnt.",
										"width" => "20",
										"show"	=> true ]; // Total Líquido

	 	$columns['total_liquido'] = [ 	"title" => "Total Líquido",
										"width" => "20",
										"show"	=> true ]; // Total Líquido
	 	$result = $db->query( $SQL );
	 	$data = [];
	 	if( $result->num_rows ){

	 		while( $row = $result->fetch_assoc() ){

	 			foreach( $row as $index => $value ){
	 				if( !array_key_exists( $index , $columns) ) unset($row[$index]);
	 			}

	 			$row['total_liquido'] = "R$ ". number_format( $row['total_liquido'], 2, ",", "." );
	 			$data[] = $row;
	 		}	 		
	 	} 

	 	$this->data['grid'] = array('data'=> $data, 'columns' => $columns);

		return true;

		
	}

}