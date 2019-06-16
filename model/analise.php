<?php

require_once "Query.php";

function analisar($codigo, $id_fonte, $id_destino)
{
	switch($id_fonte)
	{
		case 1:
			return analiseC($codigo, $id_destino);
			break;

		case 2:
			return analiseJava($codigo, $id_destino);
			break;

		case 3:
			return analiseKotlin($codigo, $id_destino);
			break;

		case 4:
			return analisePython($codigo, $id_destino);
			break;

		case 5:
			analiseHaskell($codigo, $id_destino);
			break;
	}
}

function transpilaIF($codigo, $id_destino)
{
	// formato geral de uma expressão condicional na linguagem de destino
	$result = Query::select("descricao", "ifs", "id_linguagem = ?", [$id_destino]);

	// forma da expressão condicional
	$regex = "/['('](.*?)[')']/";

	// captura a expressão condicional contida no codigo enviado
	preg_match_all($regex, $codigo, $if);

	// substitui a ocorrencia do if na linguagem de fonte para lingugagem de destino;
	$sub = str_replace('<exp>', $if[1][0], $result[0]['descricao']);

	// retorna o if na linguagem de destino
	return $sub;
}

function analiseC($codigo, $id_destino)
{
	// Transpila um if em C para qualquer outra linguagem
	if (preg_match("/^(if)/", $codigo, $matches))
	{
		return transpilaIF($codigo, $id_destino);
	}
}

function analiseJava($codigo, $id_destino)
{
	// Transpila um if em Java para qualquer outra linguagem
	if (preg_match("/^(if)/", $codigo, $matches))
	{
		return transpilaIF($codigo, $id_destino);
	}
	else if (preg_match("/^(public)/", $codigo, $matches))
	{
		// retorna os tipos primitivos na linguagem de origem
		$results = Query::select("tipo", "tipos", "id_linguagem = ?", [$id_fonte]);

		// retorna os tipos primitivos na linguagem de destino
		$results = Query::select("tipo", "tipos", "id_linguagem = ?", [$id_destino]);

		// var_dump($results);
	}
}

function analiseKotlin($codigo, $id_destino)
{
	// Transpila um if em Kotlin para qualquer outra linguagem
	if (preg_match("/^(if)/", $codigo, $matches))
	{
		return transpilaIF($codigo, $id_destino);
	}
}

function analisePython($codigo, $id_destino)
{
	// Transpila um if em python para qualquer outra linguagem
	if (preg_match("/^(if)/", $codigo, $matches))
	{
		return transpilaIF($codigo, $id_destino);
	}
	else if (preg_match("/^(for)/", $codigo, $matches))
	{
		$results = Query::select("descricao", "loops", "id_linguagem = ?", [$id_destino]);

		// if (preg_math("/^(for)/", $codigo, $results[0]['descricao']))
		// {

		// }
	}
	// ver os tipos de dados em python depois
	else if (preg_match("/^(def)/", $codigo, $matches))
	{
		// forma geral de uma funcao na liguagem de destino
		$result = Query::select("descricao", "functions", "id_linguagem = ?", [$id_destino]);

		// forma do nome da funcao
		$regex = "/^(def)(.*?)['(']/";

		// captura o nome da funcao
		preg_match_all($regex, $codigo, $nome);

		// substitui o nome da funcao
		$sub = str_replace('<nome>', $nome[2][0], $result[0]['descricao']);

		// expressao dos parametros da funcao
		$regex = "/['('](.*?)[')']/";

		// captura os parametros da funcao no codigo -- ver para haskell depois
		preg_match_all($regex, $codigo, $param);

		// parametros substituidos
		$sub = str_replace('<param>, ...', $param[1][0], $sub);

		return $sub;
	}
}

function analiseHaskell($codigo, $id_destino)
{

}