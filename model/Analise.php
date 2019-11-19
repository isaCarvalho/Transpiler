<?php

require_once "Linguagem.php";
require_once "AnaliseC.php";
require_once "AnaliseJava.php";
require_once "AnaliseKotlin.php";
require_once "AnalisePython.php";
require_once "AnaliseHaskell.php";

/**
 * Class Analise
 * Esta classe é a que contem a linguagem de fonte e a sua respectiva regra gramatical
 */
abstract class Analise
{
    /** A análise a linguagem de origem */
    private $linguagem;

    public function getLinguagem()
    {
        return $this->linguagem;
    }

    public function setLinguagem(Linguagem $linguagem)
    {
        $this->linguagem = $linguagem;
    }

    public abstract function getRegexFor();

    public abstract function getValuesFor($matches, $pos);

    public abstract function getRegexIf();

    public abstract function getRegexReturn();

    public abstract function getRegexElse();

    public abstract function getRegexElseIf();

    public abstract function getRegexDeclaration();

    public abstract function getValuesDeclaration($matches, $pos);

    public abstract function getRegexAtribuition();

    public abstract function getRegexClass();

    public abstract function getRegexFunction();

    public abstract function getDelimitador();

    public abstract function getRegexPrint();

    public abstract function getValuesFunction($matches, $pos);

    public abstract static function formatar($codigo);
}