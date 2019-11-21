<?php

require_once "model/Linguagem.php";

// API que permite carregar todos os dados de linguagens ou os dados de uma linguagem por id
class API
{
    public static function apiLoad($id_linguagem)
    {
        if ($id_linguagem == '*')
            return self::loadAll();
        else
            return self::loadById($id_linguagem);
    }

    public static function loadAll()
    {
        $array = [];
        for ($i = 0; $i < 5; $i++)
            $array[$i] = self::loadById($i + 1);

        return json_encode($array);
    }

    public static function loadById($id_linguagem)
    {
        $lang = new Linguagem($id_linguagem);

        $array = [
            "id" => $lang->getId(),
            "nome" => $lang->getNome(),
            "paradigma" => $lang->getParadigma(),
            "descricao" => $lang->getDescricao(),
            "documentacao" => $lang->getDocumentacao(),
            "tipos" => $lang->getTipos(),
            "funcao" => str_replace(['<', '>'], ['&lt', '&gt'], $lang->getFuncoes()),
            "loops" => str_replace(['<', '>'], ['&lt', '&gt'], $lang->getFors()),
            "if" => str_replace(['<', '>'], ['&lt', '&gt'], $lang->getIf()),
            "else" => str_replace(['<', '>'], ['&lt', '&gt'], $lang->getElses()),
            "elseif" => str_replace(['<', '>'], ['&lt', '&gt'], $lang->getElseIfs()),
            "declaracao" => str_replace(['<', '>'], ['&lt', '&gt'], $lang->getDeclaracao()),
            "impressao" => str_replace(['<', '>'], ['&lt', '&gt'], $lang->getPrints()),
            "retorno" => str_replace(['<', '>'], ['&lt', '&gt'], $lang->getRetornos()),
            "declaracao de classe" => str_replace(['<', '>'], ['&lt', '&gt'], $lang->getClassDeclaration())
        ];

        return json_encode($array);
    }
}