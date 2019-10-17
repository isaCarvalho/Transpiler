<?php

extract($_GET);
extract($_POST);

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

    case 'saveFile':
        require_once '../util/WriteFile.php';
        var_dump($_POST);
        echo WriteFile::saveFile('../files/linguagens.json', $content);
        break;

	default:
        break;
}