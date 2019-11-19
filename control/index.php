<?php

class Controller
{
    private function setAnalise($fonte): Analise
    {
        switch($fonte)
        {
            case 1:
                return new AnaliseC();
                break;

            case 2:
                return new AnaliseJava();
                break;

            case 3:
                return new AnaliseKotlin();
                break;

            case 4:
                return new AnalisePython();
                break;

            case 5:
                return new AnaliseHaskell();
                break;
        }
        return null;
    }

    public function enviarFonte()
    {
        $cfonte   = $_POST['cfonte'];
        $lfonte   = $_POST['lfonte'];
        $ldestino = $_POST['ldestino'];

        require_once "../model/Analise.php";
        require_once "../model/Tradutor.php";

        $analise = $this->setAnalise($lfonte);
        $analise->setLinguagem(new Linguagem($lfonte));

        $ling_destino = new Linguagem($ldestino);

        $tradutor = new Tradutor($analise, $ling_destino, $cfonte);

        $result = $tradutor->traduz();
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

$c = new Controller();

$action = $_GET['action'];
$c->$action();