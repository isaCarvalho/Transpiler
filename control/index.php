<?php
require_once "../model/Preencher.php";

extract($_GET);
extract($_POST);

$p = new Preencher($id_linguagem);

switch ($action) 
{
	case 'enviarFonte':
        require_once "../model/Analise.php";

        $result = Analise::analisar($cfonte, $lfonte, $ldestino);
        echo json_encode(["prototipo" => $result]);
        break;

    case 'API':
        require_once "../api/API.php";

        echo API::apiLoad($id);
        break;

	case 'carregarLinguagens':
        $p->preencherLinguagens();
        break;

	default:
        break;
}