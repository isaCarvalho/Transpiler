<?php

class Controller
{
    public function enviarFonte()
    {
        $cfonte   = $_GET['cfonte'];
        $lfonte   = $_GET['lfonte'];
        $ldestino = $_GET['ldestino'];

        require_once "../model/Analise.php";

        $result = Analise::analisar($cfonte, $lfonte, $ldestino);
        echo json_encode(["prototipo" => $result]);
    }

    public function API()
    {
        $id = $_GET['id'];
        require_once "../api/API.php";

        echo API::apiLoad($id);
    }

    public function saveFile()
    {
        $content = $_POST['content'];

        require_once '../util/WriteFile.php';

        var_dump($_POST);
        echo WriteFile::saveFile('../files/linguagens.json', $content);
    }
}

extract($_GET);

$c = new Controller();
$c->$action();