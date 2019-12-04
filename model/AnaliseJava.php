<?php

class AnaliseJava extends Analise
{
    public static function analiseJavaFactory()
    {
        return new AnaliseJava();
    }

    public function getRegexFor()
    {
        return "/for\s?+\(\s?+([\w]+)\s\s?+([\w+])\s?+\=\s?+(.*)\s?+\;\s?+[\w]+\s?+([<>!=]+)\s?+(.*)\s?+\;\s?+[\w]+(.*)\)\s?+\{/";
    }

    public function getValuesFor($matches, $pos)
    {
        return [
            'tipo' => $matches[1][$pos],
            'var' => $matches[2][$pos],
            'inicio' => $matches[3][$pos],
            'cond' => $matches[4][$pos],
            'fim' => $matches[5][$pos],
            'incr' => $matches[6][$pos]
        ];
    }

    public function getRegexIf()
    {
        return "/if\s?+\((.*?)\)\s?+\n?+\s?+\{/";
    }

    public function getRegexReturn()
    {
        return "/return\s+?(.*?)\;\s+?\}/";
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
        return "/(\w+)\s\s*(\w+)\s\s*\=\s\s*(.*)\;/";
    }

    public function getValuesDeclaration($matches, $pos)
    {
        return [
            'tipo' => $matches[1][$pos],
            'nome' => $matches[2][$pos],
            'valor' => $matches[3][$pos]
        ];
    }

    public function getRegexAtribuition()
    {
        return "/([\w]+)\s?+([=\-+*\/]+)\s?+(.*)\;/";
    }

    public function getRegexClass()
    {
        return "/public\s+class\s+([\w]+)\s?\{/";
    }

    public function getRegexFunction()
    {
        return "/public\s+([\w]+)\s+([\w]+)\s?+\((.*?)\)\s?+\{/";
    }

    public function getDelimitador()
    {
        return " ";
    }

    public function getValuesFunction($matches, $pos)
    {
        return [
            'tipo' => $matches[1][$pos],
            'nome' => $matches[2][$pos],
            'param' => $matches[3][$pos]
        ];
    }

    public function getRegexPrint()
    {
        return "/System.out.println\((.*?)\)\;/";
    }

    public function formatar($codigo)
    {
        return $codigo;
    }
}