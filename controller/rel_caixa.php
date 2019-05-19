<?php

class rel_caixa extends dashboard{

	public $msg = "";
	public $data = [];
	public $page = 1;
	public $limit = 10;
	
	public $find_group = "";
	public $find_from = "";
	public $find_to = "";

	public $page_title = "Relatório de Caixa(Fechado)";

	public $find_include = "internals/find-caixa.html";

	function __construct(){

	}

	function get(){

		$db = Database::getInstance();
		$nmlz = new normalize();

		$form =  $nmlz->date_mysql($this->find_from) ?  : date('Y-m-01');
		$to = $nmlz->date_mysql($this->find_to) ?  : date('Y-m-t') ;
		$produto_id = NULL;

		$group =  $this->find_group ;
		$otherJoins = "";
		$otherSelects = "";
		$columns = [];
		
		
		$SQL = " 	SELECT 	DATE_FORMAT(A.dt_abertura, '%d/%m/%Y') AS dt_abertura,
							DATE_FORMAT(A.dt_fechamento, '%d/%m/%Y') AS dt_fechamento,
							A.valor_inicial,
							A.valor_fechamento
	 				FROM 	caixa A JOIN
	 						funcionario B ON A.funcionario_id = B.id 
	 				WHERE 	1=1	 ";
		
		$SQL .= "  AND A.dt_fechamento BETWEEN '".$form." 00:00:00' AND '".$to." 23:59:59' ";
		if( $this->search ) $SQL .= "  AND B.nome LIKE '%".$this->search."%' ";


	 	// Ordenar por data decrescente
	 	$SQL .= " ORDER BY A.dt_fechamento ASC LIMIT 0,50 ";
	 	//echo $SQL;
	 	$columns['dt_abertura'] = [ "title" => "Data",
									"width" => "20",
									"show"	=> true ];

		$columns['dt_fechamento'] = [ "title" => "Data",
									"width" => "20",
									"show"	=> true ];

	 	$columns['valor_inicial'] = [ 	"title" => "Valor Inicial",
										"width" => "30",
										"show"	=> true ]; // Total Líquido

	 	$columns['valor_fechamento'] = [ "title" => "Valor Fechamento",
										"width" => "30",
										"show"	=> true ]; // Total Líquido
	 	$result = $db->query( $SQL );
	 	$data = [];
	 	if( $result->num_rows ){

	 		while( $row = $result->fetch_assoc() ){

	 			foreach( $row as $index => $value ){
	 				if( !array_key_exists( $index , $columns) ) unset($row[$index]);
	 			}

	 			$row['valor_inicial'] = "R$ ". number_format( $row['valor_inicial'], 2, ",", "." );
	 			$row['valor_fechamento'] = "R$ ". number_format( $row['valor_fechamento'], 2, ",", "." );
	 			$data[] = $row;
	 		}	 		
	 	} 

	 	$this->data['grid'] = array('data'=> $data, 'columns' => $columns);

		return true;

		
	}

}