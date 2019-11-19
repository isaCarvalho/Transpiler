<?php

class AnaliseHaskell extends Analise
{
    public function getRegexFor()
    {
        return "";
    }

    public function getValuesFor($matches, $pos)
    {
        return [];
    }

    public function getRegexIf()
    {
        return "/\|\s?+(.*)\s?+/";
    }

    public function getRegexReturn()
    {
        return "/\s={1}\s+(.*)/";
    }

    public function getRegexElse()
    {
        return "/\|\s?+otherwise\s?+/";
    }

    public function getRegexElseIf()
    {
        return "";
    }

    public function getRegexDeclaration()
    {
        return "/let\s+?(\w+)\s?+=\s?+(.*)/";
    }

    public function getValuesDeclaration($matches, $pos)
    {
        return [
            'tipo' => 'int',
            'nome' => $matches[1][$pos],
            'valor' => $matches[2][$pos]
        ];
    }

    public function getRegexAtribuition()
    {
        return "";
    }

    public function getRegexClass()
    {
        return "";
    }

    public function getRegexFunction()
    {
        return "/(\w+)\s+?(.*)/";
    }

    public function getDelimitador()
    {
        return " ";
    }

    public function getValuesFunction($matches, $pos)
    {
        return [
            'tipo' => 'int',
            'nome' => $matches[1][$pos],
            'param' => $matches[2][$pos]
        ];
    }

    public function getRegexPrint()
    {
        return "/putStr\s+?(.*?)/";
    }

    public static function formatar($codigo)
    {
        $codigo = str_replace('{', '', $codigo);
        $codigo = str_replace('}', '', $codigo);
        $codigo = preg_replace("/\}$/s", "", $codigo);
        $codigo = str_replace("\n", '', $codigo);
        $codigo = preg_replace('/\s+\=/sm', ' =', $codigo);
        $codigo = preg_replace('/\s+\|/', "\n  |", $codigo);

        return $codigo;
    }
}