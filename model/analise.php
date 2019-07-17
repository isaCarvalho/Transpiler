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

function transpilaFuncao($id_fonte, $id_destino, $tipo, $nome, $param, $delimitador = ' ')
{
	// prototipo de uma funcao na linguagem de destino
	$prototipo = Query::select("descricao", "functions", "id_linguagem = ?", [$id_destino]);

	// verifica o tipo correspondente na linguagem de destino
	$prototipo = buscaTipo($id_fonte, $id_destino, $tipo, $nome, $prototipo[0]['descricao']);

	$str = '';

	// separa os diferentes parametros
	$parametros = explode(',', $param);

	$parLenght = sizeof($parametros);

    // para cada parametro, substitui seu nome e tipo
	for ($i = 0; $i < $parLenght; $i++)
		{
			switch ($id_destino)
			{
				case 1:
					$str .= subParametro($id_fonte, $id_destino, '<tipo> <nome>', $parametros[$i], $delimitador);
					break;

				case 2:
					$str .= subParametro($id_fonte, $id_destino, '<tipo> <nome>', $parametros[$i], $delimitador);
					break;

				case 3:
					$str .= subParametro($id_fonte, $id_destino, '<nome> : <tipo>', $parametros[$i], $delimitador);
					break;

				case 4:
					$str .= subParametro($id_fonte, $id_destino, '<nome>', $parametros[$i], $delimitador);
					break;
				
				case 5:
					$str .= subParametro($id_fonte, $id_destino, '<nome>', $parametros[$i], $delimitador);
					break;
			}

			if ($id_destino != 5 && $i != $parLenght-1)
				$str .= ', ';
			else if ($id_destino == 5 && $i != $parLenght-1)
				$str .= ' ';

		}

	    //substitui todos os parametros no prototipo
		$prototipo = str_replace('<param>', $str, $prototipo);

		return $prototipo;
}

function buscaTipo($id_fonte, $id_destino, $tipo, $nome = '', $prototipo)
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

			// substitui o tipo e o nome no prototipo passado por parametro
            $prototipo = str_replace(['<tipo>', '<nome>'], [$destino[0]['tipo'], $nome], $prototipo);

			return $prototipo;
		}		
	}

	return null;
}

function subParametro($id_fonte, $id_destino, $prototipo, $parametro, $delimitador = ' ')
{
    // separa o tipo do nome do parametro
	$aux = explode($delimitador, trim($parametro));

	// retira os espaços do tipo e do nome
	$aux = array_map(function ($a) {
	    return trim($a);
    }, $aux);

	// se a linguagem for kotlin, o nome e o tipo deve ser invertido
	if ($id_fonte == 3)
        return buscaTipo($id_fonte, $id_destino, $aux[1], $aux[0], $prototipo);

	return buscaTipo($id_fonte, $id_destino, $aux[0], $aux[1], $prototipo);
}

function transpilaFor($id_fonte, $id_destino, $matches = [])
{
    // retorna o prototipo de um loop for na linguagem de destino
    $prototipo = Query::select("descricao", "loops", "id_linguagem = ?", [$id_destino]);

    // busca o tipo correspondente na linguagem de destino
    $tipo = buscaTipo($id_fonte, $id_destino, $matches['tipo'], $matches['var'], '<tipo>');

    // substitui os valores no prototipo de acordo com a linguagem de destino
    switch ($id_destino)
    {
        case 1:
        case 2:
            $antigo = ['<tipo>', '<var>', '<inicio>', '<cond>', '<fim>', '<incr>'];
            $novo = [$tipo, $matches['var'], $matches['inicio'], $matches['cond'], $matches['fim'], $matches['incr']];
            $prototipo = $prototipo[0]['descricao'];
            break;

        case 3:
            $antigo = ['<tipo>', '<var>', '<inicio>', '<fim>'];
            $novo = [$tipo, $matches['var'], $matches['inicio'], $matches['fim']];
            $prototipo = $prototipo[1]['descricao'];
            break;

        case 4:

            $antigo = ['<var>', '<inicio>', '<fim>', '<step>'];
            $novo = [$matches['var'], $matches['inicio'], $matches['fim']];
            $prototipo = $prototipo[1]['descricao'];

            switch ($matches['incr'])
            {
                case '++':
                    $novo[] = '1';
                    break;

                case '--':
                    $novo[] = '-1';
                    break;

                default:
                    if (preg_match("/\s?+\=\s?+[\w]+\s?+([+\-*\/])\s?+([\d]+)/", $matches['incr'], $match) || preg_match("/\s?+([+\-*\/])\=\s?+([\d]+)/", $matches['incr'], $match))
                        $novo[] = $match[1].$match[2];
            }
            break;
    }

    return str_replace($antigo, $novo, $prototipo);
}

function transpilaDeclaracao($id_fonte, $id_destino, $matches = [])
{
    // pega o prototipo de uma declaracao na linguagem de destino
    $prototipo = Query::select("descricao", "declaracoes", "id_linguagem = ?", [$id_destino]);

    // retorna o tipo na liguagem de destino
    $prototipo = buscaTipo($id_fonte, $id_destino, $matches['tipo'], $matches['nome'], $prototipo[0]['descricao']);

    return str_replace('<valor>', $matches['valor'], $prototipo);
}

function analiseC($codigo, $id_destino)
{
	// Transpila um if
	if (preg_match("/^(if)/", $codigo, $matches))
	{
		return transpilaIF($codigo, $id_destino);
	}
	// Transpila uma funcao
	else if (preg_match("/([\w]+)\s([\w]+)\s?\((.*?)\)/", $codigo, $matches))
    {
        return transpilaFuncao(1, $id_destino, $matches[1], $matches[2], $matches[3]);
    }
	// Transpila um for padrao
    else if (preg_match("/^for\s?+\(\s?+([\w]+)\s\s?+([\w+])\s?+\=\s?+([\d]+)\s?+\;\s?+[\w]+\s?+([<>!=]+)\s?+([\d]+)\s?+\;\s?+[\w]+(.*)\)/", $codigo, $matches))
    {
        $matches = [
            'tipo' => $matches[1],
            'var' => $matches[2],
            'inicio' => $matches[3],
            'cond' => $matches[4],
            'fim' => $matches[5],
            'incr' => $matches[6]
        ];

        return transpilaFor(2, $id_destino, $matches);
    }
    // Transpila uma declaracao de variavel
    else if (preg_match("/([\w]+)\s\s?+([\w]?+)\s?+\=\s?+([\d]+)\;/", $codigo, $matches))
    {
        $matches = [
            'tipo' => $matches[1],
            'nome' => $matches[2],
            'valor' => $matches[3]
        ];

        return transpilaDeclaracao(1, $id_destino, $matches);
    }

    return 'O codigo não pode ser transpilado!';
}

function analiseJava($codigo, $id_destino)
{
	// Transpila um if em Java
	if (preg_match("/^(if)/", $codigo, $matches))
	{
		return transpilaIF($codigo, $id_destino);
	}
	// Transpila um metodo publico
	else if (preg_match("/^public\s+([\w]+)\s+([\w]+)\((.*?)\)/", $codigo, $matches))
	{
		return transpilaFuncao(2, $id_destino, $matches[1], $matches[2], $matches[3]);
	}
	// Transpila um for padrao
	else if (preg_match("/^for\s?+\(\s?+([\w]+)\s\s?+([\w+])\s?+\=\s?+([\d]+)\s?+\;\s?+[\w]+\s?+([<>!=]+)\s?+([\d]+)\s?+\;\s?+[\w]+(.*)\)/", $codigo, $matches))
    {
        $matches = [
                    'tipo' => $matches[1],
                    'var' => $matches[2],
                    'inicio' => $matches[3],
                    'cond' => $matches[4],
                    'fim' => $matches[5],
                    'incr' => $matches[6]
        ];

        return transpilaFor(2, $id_destino, $matches);
    }
    // Transpila uma declaracao de variavel
    else if (preg_match("/([\w]+)\s\s?+([\w]?+)\s?+\=\s?+([\d]+)\;/", $codigo, $matches))
    {
        $matches = [
            'tipo' => $matches[1],
            'nome' => $matches[2],
            'valor' => $matches[3]
        ];

        return transpilaDeclaracao(2, $id_destino, $matches);
    }

    return 'O codigo não pode ser transpilado!';
}

function analiseKotlin($codigo, $id_destino)
{
	// Transpila um if em Kotlin
	if (preg_match("/^(if)/", $codigo, $matches))
	{
		return transpilaIF($codigo, $id_destino);
	}
	// Transpila um metodo em Kotlin
	else if (preg_match("/^fun\s+([\w]+)\s?+\((.*?)\)\s?+\:?\s?+([\w]+)?/", $codigo, $matches))
	{
	    //O array de matches contém o tipo de retorno, nome da função e parametros, respectivamente.
		return transpilaFuncao(3, $id_destino, $matches[3], $matches[1], $matches[2], ':');
	}
	// Transpila um laço for padrão
	else if (preg_match("/^for\s?+\(([\w]+)\s?+\:\s?+([\w]+)\s?+in\s?+([\d]+)..([\d]+)/", $codigo, $matches))
    {
        $matches = [
            'tipo' => $matches[2],
            'var' => $matches[1],
            'inicio' => $matches[3],
            'cond' => '<',
            'fim' => $matches[4],
            'incr' => $matches[1].'++'
        ];

        return transpilaFor(3, $id_destino, $matches);
    }
	// Transpila a declaracao de uma variavel
    else if (preg_match("/([\w]+)\s?+\:\s?+([\w]+)\s?+\=\s?+(.*)\;/", $codigo, $matches))
    {
        $matches = [
            'tipo' => $matches[2],
            'nome' => $matches[1],
            'valor' => $matches[3]
        ];

        return transpilaDeclaracao(3, $id_destino, $matches);
    }

	return 'O codigo não pode ser transpilado!';
}

function analisePython($codigo, $id_destino)
{
	// Transpila um if em Python 3
	if (preg_match("/^(if)/", $codigo, $matches))
	{
		return transpilaIF($codigo, $id_destino);
	}

    return 'O codigo não pode ser transpilado!';
}

function analiseHaskell($codigo, $id_destino)
{

}