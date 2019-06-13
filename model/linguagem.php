<?php 

// LEMBRAR DE CORRIGIR OS DADINHOS QUE ESTÃO VINDO ERRADOS DO BANCO

require_once "Query.php";

function encode_results($values, $table, $conditions, $array = [])
{
	$results = Query::select($values, $table, $conditions, $array);

	echo json_encode($results);
}

function preencherLinguagens()
{
	encode_results("id, nome", "linguagens", "1", []);
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

function preencherLegendas()
{
	encode_results("nome, descricao", "legendas", "1", []);
}