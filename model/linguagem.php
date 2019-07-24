<?php 
require_once "Query.php";

function encode_results($values, $table, $conditions, $array = [])
{
	$results = Query::select($values, $table, $conditions, $array);

	echo str_replace(['<', '>'], ['&lt', '&gt'], json_encode($results));
}

function preencherLinguagens()
{
	encode_results("id, nome", "linguagens", "true", []);
}

function preencherFunctions($id_linguagem)
{
	encode_results("descricao", "functions", "id_linguagem = ?", [$id_linguagem]);
}

function preencherTipos($id_linguagem = 1)
{
	encode_results("id, tipo, descricao, tamanho", "tipos", "id_linguagem = ?", [$id_linguagem]);
}

function preencherIfs($id_linguagem)
{
	encode_results("descricao", "ifs", "id_linguagem = ?", [$id_linguagem]);
}

function preencherLoops($id_linguagem)
{
	encode_results("descricao", "loops", "id_linguagem = ?", [$id_linguagem]);
}

function preencherDeclaracoes($id_linguagem)
{
    encode_results("descricao", "declaracoes", "id_linguagem = ?", [$id_linguagem]);
}

function preencherInformacoes($id_linguagem)
{
    encode_results("nome, descricao, documentacao", "linguagens", "id = ?", [$id_linguagem]);
}

function preencherLegendas()
{
	encode_results("nome, descricao", "legendas", "true", []);
}