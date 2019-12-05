<?php

/*
 * Classe de controle
 */
class Controller
{
    /*
     * Método que faz o roteamento de paginas
     */
    public function route()
    {
        $url = $_SERVER['REQUEST_URI'];
        $array = explode("/", $url);

        switch ($array[1])
        {
            case 'enviarFonte':
                $this->enviarFonte();
                break;

            case 'API':
                $this->API($array[2]);
                break;

            case 'saveFile':
                $this->saveFile();
                break;

            default:
                $this->redirect($array[1]);
                break;
        }
    }

    /*
     * Metodo que envia o codigo fonte para traducao
     */
    private function enviarFonte()
    {
        $cfonte   = $_POST['cfonte'];
        $lfonte   = $_POST['lfonte'];
        $ldestino = $_POST['ldestino'];

        require_once "model/Analise.php";
        require_once "model/Tradutor.php";

        $analise = Analise::analiseAbstractFactory($lfonte);
        $analise->setLinguagem(new Linguagem($lfonte));

        $ling_destino = new Linguagem($ldestino);

        $tradutor = Tradutor::getInstancia($analise, $ling_destino, $cfonte);

        $result = $tradutor->traduz();
        echo json_encode(["prototipo" => $result]);
    }

    /*
     * Metodo que redireciona a API
     */
    private function API($id)
    {
        include "api/API.php";

        echo API::apiLoad($id);
    }

    /**
     * Metodo de salvamento de arquivos
     */
    private function saveFile()
    {
        $content = $_POST['content'];

        require_once 'util/WriteFile.php';

        var_dump($_POST);
        echo WriteFile::saveFile('files/linguagens.json', $content);
    }

    /*
     * Metodo que chama o metodo render da classe View e direciona o usuario para a pagina correta
     */
    private function redirect($pagina)
    {
        $v = new View();
        $paginas = ['index', '', 'tradutor', 'tutorials', 'ajuda', 'contato', 'referencias', 'API'];

        if (!in_array($pagina, $paginas))
            $v->render('404');

        else if ($pagina == 'index' || $pagina == '' || $pagina == 'tradutor')
            $pagina = 'tradutor';

        $v->render($pagina);
    }
}