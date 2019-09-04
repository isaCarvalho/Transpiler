<?php

require_once "Linguagem.php";

class Analise
{
    /** A análise possui dois atributos: a linguagem de origem e a linguagem de destino */
    private $ling_fonte, $ling_destino;

    /** Quando o objeto é instanciado, as duas linguagens são instanciadas de acordo com o id de cada uma */
    public function __construct($id_fonte, $id_destino)
    {
        $this->ling_fonte = new Linguagem($id_fonte);
        $this->ling_destino = new Linguagem($id_destino);
    }

    /** O método analisar é o único método público, com exceção do construtor da classe.
     * Este método é o que direciona para a analise da linguagem desejada de acordo com o seu id*
     */
    public function analisar($codigo)
    {
        switch($this->ling_fonte->getId())
        {
            case 1:
                return $this->analiseC($codigo);
                break;

            case 2:
                return $this->analiseJava($codigo);
                break;

            case 3:
                return $this->analiseKotlin($codigo);
                break;

            case 4:
                $this->analisePython($codigo);
                break;

            case 5:
                $this->analiseHaskell($codigo);
                break;
        }
        return null;
    }

    private function buscaTipo($tipo, $nome, $prototipo)
    {
        // busca o tipo de retorno na linguagem de destino
        for($i = 0; $i < sizeof($this->ling_fonte->getTipos()); $i++)
            if ($tipo == $this->ling_fonte->getTipos()[$i]['tipo'])
                // retorna os tipos primitivos na linguagem de destino
                for ($j = 0; $j < sizeof($this->ling_destino->getTipos()); $j++)
                {
                    if ($this->ling_fonte->getTipos()[$i]['tamanho'] == $this->ling_destino->getTipos()[$j]['tamanho'] &&
                        $this->ling_fonte->getTipos()[$i]['descricao'] == $this->ling_destino->getTipos()[$j]['descricao'])
                        // substitui o tipo e o nome no prototipo passado por parametro
                        return str_replace(['<tipo>', '<nome>'], [$this->ling_destino->getTipos()[$j]['tipo'], $nome], $prototipo);
                }
        return null;
    }

    private function subParametro($prototipo, $parametro, $delimitador = ' ')
    {
        // separa o tipo do nome do parametro
        $aux = explode($delimitador, trim($parametro));

        // retira os espaços do tipo e do nome
        $aux = array_map(function ($a) {
            return trim($a);
        }, $aux);

        // se a linguagem for kotlin, o nome e o tipo deve ser invertido
        if ($this->ling_fonte->getId() == 3)
            return $this->buscaTipo($aux[1], $aux[0], $prototipo);

        return $this->buscaTipo($aux[0], $aux[1], $prototipo);
    }

    private function transpilaIF($codigo, $matches = [])
    {
        // substitui a ocorrencia do if na linguagem de fonte para lingugagem de destino;
        for ($i = 0; $i < sizeof($matches); $i++)
        {
            $aux = str_replace('<exp>', $matches[1][$i], $this->ling_destino->getIf());
            $codigo = str_replace($matches[0][$i], $aux, $codigo);
        }
        return $codigo;
    }

    // Transpila todos os elses para a linguagem de destino
    private function transpilaElse($matches, $codigo)
    {
        for ($i = 0; $i < sizeof($matches[0]); $i++)
        {
            $aux = str_replace($matches[1][$i], $this->ling_destino->getElses(), $matches[1][$i]);
            $codigo = str_replace($matches[0][$i], $aux, $codigo);
        }
        return $codigo;
    }

    private function transpilaFuncao($tipo, $nome, $param, $delimitador = ' ')
    {
        // verifica o tipo correspondente na linguagem de destino
        $prototipo = $this->buscaTipo($tipo, $nome, $this->ling_destino->getFuncoes());

        $str = '';

        // separa os diferentes parametros
        $parametros = explode(',', $param);

        $parLenght = sizeof($parametros);
        // para cada parametro, substitui seu nome e tipo
        for ($i = 0; $i < $parLenght; $i++)
        {
            switch ($this->ling_destino->getId())
            {
                case 1:
                    $str .= $this->subParametro('<tipo> <nome>', $parametros[$i], $delimitador);
                    break;

                case 2:
                    $str .= $this->subParametro('<tipo> <nome>', $parametros[$i], $delimitador);
                    break;

                case 3:
                    $str .= $this->subParametro('<nome> : <tipo>', $parametros[$i], $delimitador);
                    break;

                case 4:
                    $str .= $this->subParametro('<nome>', $parametros[$i], $delimitador);
                    break;

                case 5:
                    $str .= $this->subParametro('<nome>', $parametros[$i], $delimitador);
                    break;
            }

            if ($this->ling_destino->getId() != 5 && $i != $parLenght-1)
                $str .= ', ';
            else if ($this->ling_destino->getId() == 5 && $i != $parLenght-1)
                $str .= ' ';
        }
        //substitui todos os parametros no prototipo
        return str_replace('<param>', $str, $prototipo);
    }

    private function transpilaFor($matches = [])
    {
        $for = $this->ling_destino->getFors();

        // busca o tipo correspondente na linguagem de destino
        $tipo = $this->buscaTipo($matches['tipo'], $matches['var'], '<tipo>');

        $novo = [];
        $antigo = [];
        $prototipo = '';
        // substitui os valores no prototipo de acordo com a linguagem de destino
        switch ($this->ling_destino->getId())
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

    private function transpilaDeclaracao($matches = [])
    {
        // retorna o tipo na liguagem de destino
        $prototipo = $this->buscaTipo($matches['tipo'], $matches['nome'], $this->ling_destino->getDeclaracao());

        return str_replace('<valor>', $matches['valor'], $prototipo);
    }

    private function transpilaReturn($valor)
    {
        $str = str_replace('<valor>', $valor, $this->ling_destino->getRetornos());

        if ($this->ling_destino->getId() != 5 && $this->ling_destino->getId() != 4)
            $str .= "\n}";
        else if ($this->ling_destino->getId() == 4)
            $str .= "\n";

        return $str;
    }

// Transpila a atribuicao para a linguagem de destino
    private function transpilaAtribuicao($matches, $codigo)
    {
        foreach ($matches as $match)
        {
            $aux = $match[0];

            if ($this->ling_destino->getId() != 1 && $this->ling_destino->getId() != 2)
                $aux = str_replace(';', '', $match[0]);

            $codigo = str_replace($match[0], $aux,$codigo);
        }
        return $codigo;
    }

// Formata o código final tirando espaços desnecessários e chaves, quando necessário
    private function format($id_destino, $codigo)
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

    private function codigo_final($codigo_final)
    {
        if (strlen($codigo_final))
            return $this->format($this->ling_destino->getId(), $codigo_final);

        return 'O codigo não pode ser transpilado!';
    }

    private function functionIf($codigo)
    {
        if (preg_match_all("/if\s?+\((.*?)\)\s?+\n?+\s?+\{/", $codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[0]); $i++)
            {
                $codigo = $this->transpilaIF($codigo, $matches);
            }
        }
        return $codigo;
    }

    private function functionElse($codigo)
    {
        if (preg_match_all("/(else)\s?+\n?+\s?+\{/", $codigo, $matches))
        {
            $codigo = $this->transpilaElse($matches, $codigo);
        }
        return $codigo;
    }

    private function functionFor($codigo)
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

                $aux = $this->transpilaFor($values);

                $codigo = str_replace($match, $aux, $codigo);
            }
        }
        return $codigo;
    }

    private function functionReturn($codigo)
    {

        if (preg_match_all("/return\s+?(.*?)\;\s+?\}/", $codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[0]); $i++)
            {
                $aux= $this->transpilaReturn($matches[1][$i]);

                $codigo = str_replace($matches[0][$i], $aux, $codigo);
            }
        }
        return $codigo;
    }

    private function functionDeclaracao($codigo)
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

                $aux = $this->transpilaDeclaracao($values);

                $codigo = str_replace($matches[0][$i], $aux, $codigo);
            }
        }
        return $codigo;
    }

    private function functionAtribuicao($codigo)
    {
        if (preg_match_all("/([\w]+)\s?+([=\-+*\/]+)\s?+(.*)\;/", $codigo, $matches))
            $codigo = $this->transpilaAtribuicao($matches, $codigo);

        return $codigo;
    }

    private function functionIfElses($codigo)
    {
        if (preg_match_all("/else\s?+if\s?\((.*)\)\s?+\{/", $codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[0]); $i++) {
                $aux = str_replace('<exp>', $matches[1][$i], $this->ling_destino->getElseIfs());

                $codigo = str_replace($matches[0][$i], $aux, $codigo);
            }
        }

        return $codigo;
    }

    private function analiseC($codigo)
    {
        // Transpila um else
        $codigo = $this->functionElse($codigo);

        // Transpila else if
        $codigo = $this->functionIfElses($codigo);

        // Transpila um if
        $codigo = $this->functionIf($codigo);

        // Transpila uma funcao
        if (preg_match_all("/([\w]+[^else])\s([\w]+)\s?\((.*?)\)\s?+\{/", $codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[0]); $i++)
            {
                $aux = $this->transpilaFuncao($matches[1][$i], $matches[2][$i], $matches[3][$i]);

                $codigo = str_replace($matches[0][$i], $aux, $codigo);
            }
        }
        // Transpila um for padrao
        $codigo = $this->functionFor($codigo);

        // Transpila uma declaracao de variavel
        $codigo = $this->functionDeclaracao($codigo);

        // Transpila return
        $codigo = $this->functionReturn($codigo);

        // Transpila atribuicoes
        $codigo = $this->functionAtribuicao($codigo);

        if (preg_match_all("/printf\((.*?)\)\;/", $codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[0]); $i++)
            {
                $parametros = explode(',', $matches[1][$i]);
                var_dump($parametros);

                $aux = str_replace("<param>", $matches[1][$i], $this->ling_destino->getPrints());

                $codigo = str_replace($matches[0][$i], $aux, $codigo);
            }
        }

        return $this->codigo_final($codigo);
    }

    private function analiseJava($codigo)
    {
        /// Transpila um if
        $codigo = $this->functionIf($codigo);

        // Transpila um else
        $codigo = $this->functionElse($codigo);

        // Transpila else if
        $codigo = $this->functionIfElses($codigo);

        // Transpila um metodo publico
        if (preg_match_all("/public\s+([\w]+)\s+([\w]+)\s?+\((.*?)\)\s?+\{/", $codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[0]); $i++)
            {
                $aux = $this->transpilaFuncao($matches[1][$i], $matches[2][$i], $matches[3][$i]);

                $codigo = str_replace($matches[0][$i], $aux, $codigo);
            }
        }
        // Transpila um for padrao
        $codigo = $this->functionFor($codigo);

        // Transpila uma declaracao de variavel
        $codigo = $this->functionDeclaracao($codigo);

        // Transpila return
        $codigo = $this->functionReturn($codigo);

        // Transpila atribuicoes
        $codigo = $this->functionAtribuicao($codigo);

        return $this->codigo_final($codigo);
    }

    private function analiseKotlin($codigo)
    {
        // Transpila um if
        $codigo = $this->functionIf($codigo);

        // Transpila um else
        $codigo = $this->functionElse($codigo);

        // Transpila else if
        $codigo = $this->functionIfElses($codigo);

        // Transpila um metodo em Kotlin
        if (preg_match_all("/fun\s+([\w]+)\s?+\((.*?)\)\s?+\:?\s?+([\w]+)?\s?+\{/", $codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[0]); $i++)
            {
                //O array de matches contém o tipo de retorno, nome da função e parametros, respectivamente.
                $aux = $this->transpilaFuncao($matches[3][$i], $matches[1][$i], $matches[2][$i], ':');

                $codigo = str_replace($matches[0][$i], $aux, $codigo);
            }
        }
        // Transpila um laço for padrão
        if (preg_match_all("/for\s?+\(([\w]+)\s?+\:\s?+([\w]+)\s?+in\s?+([\d]+)..([\d]+)\s?+\)\s?+\{/", $codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[0]); $i++)
            {
                $match = $matches[0][$i];
                $values = [
                    'tipo' => $matches[2][$i],
                    'var' => $matches[1][$i],
                    'inicio' => $matches[3][$i],
                    'cond' => '<',
                    'fim' => $matches[4][$i],
                    'incr' => '++'
                ];

                $aux = $this->transpilaFor($values);

                $codigo = str_replace($match, $aux, $codigo);
            }
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

                $aux = $this->transpilaDeclaracao($values);

                $codigo = str_replace($matches[0][$i], $aux, $codigo);
            }
        }
        // Transpila return
        if (preg_match_all("/return\s+?(.*?)\;?\s+?\}/", $codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[0]); $i++)
            {
                $aux = $this->transpilaReturn($matches[1][$i]);

                $codigo = str_replace($matches[0][$i], $aux, $codigo);
            }
        }
        return $this->codigo_final($codigo);
    }

    private function analisePython($codigo)
    {

    }

    private function analiseHaskell($codigo)
    {

    }
}