<?php

class AnalisePython extends Analise
{
    public function getRegexFor()
    {
        return "/for\s+(\w+)\s+in\s+range\((\d+),\s+(\d+),\s+(\d+)\)\:/";
    }

    public function getValuesFor($matches, $pos)
    {
        return [
            'tipo' => 'int',
            'var' => $matches[1][$pos],
            'inicio' => $matches[2][$pos],
            'cond' => '<',
            'fim' => $matches[3][$pos],
            'incr' => $matches[4][$pos]
        ];
    }

    public function getRegexIf()
    {
        return "/if\s?+\((.*?)\)\s?+\n?+\s?+\:/";
    }

    public function getRegexReturn()
    {
        return "/return\s+?(.*?)/";
    }

    public function getRegexElse()
    {
        return "/(else)\s?+\n?+\s?+\:/";
    }

    public function getRegexElseIf()
    {
        return "/elif\s?\((.*)\)\s?+\:/";
    }

    public function getRegexDeclaration()
    {
        return "/(\w+)\s\s*\=\s\s*(.*)/";
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
        return "/([\w]+)\s?+([=\-+*\/]+)\s?+(.*)/";
    }

    public function getRegexClass()
    {
        return "/class\s+([\w]+)\s?:/";
    }

    public function getRegexFunction()
    {
        return "/def\s+(\w+)\s?+\((.*?)\)\:/";
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
        return "/print\((.*?)\)\/";
    }

    public function formatar($codigo)
    {
        $codigo = str_replace('{', '', $codigo);
        $codigo = str_replace('}', '', $codigo);
        $codigo = preg_replace("/\}$/s", "", $codigo);

        return $codigo;
    }

}