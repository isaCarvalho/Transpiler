<?php

class AnaliseKotlin extends Analise
{
    public function getRegexFor()
    {
        return "/for\s?+\(([\w]+)\s?+\:\s?+([\w]+)\s?+in\s?+([\d]+)..([\d]+)\s?+\)\s?+\{/";
    }

    public function getValuesFor($matches, $pos)
    {
        return [
            'tipo' => $matches[2][$pos],
            'var' => $matches[1][$pos],
            'inicio' => $matches[3][$pos],
            'cond' => '<',
            'fim' => $matches[4][$pos],
            'incr' => '++'
        ];
    }

    public function getRegexIf()
    {
        return "/if\s?+\((.*?)\)\s?+\n?+\s?+\{/";
    }

    public function getRegexReturn()
    {
        return "/return\s+?(.*?)\;?\s+?\}/";
    }

    public function getRegexElse()
    {
        return "/(else)\s?+\n?+\s?+\{/";
    }

    public function getRegexElseIf()
    {
        return "/else\s?+if\s?\((.*)\)\s?+\{/";
    }

    public function getRegexDeclaration()
    {
        return "/([\w]+)\s?+\:\s?+([\w]+)\s?+\=\s?+(.*)\;?/";
    }

    public function getValuesDeclaration($matches, $pos)
    {
        return [
            'tipo' => trim($matches[2][$pos]),
            'nome' => trim($matches[1][$pos]),
            'valor' => trim($matches[3][$pos])
        ];
    }

    public function getRegexAtribuition()
    {
        return "/(\w+)\s+([=*+-\/]+)(.*)/";
    }

    public function getRegexClass()
    {
        return "/class\s+([\w]+)\s?\{/";
    }

    public function getRegexFunction()
    {
        return "/fun\s+([\w]+)\s?+\((.*?)\)\s?+\:?\s?+([\w]+)?\s?+\{/";
    }

    public function getValuesFunction($matches, $pos)
    {
        return [
            'tipo' => $matches[3][$pos],
            'nome' => $matches[1][$pos],
            'param' => $matches[2][$pos]
        ];
    }

    public function getDelimitador()
    {
        return ':';
    }

    public function getRegexPrint()
    {
        return "/print\((.*?)\)\;/";
    }

    public static function formatar($codigo)
    {
        return $codigo;
    }
}