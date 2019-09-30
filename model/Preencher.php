<?php 
require_once "Query.php";
require_once "Linguagem.php";

class Preencher
{
    private $linguagens;
    private $linguagem;
    private $legendas;

    public function __construct($id_linguagem)
    {
        $this->linguagens = Query::select("id, nome", "linguagens", "true", []);
        $this->linguagem = new Linguagem($id_linguagem);
        $this->legendas = Query::select("nome, descricao", "legendas", "true", []);
    }

    public function encode_results($results)
    {
        echo str_replace(['<', '>'], ['&lt', '&gt'], json_encode($results));
    }

    public function preencherLinguagens()
    {
        $this->encode_results($this->linguagens);
    }

    public function preencherPrints()
    {
        $results = ["descricao" => $this->linguagem->getPrints()];
        $this->encode_results($results);
    }

    public function preencherReturns()
    {
        $results = ["descricao" => $this->linguagem->getRetornos()];
        $this->encode_results($results);
    }

    public function preencherFunctions()
    {
        $results = ["descricao" => $this->linguagem->getFuncoes()];
        $this->encode_results($results);
    }

    public function preencherTipos()
    {
        $this->encode_results($this->linguagem->getTipos());
    }

    public function preencherIfs()
    {
        $results = ["descricao" => $this->linguagem->getIf()];
        $this->encode_results($results);
    }

    public function preencherLoops()
    {
        $this->encode_results($this->linguagem->getFors());
    }

    public function preencherDeclaracoes()
    {
        $results = ["descricao" => $this->linguagem->getDeclaracao()];
        $this->encode_results($results);
    }

    public function preencherInformacoes()
    {
        $results = [
            "nome" => $this->linguagem->getNome(),
            "descricao" => $this->linguagem->getDescricao(),
            "documentacao" => $this->linguagem->getDocumentacao()
        ];

        $this->encode_results($results);
    }

    public function preencherLegendas()
    {
        $this->encode_results($this->legendas);
    }
}