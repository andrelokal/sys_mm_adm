<?php

class rel_sale extends dashboard{

	public $msg = "";
	public $data = [];
	public $page = 1;
	public $limit = 10;

	public $find_group = "";
	public $find_from = "";
	public $find_to = "";

	public $find_include = "internals/find-sale.html";
	
	public $page_title = "Relatório de Vendas";

	function __construct(){

	}

	function get(){

		$db = Database::getInstance();

		$nmlz = new normalize();

		$form =  $nmlz->date_mysql($this->find_from) ?  : date('Y-m-01')  ;
		$to = $nmlz->date_mysql($this->find_to) ?  : date('Y-m-t') ;
		$produto_id = NULL;

		$group =  $this->find_group ;
		$otherJoins = "";
		$otherSelects = "";
		$columns = [];
		
		if( $group == 'product' ){
			$otherJoins .= " JOIN produto C ON A.produto_id = C.id ";
			$otherSelects .= "  C.nome AS produto , ";
			$columns['produto'] = [ "title" => "Produto",
		 							"width" => "40",
		 							"show"	=> true ];
		}

		$columns['data_br'] = [ "title" => "Data",
								"width" => "20",
								"show"	=> true ]; // DATA VENDA

		$SQL = " 	SELECT 	{$otherSelects}
							DATE_FORMAT(B.data, '%d/%m/%Y') AS data_br,
							DATE_FORMAT(B.data, '%d/%m') AS data_dm,
							/*SUM( ( ( B.desconto * A.valor_unitario) /100 ) ) AS total_desconto,*/
	 						ABS( SUM( B.total - ((B.desconto * B.total) / 100 )) ) AS total_liquido,
	 						DATE_FORMAT( B.data, '%m/%Y') AS mes	 						

	 				FROM 	itens_venda A JOIN
	 						venda B ON A.venda_id = B.id ". $otherJoins ."
	 				WHERE 	B.status != 'C'	 ";
		
		// Período - Obrigatório
		$SQL .= "  AND B.data BETWEEN '".$form." 00:00:00' AND '".$to." 23:59:59' ";

		// Filtro por produto
		if( $produto_id ) $SQL .= "  AND A.produto_id = '".$produto_id."' ";
		if( $group == 'product' ) {
			if( $this->search ) $SQL .= "  AND C.nome LIKE '%".$this->search."%' ";
		}
	 	
	 	// Agrupar
		switch ($group) {
	 		case 'month': // Mês
	 			$columns['data_dm'] = $columns['data_br']; 
	 			unset($columns['data_br']);
	 			$SQL .= "	GROUP BY MONTH( B.data ) ";
	 			break;

	 		case 'product': // Produto
	 			$SQL .= "	GROUP BY A.produto_id ";
	 			break;
	 		default: // Dia - padarão
	 			$SQL .= "	GROUP BY data_br ";
	 			break;
	 	}	

	 	// Ordenar por data decrescente
	 	$SQL .= " ORDER BY B.data ASC LIMIT 0,50 ";

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