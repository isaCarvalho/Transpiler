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

    public function preencherLegendas()
    {
        $this->encode_results($this->legendas);
    }
}