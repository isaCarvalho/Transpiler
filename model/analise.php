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

function transpilaFuncao($id_fonte, $id_destino, $tipo, $nome, $param)
{
	// prototipo de uma funcao na linguagem de destino
	$prototipo = Query::select("descricao", "functions", "id_linguagem = ?", [$id_destino]);

	$prototipo = buscaTipo($id_fonte, $id_destino, $tipo, $nome, $prototipo[0]['descricao']);

	$str = '';
	$parametros = explode(',', $param);

	$parLenght = sizeof($parametros);

	for ($i = 0; $i < $parLenght; $i++)
		{
			switch ($id_destino)
			{
				case 1:
					$str .= subParametro($id_destino, '<tipo> <nome>', $parametros[$i]);											
					break;

				case 2:
					$str .= subParametro($id_destino, '<tipo> <nome>', $parametros[$i]);
					break;

				case 3:
					$str .= subParametro($id_destino, '<nome> : <tipo>', $parametros[$i]);
					break;

				case 4:
					$str .= subParametro($id_destino, '<nome>', $parametros[$i]);
					break;
				
				case 5:
					$str .= subParametro($id_destino, '<nome>', $parametros[$i]);
					break;
			}

			if ($id_destino != 5 && $i != $parLenght-1)
				$str .= ', ';
			else if ($id_destino == 5 && $i != $parLenght-1)
				$str .= ' ';

		}

		$prototipo = str_replace('<param>', $str, $prototipo);

		return $prototipo;
}

function buscaTipo($id_fonte, $id_destino, $tipo, $nome, $prototipo)
{
	// retorna os tipos primitivos na linguagem de origem
	$results = Query::select("tipo, descricao, tamanho", "tipos", "id_linguagem = ?", [$id_fonte]);

	// busca o tipo de retorno na linguagem de destino
	foreach($results as $result)
	{		
		if ($tipo == $result['tipo'])
		{
			// retorna os tipos primitivos na linguagem de destino
			$destino = Query::select("tipo", "tipos", "id_linguagem = ? AND descricao = ? AND tamanho = ?", [$id_destino, $result['descricao'], $result['tamanho']]);

			$prototipo = str_replace('<tipo>', $destino[0]['tipo'], $prototipo);
			$prototipo = str_replace('<nome>', $nome, $prototipo);

			return $prototipo;
		}		
	}

	return null;
}

function subParametro($id_destino, $prototipo, $parametro)
{
	$aux = explode(' ', trim($parametro));

	$aux = buscaTipo(2, $id_destino, $aux[0], $aux[1], $prototipo);

	return $aux;
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
	else if (preg_match("/^public\s+([\w]+)\s+([\w]+)\((.*?)\)/", $codigo, $matches))
	{
		// var_dump($matches);
		return transpilaFuncao(2, $id_destino, $matches[1], $matches[2], $matches[3]);
	}
}

// TERMINAR ISSO
function analiseKotlin($codigo, $id_destino)
{
	// Transpila um if em Kotlin para qualquer outra linguagem
	if (preg_match("/^(if)/", $codigo, $matches))
	{
		return transpilaIF($codigo, $id_destino);
	}
	else if (preg_match("/^fun\s+([\w]+)\s?+\((.*?)\)\s?+\:?\s?+([\w]+)?/", $codigo, $matches))
	{
		return transpilaFuncao(3, $id_destino, $matches[3], $matches[1], $matches[2]);
	}
}

function analisePython($codigo, $id_destino)
{
	// Transpila um if em python para qualquer outra linguagem
	if (preg_match("/^(if)/", $codigo, $matches))
	{
		return transpilaIF($codigo, $id_destino);
	}
}

function analiseHaskell($codigo, $id_destino)
{

}