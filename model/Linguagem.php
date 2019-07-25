<?php

require_once "Query.php";

class Linguagem
{
    private $id, $nome, $paradigma, $descricao, $documentacao;
    private $tipos, $funcoes, $fors, $ifs, $elses;
    private $declaracao, $retornos;

    public function __construct($id)
    {
        $results = Query::select("linguagens.id, linguagens.nome as nome, linguagens.descricao, " .
            "linguagens.documentacao, paradigmas.nome as paradigma",
            "linguagens, paradigmas",
            "linguagens.id_paradigma = paradigmas.id AND linguagens.id = ?",
            [$id]);

        $this->id = $results[0]['id'];
        $this->nome = $results[0]['nome'];
        $this->paradigma = $results[0]['paradigma'];
        $this->descricao = $results[0]['descricao'];
        $this->documentacao = $results[0]['documentacao'];

        $this->tipos = Query::select("tipo, descricao, tamanho",
            "tipos", "id_linguagem = ?", [$this->id]);

        $this->fors = Query::select("descricao", "loops", "id_linguagem = ?", [$this->id]);

        $results = Query::select("descricao", "ifs", "id_linguagem = ?", [$this->id]);
        $this->ifs = $results[0]['descricao'];

        $results = Query::select("descricao", "elses", "id_linguagem = ?", [$this->id]);
        $this->elses = $results[0]['descricao'];

        $results = Query::select("descricao", "functions", "id_linguagem = ?", [$this->id]);
        $this->funcoes = $results[0]['descricao'];

        $results = Query::select("descricao", "declaracoes","id_linguagem = ?", [$this->id]);
        $this->declaracao = $results[0]['descricao'];

        $results = Query::select("descricao", "returns", "id_linguagem = ?", [$this->id]);
        $this->retornos = $results[0]['descricao'];
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
}