<?php
extract($_GET);

extract($_POST);

switch ($action) 
{
	case 'enviarFonte':
		include "../model/analise.php";

		$result = analisar($cfonte, $lfonte, $ldestino);
		break;
	
	default:
		# code...
		break;
}