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

function transpilaIF($codigo, $id_destino, $matches = [])
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

function transpilaReturn($id_destino, $valor)
{
    // pega o prototipo do return na linguagem de destino
    $prototipo = Query::select("descricao", "returns", "id_linguagem = ?", [$id_destino]);

    $str = str_replace('<valor>', $valor, $prototipo[0]['descricao']) ;

    if ($id_destino != 5 && $id_destino != 4)
        $str .= "\n}";

    return $str;
}

function transpilaAtribuicao($id_destino, $matches, $codigo)
{
    foreach ($matches as $match)
    {
        $aux = $match[0];

        if ($id_destino != 1 && $id_destino != 2)
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
    if ($id_destino != 5 && $id_destino != 4)
        return " {";
}

function analiseC($codigo, $id_destino)
{
	// Transpila um if
	if (preg_match("/if\s?+\((.*?)\)\s?+\{/", $codigo, $matches))
	{
        $aux = transpilaIF($codigo, $id_destino, $matches);

        $codigo = str_replace($matches[0], $aux, $codigo);
	}
	// Transpila uma funcao
	if (preg_match("/([\w]+)\s([\w]+)\s?\((.*?)\)\s?+\{/", $codigo, $matches))
    {
        $aux = transpilaFuncao(1, $id_destino, $matches[1], $matches[2], $matches[3]).formatarFuncao($id_destino);

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

        $aux = transpilaFor(2, $id_destino, $matches).formatarFuncao($id_destino);

        $codigo = str_replace($match, $aux, $codigo);

    }
    // Transpila uma declaracao de variavel
    if (preg_match_all("/([\w]+)\s\s?+([\w]?+)\s?+\=\s?+([\d]+)\;/", $codigo, $matches))
    {
        for ($i = 0; $i < sizeof($matches)-1; $i++)
        {
            $values = [
                'tipo' => $matches[1][$i],
                'nome' => $matches[2][$i],
                'valor' => $matches[3][$i]
            ];

            $aux = transpilaDeclaracao(2, $id_destino, $values);

            $codigo = str_replace($matches[0][$i], $aux, $codigo);
        }

    }
    // Transpila return
    if (preg_match("/return\s+?(.*?)\;\s+?\}/", $codigo, $matches))
    {
        $aux = transpilaReturn($id_destino, $matches[1]);

        $codigo = str_replace($matches[0], $aux, $codigo);
    }
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
		$aux = transpilaIF($codigo, $id_destino, $matches);

		$codigo = str_replace($matches[0], $aux, $codigo);
	}
	// Transpila um metodo publico
	if (preg_match("/public\s+([\w]+)\s+([\w]+)\s?+\((.*?)\)\s?+\{/", $codigo, $matches))
    {
        $aux = transpilaFuncao(2, $id_destino, $matches[1], $matches[2], $matches[3]).formatarFuncao($id_destino);

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

        $aux = transpilaFor(2, $id_destino, $matches).formatarFuncao($id_destino);

        $codigo = str_replace($match, $aux, $codigo);
    }
    // Transpila uma declaracao de variavel
    if (preg_match_all("/([\w]+)\s\s?+([\w]?+)\s?+\=\s?+([\d]+)\;/", $codigo, $matches))
    {
        for ($i = 0; $i < sizeof($matches)-1; $i++)
        {
            $values = [
                'tipo' => $matches[1][$i],
                'nome' => $matches[2][$i],
                'valor' => $matches[3][$i]
            ];

            $aux = transpilaDeclaracao(2, $id_destino, $values);

            $codigo = str_replace($matches[0][$i], $aux, $codigo);
        }

    }
    // Transpila return
    if (preg_match("/return\s+?(.*?)\;\s+?\}/", $codigo, $matches))
    {
        $aux= transpilaReturn($id_destino, $matches[1]);

        $codigo = str_replace($matches[0], $aux, $codigo);
    }
    if (preg_match_all("/([\w]+)\s?+([=\-+*\/]+)\s?+(.*)\;/", $codigo, $matches))
    {

        $codigo = transpilaAtribuicao($id_destino, $matches, $codigo);
    }

    return codigo_final($codigo);
}

// LEMBRAR DE VERIFICAR AS CHAVES AQUI E DE FAZER ANALISE EM BLOCO
function analiseKotlin($codigo, $id_destino)
{
    $codigo_final = '';

	// Transpila um if em Kotlin
	if (preg_match("/(if)/", $codigo, $matches))
	{
        $codigo_final .= transpilaIF($codigo, $id_destino)."\n";
	}
	// Transpila um metodo em Kotlin
	if (preg_match("/fun\s+([\w]+)\s?+\((.*?)\)\s?+\:?\s?+([\w]+)?", $codigo, $matches))
	{
	    //O array de matches contém o tipo de retorno, nome da função e parametros, respectivamente.
        $codigo_final .= transpilaFuncao(3, $id_destino, $matches[3], $matches[1], $matches[2], ':');

        $codigo_final .= formatarFuncao($id_destino);
    }
	// Transpila um laço for padrão
	if (preg_match("/for\s?+\(([\w]+)\s?+\:\s?+([\w]+)\s?+in\s?+([\d]+)..([\d]+)\s?+\{/", $codigo, $matches))
    {
        $matches = [
            'tipo' => $matches[2],
            'var' => $matches[1],
            'inicio' => $matches[3],
            'cond' => '<',
            'fim' => $matches[4],
            'incr' => $matches[1].'++'
        ];

        $codigo_final .= transpilaFor(3, $id_destino, $matches)."\n";
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

            $codigo_final .= transpilaDeclaracao(3, $id_destino, $values);

            if ($i != sizeof($matches)-2)
                $codigo_final .= "\n";
        }
    }
    // Transpila return
    if (preg_match("/return\s+?(.*?)\;?\s+?\}/", $codigo, $matches))
    {
        $codigo_final .= transpilaReturn($id_destino, $matches[1]);
    }

	return codigo_final($codigo_final);
}

function analisePython($codigo, $id_destino)
{
	// Transpila um if em Python 3
	if (preg_match("/(if)/", $codigo, $matches))
	{
		return transpilaIF($codigo, $id_destino)."\n";
	}

    return 'O codigo não pode ser transpilado!';
}

function analiseHaskell($codigo, $id_destino)
{

}