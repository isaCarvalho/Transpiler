<?php

require_once "Linguagem.php";
require_once "AnaliseC.php";
require_once "AnaliseJava.php";
require_once "AnaliseKotlin.php";
require_once "AnalisePython.php";
require_once "AnaliseHaskell.php";

abstract class Analise
{
    /** A análise possui dois atributos: a linguagem de origem e a linguagem de destino */
    protected static $ling_fonte, $ling_destino;

    /** O método analisar é o único método público, com exceção do construtor da classe.
     * Este método é o que direciona para a analise da linguagem desejada de acordo com o seu id*
     */
    public static function analisar($codigo, $id_fonte, $id_destino)
    {
        self::$ling_fonte = new Linguagem($id_fonte);
        self::$ling_destino = new Linguagem($id_destino);

        switch(self::$ling_fonte->getId())
        {
            case 1:
                return AnaliseC::traduz($codigo);
                break;

            case 2:
                return AnaliseJava::traduz($codigo);
                break;

            case 3:
                return AnaliseKotlin::traduz($codigo);
                break;

            case 4:
                AnalisePython::traduz($codigo);
                break;

            case 5:
                AnaliseHaskell::traduz($codigo);
                break;
        }
        return null;
    }

    protected static function buscaTipo($tipo, $nome, $prototipo)
    {
        // busca o tipo de retorno na linguagem de destino
        for($i = 0; $i < sizeof(self::$ling_fonte->getTipos()); $i++)
            if ($tipo == self::$ling_fonte->getTipos()[$i]['tipo'])
                // retorna os tipos primitivos na linguagem de destino
                for ($j = 0; $j < sizeof(self::$ling_destino->getTipos()); $j++)
                {
                    if (self::$ling_fonte->getTipos()[$i]['tamanho'] == self::$ling_destino->getTipos()[$j]['tamanho'] &&
                        self::$ling_fonte->getTipos()[$i]['descricao'] == self::$ling_destino->getTipos()[$j]['descricao'])
                        // substitui o tipo e o nome no prototipo passado por parametro
                        return str_replace(['<tipo>', '<nome>'], [self::$ling_destino->getTipos()[$j]['tipo'], $nome], $prototipo);
                }
        return null;
    }

    protected static function subParametro($prototipo, $parametro, $delimitador = ' ')
    {
        // separa o tipo do nome do parametro
        $aux = explode($delimitador, trim($parametro));

        // retira os espaços do tipo e do nome
        $aux = array_map(static function ($a) {
            return trim($a);
        }, $aux);

        // se a linguagem for kotlin, o nome e o tipo deve ser invertido
        if (self::$ling_fonte->getId() == 3)
            return self::buscaTipo($aux[1], $aux[0], $prototipo);

        return self::buscaTipo($aux[0], $aux[1], $prototipo);
    }

    protected static function transpilaIF($regex, $codigo, $matches = [])
    {
        if (preg_match_all($regex, $codigo, $matches)) {
            // substitui a ocorrencia do if na linguagem de fonte para lingugagem de destino;
            for ($i = 0; $i < sizeof($matches); $i++) {
                $aux = str_replace('<exp>', $matches[1][$i], self::$ling_destino->getIf());
                $codigo = str_replace($matches[0][$i], $aux, $codigo);
            }
        }
        return $codigo;
    }

    // Transpila todos os elses para a linguagem de destino
    protected static function transpilaElse($regex, $codigo)
    {
        if (preg_match_all($regex, $codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[0]); $i++)
            {
                $aux = str_replace($matches[1][$i], self::$ling_destino->getElses(), $matches[1][$i]);
                $codigo = str_replace($matches[0][$i], $aux, $codigo);
            }
        }

        return $codigo;
    }

    protected static function transpilaIfElses($regex, $codigo)
    {
        if (preg_match_all($regex, $codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[0]); $i++) {
                $aux = str_replace('<exp>', $matches[1][$i], self::$ling_destino->getElseIfs());

                $codigo = str_replace($matches[0][$i], $aux, $codigo);
            }
        }

        return $codigo;
    }

    protected static function transpilaFuncao($tipo, $nome, $param, $delimitador = ' ')
    {
        // verifica o tipo correspondente na linguagem de destino
        $prototipo = self::buscaTipo($tipo, $nome, self::$ling_destino->getFuncoes());

        $str = '';

        // separa os diferentes parametros
        $parametros = explode(',', $param);

        $parLenght = sizeof($parametros);
        // para cada parametro, substitui seu nome e tipo
        for ($i = 0; $i < $parLenght; $i++)
        {
            switch (self::$ling_destino->getId())
            {
                case 1:
                    $str .= self::subParametro('<tipo> <nome>', $parametros[$i], $delimitador);
                    break;

                case 2:
                    $str .= self::subParametro('<tipo> <nome>', $parametros[$i], $delimitador);
                    break;

                case 3:
                    $str .= self::subParametro('<nome> : <tipo>', $parametros[$i], $delimitador);
                    break;

                case 4:
                    $str .= self::subParametro('<nome>', $parametros[$i], $delimitador);
                    break;

                case 5:
                    $str .= self::subParametro('<nome>', $parametros[$i], $delimitador);
                    break;
            }

            if (self::$ling_destino->getId() != 5 && $i != $parLenght-1)
                $str .= ', ';
            else if (self::$ling_destino->getId() == 5 && $i != $parLenght-1)
                $str .= ' ';
        }
        //substitui todos os parametros no prototipo
        return str_replace('<param>', $str, $prototipo);
    }

    protected static function transpilaFor($matches = [])
    {
        $for = self::$ling_destino->getFors();

        // busca o tipo correspondente na linguagem de destino
        $tipo = self::buscaTipo($matches['tipo'], $matches['var'], '<tipo>');

        $novo = [];
        $antigo = [];
        $prototipo = '';
        // substitui os valores no prototipo de acordo com a linguagem de destino
        switch (self::$ling_destino->getId())
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

    protected static function transpilaDeclaracao($matches = [])
    {
        // retorna o tipo na liguagem de destino
        $prototipo = self::buscaTipo($matches['tipo'], $matches['nome'], self::$ling_destino->getDeclaracao());

        return str_replace('<valor>', $matches['valor'], $prototipo);
    }

    protected static function transpilaReturn($valor)
    {
        $str = str_replace('<valor>', $valor, self::$ling_destino->getRetornos());

        if (self::$ling_destino->getId() != 5 && self::$ling_destino->getId() != 4)
            $str .= "\n}";
        else if (self::$ling_destino->getId() == 4)
            $str .= "\n";

        return $str;
    }

// Transpila a atribuicao para a linguagem de destino
    protected static function transpilaAtribuicao($matches, $codigo)
    {
        foreach ($matches as $match)
        {
            $aux = $match[0];

            if (self::$ling_destino->getId() != 1 && self::$ling_destino->getId() != 2)
                $aux = str_replace(';', '', $match[0]);

            $codigo = str_replace($match[0], $aux,$codigo);
        }
        return $codigo;
    }

// Formata o código final tirando espaços desnecessários e chaves, quando necessário
    protected static function format($id_destino, $codigo)
    {
        if ($id_destino == 4 || $id_destino == 5)
        {
            $codigo = str_replace('{', '', $codigo);
            $codigo = str_replace('}', '', $codigo);
        }
        else if ($id_destino == 5)
            $codigo = str_replace("\n", '', $codigo);

        return trim($codigo);
    }

    protected static function codigo_final($codigo_final)
    {
        if (strlen($codigo_final))
            return self::format(self::$ling_destino->getId(), $codigo_final);

        return 'O codigo não pode ser transpilado!';
    }

    protected static function functionFor($codigo)
    {
        $regex = "/for\s?+\(\s?+([\w]+)\s\s?+([\w+])\s?+\=\s?+(.*)\s?+\;\s?+[\w]+\s?+([<>!=]+)\s?+(.*)\s?+\;\s?+[\w]+(.*)\)\s?+\{/";
        if (preg_match_all($regex, $codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[$i]); $i++)
            {
                $match = $matches[0][$i];
                $values = [
                    'tipo' => $matches[1][$i],
                    'var' => $matches[2][$i],
                    'inicio' => $matches[3][$i],
                    'cond' => $matches[4][$i],
                    'fim' => $matches[5][$i],
                    'incr' => $matches[6][$i]
                ];

                $aux = self::transpilaFor($values);

                $codigo = str_replace($match, $aux, $codigo);
            }
        }
        return $codigo;
    }

    protected static function functionReturn($codigo)
    {

        if (preg_match_all("/return\s+?(.*?)\;\s+?\}/", $codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[0]); $i++)
            {
                $aux= self::transpilaReturn($matches[1][$i]);

                $codigo = str_replace($matches[0][$i], $aux, $codigo);
            }
        }
        return $codigo;
    }

    protected static function functionDeclaracao($codigo)
    {
        if (preg_match_all("/([\w]+)\s\s?+([\w]?+)\s?+\=\s?+(.*)\;/", $codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches)-1; $i++)
            {
                $values = [
                    'tipo' => $matches[1][$i],
                    'nome' => $matches[2][$i],
                    'valor' => $matches[3][$i]
                ];

                $aux = self::transpilaDeclaracao($values);

                $codigo = str_replace($matches[0][$i], $aux, $codigo);
            }
        }
        return $codigo;
    }

    protected static function functionAtribuicao($codigo)
    {
        if (preg_match_all("/([\w]+)\s?+([=\-+*\/]+)\s?+(.*)\;/", $codigo, $matches))
            $codigo = self::transpilaAtribuicao($matches, $codigo);

        return $codigo;
    }

    protected abstract static function traduz($codigo);
}