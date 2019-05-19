<?php

$uri = explode( "/" , $_SERVER['REQUEST_URI']) ;
$uri = array_values( array_filter( $uri ) );

switch ( $uri[0] ) {
	case 'grid':

		$grid = $uri[1];
		include_once( "controller/".$grid.".php" );

		$ctl = new $grid;		
		$cfg = $ctl->getConfig("alias_url");
		echo $ctl->view( $cfg['internals']."/grid.html" );

		break;
	
	case 'rel':

		$grid = $uri[0]."_".$uri[1];
		include_once( "controller/".$grid.".php" );

		$ctl = new $grid();
		$ctl->page_class = $grid;
		$cfg = $ctl->getConfig("alias_url");
		echo $ctl->view( $cfg['internals']."/rel.html" );

		break;

	default:

		
		# code...
		break;
}