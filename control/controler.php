<?php

extract($_GET);
extract($_POST);

switch ($action) 
{
	case 'enviarFonte':
		require_once "../model/analise.php";

		$result = analisar($cfonte, $lfonte, $ldestino);
		echo json_encode(["prototipo" => $result]);
		break;
	
	case 'carregarLinguagens':
		require_once "../model/linguagem.php";

		preencherLinguagens();
		break;

	case 'carregarFunctions':
		require_once "../model/linguagem.php";

		preencherFunctions($id_linguagem);
		break;

	case 'carregarTipos':
		require_once "../model/linguagem.php";

		preencherTipos($id_linguagem);
		break;

	case 'carregarIfs':
		require_once "../model/linguagem.php";

		preencherIfs($id_linguagem);
		break;

	case 'carregarLoops':
		require_once "../model/linguagem.php";

		preencherLoops($id_linguagem);
		break;

    case 'carregarDeclaracoes':
        require_once  "../model/linguagem.php";

        preencherDeclaracoes($id_linguagem);
        break;

	case 'carregarLegendas':
		require_once "../model/linguagem.php";

		preencherLegendas();
		break;

	default:
		break;
}