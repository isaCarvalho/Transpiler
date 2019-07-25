<?php

require_once "Linguagem.php";

function analisar($codigo, $id_fonte, $id_destino)
{
    $ling_fonte = new Linguagem($id_fonte);
    $ling_destino = new Linguagem($id_destino);

	switch($ling_fonte->getId())
	{
        case 1:
			return analiseC($codigo, $ling_fonte, $ling_destino);
			break;

		case 2:
			return analiseJava($codigo, $ling_fonte, $ling_destino);
			break;

		case 3:
			return analiseKotlin($codigo, $ling_fonte, $ling_destino);
			break;

		case 4:
			return analisePython($codigo, $ling_fonte, $ling_destino);
			break;

		case 5:
			analiseHaskell($codigo, $ling_fonte, $ling_destino);
			break;
	}
}

function transpilaIF($ling_destino, $matches = [])
{
    // substitui a ocorrencia do if na linguagem de fonte para lingugagem de destino;
    return str_replace('<exp>', $matches[1], $ling_destino->getIf());
}

function transpilaFuncao($ling_fonte, $ling_destino, $tipo, $nome, $param, $delimitador = ' ')
{
	// verifica o tipo correspondente na linguagem de destino
	$prototipo = buscaTipo($ling_fonte->getTipos(), $ling_destino->getTipos(), $tipo, $nome, $ling_destino->getFuncoes());

	$str = '';

	// separa os diferentes parametros
	$parametros = explode(',', $param);

	$parLenght = sizeof($parametros);
    // para cada parametro, substitui seu nome e tipo
	for ($i = 0; $i < $parLenght; $i++)
		{
			switch ($ling_destino->getId())
			{
				case 1:
					$str .= subParametro($ling_fonte, $ling_destino, '<tipo> <nome>', $parametros[$i], $delimitador);
					break;

				case 2:
					$str .= subParametro($ling_fonte, $ling_destino, '<tipo> <nome>', $parametros[$i], $delimitador);
					break;

				case 3:
					$str .= subParametro($ling_fonte, $ling_destino, '<nome> : <tipo>', $parametros[$i], $delimitador);
					break;

				case 4:
					$str .= subParametro($ling_fonte, $ling_destino, '<nome>', $parametros[$i], $delimitador);
					break;
				
				case 5:
					$str .= subParametro($ling_fonte, $ling_destino, '<nome>', $parametros[$i], $delimitador);
					break;
			}

			if ($ling_destino->getId() != 5 && $i != $parLenght-1)
				$str .= ', ';
			else if ($ling_destino->getId() == 5 && $i != $parLenght-1)
				$str .= ' ';
		}

	    //substitui todos os parametros no prototipo
		return str_replace('<param>', $str, $prototipo);
}

function buscaTipo($tipos_fonte, $tipos_destino, $tipo, $nome, $prototipo)
{
	// busca o tipo de retorno na linguagem de destino
	for($i = 0; $i < sizeof($tipos_fonte); $i++)
		if ($tipo == $tipos_fonte[$i]['tipo'])
			// retorna os tipos primitivos na linguagem de destino
			for ($j = 0; $j < sizeof($tipos_destino); $j++)
            {
                if ($tipos_fonte[$i]['tamanho'] == $tipos_destino[$j]['tamanho'] &&
                    $tipos_fonte[$i]['descricao'] == $tipos_destino[$j]['descricao'])
                   // substitui o tipo e o nome no prototipo passado por parametro
                    return str_replace(['<tipo>', '<nome>'], [$tipos_destino[$j]['tipo'], $nome], $prototipo);
            }

	return null;
}

function subParametro($ling_fonte, $ling_destino, $prototipo, $parametro, $delimitador = ' ')
{
    // separa o tipo do nome do parametro
	$aux = explode($delimitador, trim($parametro));

	// retira os espaços do tipo e do nome
	$aux = array_map(function ($a) {
	    return trim($a);
    }, $aux);

	// se a linguagem for kotlin, o nome e o tipo deve ser invertido
	if ($ling_fonte->getId() == 3)
        return buscaTipo($ling_fonte->getTipos(), $ling_destino->getTipos(), $aux[1], $aux[0], $prototipo);

	return buscaTipo($ling_fonte->getTipos(), $ling_destino->getTipos(), $aux[0], $aux[1], $prototipo);
}

function transpilaFor($ling_fonte, $ling_destino, $matches = [])
{
    $for = $ling_destino->getFors();

    // busca o tipo correspondente na linguagem de destino
    $tipo = buscaTipo($ling_fonte->getTipos(), $ling_destino->getTipos(), $matches['tipo'], $matches['var'], '<tipo>');

    // substitui os valores no prototipo de acordo com a linguagem de destino
    switch ($ling_destino->getId())
    {
        case 1:
        case 2:
            $antigo = ['<tipo>', '<var>', '<inicio>', '<cond>', '<fim>', '<incr>'];
            $novo = [$tipo, $matches['var'], $matches['inicio'], $matches['cond'], $matches['fim'], $matches['incr']];
            $prototipo = $for[0]['descricao'];
            break;

        case 3:
            $antigo = ['<tipo>', '<var>', '<inicio>', '<fim>'];
            $novo = [$tipo, $matches['var'], $matches['inicio'], $matches['fim']];
            $prototipo = $for[1]['descricao'];
            break;

        case 4:
            $antigo = ['<var>', '<inicio>', '<fim>', '<step>'];
            $novo = [$matches['var'], $matches['inicio'], $matches['fim']];
            $prototipo = $for[1]['descricao'];

            switch ($matches['incr'])
            {
                case '++':
                    $novo[] = '1';
                    break;

                case '--':
                    $novo[] = '-1';
                    break;

                default:
                    if (preg_match("/\s?+\=\s?+[\w]+\s?+([+\-*\/])\s?+([\d]+)/", $matches['incr'], $match)
                        || preg_match("/\s?+([+\-*\/])\=\s?+([\d]+)/", $matches['incr'], $match))
                        $novo[] = $match[1].$match[2];
            }
            break;
    }

    return str_replace($antigo, $novo, $prototipo);
}

function transpilaDeclaracao($ling_fonte, $ling_destino, $matches = [])
{
    // retorna o tipo na liguagem de destino
    $prototipo = buscaTipo($ling_fonte->getTipos(), $ling_destino->getTipos(), $matches['tipo'],
        $matches['nome'], $ling_destino->getDeclaracao());

    return str_replace('<valor>', $matches['valor'], $prototipo);
}

function transpilaReturn($ling_destino, $valor)
{
    $str = str_replace('<valor>', $valor, $ling_destino->getRetornos());

    if ($ling_destino->getId() != 5 && $ling_destino->getId() != 4)
        $str .= "\n}";

    return $str;
}

function transpilaAtribuicao($ling_destino, $matches, $codigo)
{
    foreach ($matches as $match)
    {
        $aux = $match[0];

        if ($ling_destino->getId() != 1 && $ling_destino->getId() != 2)
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

function analiseC($codigo, $ling_fonte, $ling_destino)
{
	// Transpila um if
	if (preg_match("/if\s?+\((.*?)\)\s?+\n?+\s?+\{/", $codigo, $matches))
	{
        $aux = transpilaIF($ling_destino, $matches);

        $codigo = str_replace($matches[0], $aux, $codigo);
	}
	// Transpila uma funcao
	if (preg_match("/([\w]+)\s([\w]+)\s?\((.*?)\)\s?+\{/", $codigo, $matches))
    {
        $aux = transpilaFuncao($ling_fonte, $ling_destino, $matches[1], $matches[2], $matches[3]);

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

        $aux = transpilaFor($ling_fonte, $ling_destino, $matches);

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

            $aux = transpilaDeclaracao($ling_fonte, $ling_destino, $values);

            $codigo = str_replace($matches[0][$i], $aux, $codigo);
        }
    }
    // Transpila return
    if (preg_match("/return\s+?(.*?)\;\s+?\}/", $codigo, $matches))
    {
        $aux = transpilaReturn($ling_destino, $matches[1]);

        $codigo = str_replace($matches[0], $aux, $codigo);
    }
    // Transpila atribuicoes
    if (preg_match_all("/([\w]+)\s?+([=\-+*\/]+)\s?+(.*)\;/", $codigo, $matches))
    {
        $codigo = transpilaAtribuicao($ling_destino, $matches, $codigo);
    }
    return codigo_final($codigo);
}

function analiseJava($codigo, $ling_fonte, $ling_destino)
{
	// Transpila um if em Java
	if (preg_match("/if\s?+\((.*?)\)\s?+\{/", $codigo, $matches))
	{
		$aux = transpilaIF($ling_destino, $matches);

		$codigo = str_replace($matches[0], $aux, $codigo);
	}
	// Transpila um metodo publico
	if (preg_match("/public\s+([\w]+)\s+([\w]+)\s?+\((.*?)\)\s?+\{/", $codigo, $matches))
    {
        $aux = transpilaFuncao($ling_fonte, $ling_destino, $matches[1], $matches[2], $matches[3]);

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

        $aux = transpilaFor($ling_fonte, $ling_destino, $matches);

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

            $aux = transpilaDeclaracao($ling_fonte, $ling_destino, $values);

            $codigo = str_replace($matches[0][$i], $aux, $codigo);
        }
    }
    // Transpila return
    if (preg_match("/return\s+?(.*?)\;\s+?\}/", $codigo, $matches))
    {
        $aux= transpilaReturn($ling_destino, $matches[1]);

        $codigo = str_replace($matches[0], $aux, $codigo);
    }
    // Transpila atribuicoes
    if (preg_match_all("/([\w]+)\s?+([=\-+*\/]+)\s?+(.*)\;/", $codigo, $matches))
    {
        $codigo = transpilaAtribuicao($ling_destino, $matches, $codigo);
    }
    return codigo_final($codigo);
}

function analiseKotlin($codigo, $ling_fonte, $ling_destino)
{
	// Transpila um if em Kotlin
	if (preg_match("/if\s?+\((.*?)\)\s?+\{/", $codigo, $matches))
	{
        $aux = transpilaIF($ling_destino, $matches);

        $codigo = str_replace($matches[0], $aux, $codigo);
	}
	// Transpila um metodo em Kotlin
	if (preg_match("/fun\s+([\w]+)\s?+\((.*?)\)\s?+\:?\s?+([\w]+)?\s?+\{/", $codigo, $matches))
	{
	    //O array de matches contém o tipo de retorno, nome da função e parametros, respectivamente.
        $aux = transpilaFuncao($ling_fonte, $ling_destino, $matches[3], $matches[1], $matches[2], ':');

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

        $aux = transpilaFor($ling_fonte, $ling_destino, $matches);

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

            $aux = transpilaDeclaracao($ling_fonte, $ling_destino, $values);

            $codigo = str_replace($matches[0][$i], $aux, $codigo);
        }
    }
    // Transpila return
    if (preg_match("/return\s+?(.*?)\;?\s+?\}/", $codigo, $matches))
    {
        $aux = transpilaReturn($ling_destino, $matches[1]);

        $codigo = str_replace($matches[0], $aux, $codigo);
    }

	return codigo_final($codigo);
}

function analisePython($codigo, $ling_fonte, $ling_destino)
{
	// Transpila um if em Python 3
	if (preg_match("/(if)/", $codigo, $matches))
	{

		return transpilaIF($ling_destino, $matches)."\n";
	}

    return 'O codigo não pode ser transpilado!';
}

function analiseHaskell($codigo, $ling_fonte, $ling_destino)
{

}