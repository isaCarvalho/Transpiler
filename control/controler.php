<?php

extract($_GET);
extract($_POST);

switch ($action) 
{
	case 'enviarFonte':
		require_once "../model/analise.php";

		$result = analisar($cfonte, $lfonte, $ldestino);
		break;
	
	case 'carregarLinguagens':
		require_once "../model/linguagem.php";

		preencherLinguagens();
		break;

	case 'carregarTipos':
		require_once "../model/linguagem.php";

		preencherTipos(2);
		break;

	default:
		break;
}