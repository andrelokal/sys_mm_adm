<?php

class sale extends dashboard{

	public $msg = "";
	public $data = [];
	public $page = 1;
	public $limit = 10;
	public $idValue = 'id';
	
	function __construct(){

		$this->table = 'venda';

	}
	
	function change_status( $id, $status ){
		$db = Database::getInstance();

		$SQL = "UPDATE 	itens_venda 
				SET status = '". $status ."' 
				WHERE id = '". $id ."' ";
		if( $db->query( $SQL ) ){

			$item_venda = new itens_venda( $id );

			$resDL = $db->query( "	SELECT 	id
									FROM 	delivery 
									WHERE 	venda_id = '". $item_venda->venda_id ."' " );

			if( $resDL->num_rows ){
				$rowDL = $resDL->fetch_assoc();

				$res = $db->query( "SELECT 	COUNT(*) AS total
									FROM 	itens_venda 
									WHERE 	venda_id = '". $item_venda->venda_id ."' AND 
											status IN ('E','A') " );
				$row = $res->fetch_assoc();
				if( !$row['total'] ){
					

					$SQL = "UPDATE 	delivery 
							SET 	status = 'A' 
							WHERE 	id = '". $rowDL['id'] ."' ";

					if( !$db->query( $SQL ) ){
						$this->msg = "Erro ao alterar status delivery!";
						return false;
					}
				}	
			}
			

			$this->msg = "Alterado com sucesso!";
			return true;
		} else {
			$this->msg = "Erro ao alterar status!";
			return false;
		}
	}

	function change_status_venda( $id, $status ){
		$db = Database::getInstance();

		$SQL = "UPDATE 	venda 
				SET 	status = '". $status ."' 
				WHERE 	id = '". $id ."' ";
		if( $db->query( $SQL ) ){
			$this->msg = "Alterado com sucesso!";
			return true;
		} else {
			$this->msg = "Erro ao alterar status!";
			return false;
		}
	}

	function change_status_entrega( $id, $status ){
		$db = Database::getInstance();

		$SQL = "UPDATE 	delivery 
				SET 	status = '". $status ."' 
				WHERE 	id = '". $id ."' ";
		if( $db->query( $SQL ) ){
			$this->msg = "Alterado com sucesso!";
			return true;
		} else {
			$this->msg = "Erro ao alterar status!";
			return false;
		}
	}

	

}