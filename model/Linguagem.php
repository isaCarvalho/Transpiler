<?php

require_once "Query.php";

class Linguagem
{
    private $id, $nome, $paradigma, $descricao, $documentacao;
    private $tipos, $funcoes, $fors, $ifs, $elses, $else_if;
    private $declaracao, $retornos, $prints, $class_dec;

    public function __construct($id)
    {
        $results = Query::select("*", "vw_linguagens", "id = ?", [$id]);

        $this->id = $results[0]['id'];
        $this->nome = $results[0]['nome'];
        $this->paradigma = $results[0]['paradigma'];
        $this->descricao = $results[0]['descricao'];
        $this->documentacao = $results[0]['documentacao'];
        $this->ifs = $results[0]['if_bnf'];
        $this->elses = $results[0]['else_bnf'];
        $this->funcoes = $results[0]['function_bnf'];
        $this->declaracao = $results[0]['declaration_bnf'];
        $this->retornos = $results[0]['return_bnf'];
        $this->else_if = $results[0]['else_if_bnf'];
        $this->prints = $results[0]['print_bnf'];
        $this->class_dec = $results[0]["class_declaration_bnf"];

        $this->tipos = Query::select("tipo, descricao, tamanho",
            "tipos", "id_linguagem = ?", [$this->id]);

        $this->fors = Query::select("descricao", "loops", "id_linguagem = ?", [$this->id]);
    }

    public function getId() { return $this->id; }

    public function getNome() { return $this->nome; }

    public function getParadigma() { return $this->paradigma; }

    public function getDescricao() { return $this->descricao; }

    public function getDocumentacao() { return $this->documentacao; }

    public function getTipos() { return $this->tipos; }

    public function getIf() { return $this->ifs; }

    public function getElses() { return $this->elses; }

    public function getFors() { return $this->fors; }

    public function getFuncoes() { return $this->funcoes; }

    public function getDeclaracao() { return $this->declaracao; }

    public function getRetornos() { return $this->retornos; }

    public function getElseIfs() { return $this->else_if; }

    public function getPrints() { return $this->prints; }

    public function getClassDeclaration() { return $this->class_dec; }
}