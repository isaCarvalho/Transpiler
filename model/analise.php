<?php

require_once "Query.php";
require_once "Linguagem.php";

function analisar($codigo, $id_fonte, $id_destino)
{
	switch($id_fonte)
	{
        case Linguagem::C:
			return analiseC($codigo, $id_destino);
			break;

		case Linguagem::JAVA:
			return analiseJava($codigo, $id_destino);
			break;

		case Linguagem::KOTLIN:
			return analiseKotlin($codigo, $id_destino);
			break;

		case Linguagem::PYTHON:
			return analisePython($codigo, $id_destino);
			break;

		case Linguagem::HASKELL:
			analiseHaskell($codigo, $id_destino);
			break;
	}
}

function transpilaIF($id_destino, $matches = [])
{
	// formato geral de uma expressão condicional na linguagem de destino
	$result = Query::select("descricao", "ifs", "id_linguagem = ?", [$id_destino]);

	// substitui a ocorrencia do if na linguagem de fonte para lingugagem de destino;
	$sub = str_replace('<exp>', $matches[1], $result[0]['descricao']);

	$sub .= formatarFuncao($id_destino);

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
				case Linguagem::C:
					$str .= subParametro($id_fonte, $id_destino, '<tipo> <nome>', $parametros[$i], $delimitador);
					break;

				case Linguagem::JAVA:
					$str .= subParametro($id_fonte, $id_destino, '<tipo> <nome>', $parametros[$i], $delimitador);
					break;

				case Linguagem::KOTLIN:
					$str .= subParametro($id_fonte, $id_destino, '<nome> : <tipo>', $parametros[$i], $delimitador);
					break;

				case Linguagem::PYTHON:
					$str .= subParametro($id_fonte, $id_destino, '<nome>', $parametros[$i], $delimitador);
					break;
				
				case Linguagem::HASKELL:
					$str .= subParametro($id_fonte, $id_destino, '<nome>', $parametros[$i], $delimitador);
					break;
			}

			if ($id_destino != Linguagem::HASKELL && $i != $parLenght-1)
				$str .= ', ';
			else if ($id_destino == Linguagem::HASKELL && $i != $parLenght-1)
				$str .= ' ';

		}

	    //substitui todos os parametros no prototipo
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
	if ($id_fonte == Linguagem::KOTLIN)
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
        case Linguagem::C:
        case Linguagem::JAVA:
            $antigo = ['<tipo>', '<var>', '<inicio>', '<cond>', '<fim>', '<incr>'];
            $novo = [$tipo, $matches['var'], $matches['inicio'], $matches['cond'], $matches['fim'], $matches['incr']];
            $prototipo = $prototipo[0]['descricao'];
            break;

        case Linguagem::KOTLIN:
            $antigo = ['<tipo>', '<var>', '<inicio>', '<fim>'];
            $novo = [$tipo, $matches['var'], $matches['inicio'], $matches['fim']];
            $prototipo = $prototipo[1]['descricao'];
            break;

        case Linguagem::PYTHON:

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

//    var_dump($prototipo);

    // retorna o tipo na liguagem de destino
    $prototipo = buscaTipo($id_fonte, $id_destino, $matches['tipo'], $matches['nome'], $prototipo[0]['descricao']);

    return str_replace('<valor>', $matches['valor'], $prototipo);
}

function transpilaReturn($id_destino, $valor)
{
    // pega o prototipo do return na linguagem de destino
    $prototipo = Query::select("descricao", "returns", "id_linguagem = ?", [$id_destino]);

    $str = str_replace('<valor>', $valor, $prototipo[0]['descricao']);

    if ($id_destino != Linguagem::HASKELL && $id_destino != Linguagem::PYTHON)
        $str .= "\n}";

    return $str;
}

function transpilaAtribuicao($id_destino, $matches, $codigo)
{
    foreach ($matches as $match)
    {
        $aux = $match[0];

        if ($id_destino != Linguagem::C && $id_destino != Linguagem::JAVA)
            $aux = str_replace(';', '', $match[0]);

        $codigo = str_replace($match[0], $aux,$codigo);
    }

    return $codigo;
}

function codigo_final($codigo_final)
{
    if (strlen($codigo_final))
        return $codigo_final;

    return 'O codigo não pode ser transpilado!';
}

function formatarFuncao($id_destino)
{
    if ($id_destino != Linguagem::HASKELL && $id_destino != Linguagem::PYTHON)
        return " {";
}

function analiseC($codigo, $id_destino)
{
	// Transpila um if
	if (preg_match("/if\s?+\((.*?)\)\s?+\{/", $codigo, $matches))
	{
        $aux = transpilaIF($id_destino, $matches);

        $codigo = str_replace($matches[0], $aux, $codigo);
	}
	// Transpila uma funcao
	if (preg_match("/([\w]+)\s([\w]+)\s?\((.*?)\)\s?+\{/", $codigo, $matches))
    {
        $aux = transpilaFuncao(Linguagem::C, $id_destino, $matches[1], $matches[2], $matches[3]).formatarFuncao($id_destino);

//        var_dump($aux);
        $codigo = str_replace($matches[0], $aux, $codigo);
    }
	// Transpila um for padrao
    if (preg_match("/for\s?+\(\s?+([\w]+)\s\s?+([\w+])\s?+\=\s?+(.*)\s?+\;\s?+[\w]+\s?+([<>!=]+)\s?+(.*)\s?+\;\s?+[\w]+(.*)\)\s?+\{/", $codigo, $matches))
    {
        $match = $matches[0];

        $matches = [
            'tipo' => $matches[1],
            'var' => $matches[2],
            'inicio' => $matches[3],
            'cond' => $matches[4],
            'fim' => $matches[5],
            'incr' => $matches[6]
        ];

        $aux = transpilaFor(Linguagem::C, $id_destino, $matches).formatarFuncao($id_destino);

        $codigo = str_replace($match, $aux, $codigo);

    }
    // Transpila uma declaracao de variavel
    if (preg_match_all("/([\w]+)\s\s?+([\w]?+)\s?+\=\s?+(.*)\;/", $codigo, $matches))
    {
        for ($i = 0; $i < sizeof($matches)-1; $i++)
        {
            $values = [
                'tipo' => $matches[1][$i],
                'nome' => $matches[2][$i],
                'valor' => $matches[3][$i]
            ];

            $aux = transpilaDeclaracao(Linguagem::C, $id_destino, $values);

            $codigo = str_replace($matches[0][$i], $aux, $codigo);
        }

    }
    // Transpila return
    if (preg_match("/return\s+?(.*?)\;\s+?\}/", $codigo, $matches))
    {
        $aux = transpilaReturn($id_destino, $matches[1]);

        $codigo = str_replace($matches[0], $aux, $codigo);
    }
    // Transpila atribuicoes
    if (preg_match_all("/([\w]+)\s?+([=\-+*\/]+)\s?+(.*)\;/", $codigo, $matches))
    {

        $codigo = transpilaAtribuicao($id_destino, $matches, $codigo);
    }

    return codigo_final($codigo);
}

function analiseJava($codigo, $id_destino)
{
	// Transpila um if em Java
	if (preg_match("/if\s?+\((.*?)\)\s?+\{/", $codigo, $matches))
	{
		$aux = transpilaIF($id_destino, $matches);

		$codigo = str_replace($matches[0], $aux, $codigo);
	}
	// Transpila um metodo publico
	if (preg_match("/public\s+([\w]+)\s+([\w]+)\s?+\((.*?)\)\s?+\{/", $codigo, $matches))
    {
        $aux = transpilaFuncao(Linguagem::JAVA, $id_destino, $matches[1], $matches[2], $matches[3]).formatarFuncao($id_destino);

        $codigo = str_replace($matches[0], $aux, $codigo);
    }
	// Transpila um for padrao
	if (preg_match("/for\s?+\(\s?+([\w]+)\s\s?+([\w+])\s?+\=\s?+(.*)\s?+\;\s?+[\w]+\s?+([<>!=]+)\s?+(.*)\s?+\;\s?+[\w]+(.*)\)\s?+\{/", $codigo, $matches))
    {
        $match = $matches[0];
        $matches = [
                    'tipo' => $matches[1],
                    'var' => $matches[2],
                    'inicio' => $matches[3],
                    'cond' => $matches[4],
                    'fim' => $matches[5],
                    'incr' => $matches[6]
        ];

        $aux = transpilaFor(Linguagem::JAVA, $id_destino, $matches).formatarFuncao($id_destino);

        $codigo = str_replace($match, $aux, $codigo);
    }
    // Transpila uma declaracao de variavel
    if (preg_match_all("/([\w]+)\s\s?+([\w]?+)\s?+\=\s?+(.*)\;/", $codigo, $matches))
    {
        for ($i = 0; $i < sizeof($matches)-1; $i++)
        {
            $values = [
                'tipo' => $matches[1][$i],
                'nome' => $matches[2][$i],
                'valor' => $matches[3][$i]
            ];

            $aux = transpilaDeclaracao(Linguagem::JAVA, $id_destino, $values);

            $codigo = str_replace($matches[0][$i], $aux, $codigo);
        }

    }
    // Transpila return
    if (preg_match("/return\s+?(.*?)\;\s+?\}/", $codigo, $matches))
    {
        $aux= transpilaReturn($id_destino, $matches[1]);

        $codigo = str_replace($matches[0], $aux, $codigo);
    }
    // Transpila atribuicoes
    if (preg_match_all("/([\w]+)\s?+([=\-+*\/]+)\s?+(.*)\;/", $codigo, $matches))
    {

        $codigo = transpilaAtribuicao($id_destino, $matches, $codigo);
    }

    return codigo_final($codigo);
}

// LEMBRAR DE VERIFICAR AS CHAVES AQUI E DE FAZER ANALISE EM BLOCO
function analiseKotlin($codigo, $id_destino)
{
	// Transpila um if em Kotlin
	if (preg_match("/if\s?+\((.*?)\)\s?+\{/", $codigo, $matches))
	{
        $aux = transpilaIF($id_destino, $matches);

        $codigo = str_replace($matches[0], $aux, $codigo);
	}
	// Transpila um metodo em Kotlin
	if (preg_match("/fun\s+([\w]+)\s?+\((.*?)\)\s?+\:?\s?+([\w]+)?\s?+\{/", $codigo, $matches))
	{
	    //O array de matches contém o tipo de retorno, nome da função e parametros, respectivamente.
        $aux = transpilaFuncao(Linguagem::KOTLIN, $id_destino, $matches[3], $matches[1], $matches[2], ':').formatarFuncao($id_destino);

        $codigo = str_replace($matches[0], $aux, $codigo);
    }
	// Transpila um laço for padrão
	if (preg_match("/for\s?+\(([\w]+)\s?+\:\s?+([\w]+)\s?+in\s?+([\d]+)..([\d]+)\s?+\{/", $codigo, $matches))
    {
        $match = $matches[0];
        $matches = [
            'tipo' => $matches[2],
            'var' => $matches[1],
            'inicio' => $matches[3],
            'cond' => '<',
            'fim' => $matches[4],
            'incr' => $matches[1].'++'
        ];

        $aux = transpilaFor(Linguagem::KOTLIN, $id_destino, $matches);

        $codigo = str_replace($match, $aux, $codigo);
    }
	// Transpila a declaracao de uma variavel
    if (preg_match_all("/([\w]+)\s?+\:\s?+([\w]+)\s?+\=\s?+(.*)\;?/", $codigo, $matches))
    {

        for ($i = 0; $i < sizeof($matches)-1; $i++)
        {
            $values = [
                'tipo' => trim($matches[2][$i]),
                'nome' => trim($matches[1][$i]),
                'valor' => trim($matches[3][$i])
            ];

            $aux = transpilaDeclaracao(Linguagem::KOTLIN, $id_destino, $values);

            $codigo = str_replace($matches[0][$i], $aux, $codigo);
        }
    }
    // Transpila return
    if (preg_match("/return\s+?(.*?)\;?\s+?\}/", $codigo, $matches))
    {
        $aux = transpilaReturn($id_destino, $matches[1]);

        $codigo = str_replace($matches[0], $aux, $codigo);
    }

	return codigo_final($codigo);
}

function analisePython($codigo, $id_destino)
{
	// Transpila um if em Python 3
	if (preg_match("/(if)/", $codigo, $matches))
	{

		return transpilaIF($id_destino, $matches)."\n";
	}

    return 'O codigo não pode ser transpilado!';
}

function analiseHaskell($codigo, $id_destino)
{

}