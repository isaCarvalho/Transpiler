<?php

// Classe que realiza a tradução
class Tradutor
{
    private $linguagem;
    private $analise;
    private $codigo;

    public function __construct(Analise $analise, Linguagem $linguagem, $codigo)
    {
        $this->codigo = $codigo;
        $this->linguagem = $linguagem;
        $this->analise = $analise;
    }

    protected function buscaTipo($tipo, $nome, $prototipo)
    {
        // busca o tipo de retorno na linguagem de destino
        $ling_fonte = $this->analise->getLinguagem();
        for($i = 0; $i < sizeof($ling_fonte->getTipos()); $i++)
            if ($tipo == $ling_fonte->getTipos()[$i]['tipo'])
                // retorna os tipos primitivos na linguagem de destino
                for ($j = 0; $j < sizeof($this->linguagem->getTipos()); $j++)
                {
                    if ($ling_fonte->getTipos()[$i]['tamanho'] == $this->linguagem->getTipos()[$j]['tamanho'] &&
                        $ling_fonte->getTipos()[$i]['descricao'] == $this->linguagem->getTipos()[$j]['descricao'])
                        // substitui o tipo e o nome no prototipo passado por parametro
                        return str_replace(['<tipo>', '<nome>'], [$this->linguagem->getTipos()[$j]['tipo'], $nome], $prototipo);
                }
        return null;
    }

    protected function subParametro($prototipo, $parametro, $delimitador = ' ')
    {
        // separa o tipo do nome do parametro
        $aux = explode($delimitador, trim($parametro));

        // retira os espaços do tipo e do nome
        $aux = array_map(static function ($a) {
            return trim($a);
        }, $aux);

        $ling_fonte = $this->analise->getLinguagem();
        // se a linguagem for kotlin, o nome e o tipo deve ser invertido
        if ($ling_fonte->getId() == 3)
            return $this->buscaTipo($aux[1], $aux[0], $prototipo);

        return $this->buscaTipo($aux[0], $aux[1], $prototipo);
    }

    protected function transpilaIF()
    {
        $regex = $this->analise->getRegexIf();
        if (preg_match_all($regex, $this->codigo, $matches)) {
            // substitui a ocorrencia do if na linguagem de fonte para lingugagem de destino;
            for ($i = 0; $i < sizeof($matches); $i++) {
                $aux = str_replace('<exp>', $matches[1][$i], $this->linguagem->getIf());
                $this->codigo = str_replace($matches[0][$i], $aux, $this->codigo);
            }
        }
    }

    protected function transpilaElse()
    {
        $regex = $this->analise->getRegexElse();
        if (preg_match_all($regex, $this->codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[0]); $i++)
            {
                $aux = str_replace($matches[1][$i], $this->linguagem->getElses(), $matches[1][$i]);
                $this->codigo = str_replace($matches[0][$i], $aux, $this->codigo);
            }
        }
    }

    protected function transpilaIfElses()
    {
        $regex = $this->analise->getRegexElseIf();
        if (preg_match_all($regex, $this->codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[0]); $i++) {
                $aux = str_replace('<exp>', $matches[1][$i], $this->linguagem->getElseIfs());

                $this->codigo = str_replace($matches[0][$i], $aux, $this->codigo);
            }
        }
    }

    protected function transpilaFuncao($tipo, $nome, $param, $delimitador = ' ')
    {
        // verifica o tipo correspondente na linguagem de destino
        $prototipo = self::buscaTipo($tipo, $nome, $this->linguagem->getFuncoes());

        $str = '';

        // separa os diferentes parametros
        $parametros = explode(',', $param);

        $parLenght = sizeof($parametros);
        // para cada parametro, substitui seu nome e tipo
        for ($i = 0; $i < $parLenght; $i++)
        {
            switch ($this->linguagem->getId())
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

            if ($this->linguagem->getId() != 5 && $i != $parLenght-1)
                $str .= ', ';
            else if ($this->linguagem->getId() == 5 && $i != $parLenght-1)
                $str .= ' ';
        }
        //substitui todos os parametros no prototipo
        return str_replace('<param>', $str, $prototipo);
    }

    protected function transpilaFor($matches = [])
    {
        $for = $this->linguagem->getFors();

        // busca o tipo correspondente na linguagem de destino
        $tipo = self::buscaTipo($matches['tipo'], $matches['var'], '<tipo>');

        $novo = [];
        $antigo = [];
        $prototipo = '';
        // substitui os valores no prototipo de acordo com a linguagem de destino
        switch ($this->linguagem->getId())
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

    protected function transpilaDeclaracao($matches = [])
    {
        // retorna o tipo na liguagem de destino
        $prototipo = $this->buscaTipo($matches['tipo'], $matches['nome'], $this->linguagem->getDeclaracao());

        return str_replace('<valor>', $matches['valor'], $prototipo);
    }

    protected function transpilaReturn($valor)
    {
        $str = str_replace('<valor>', $valor, $this->linguagem->getRetornos());

        if ($this->linguagem->getId() != 5 && $this->linguagem->getId() != 4)
            $str .= "\n}";
        else if ($this->linguagem->getId() == 4)
            $str .= "\n";

        return $str;
    }

// Transpila a atribuicao para a linguagem de destino
    protected function transpilaAtribuicao($matches)
    {
        foreach ($matches as $match)
        {
            $aux = $match[0];

            if ($this->linguagem->getId() != 1 && $this->linguagem->getId() != 2)
                $aux = str_replace(';', '', $match[0]);

            $this->codigo = str_replace($match[0], $aux, $this->codigo);
        }
    }

    protected function transpilaClasse()
    {
        $regex = $this->analise->getRegexClass();
        if ($regex == "")
        {
            $aux = str_replace("<nome>", "ClasseExemplo", $this->linguagem->getClassDeclaration());
            $this->codigo = $aux . "\n" . $this->codigo . "\n}";
        }
        else if (preg_match($regex, $this->codigo, $matches))
        {
            $classDec = $this->linguagem->getClassDeclaration();

            if ($classDec == "")
                $this->codigo = str_replace($matches[0], "", $this->codigo);
            else {
                $aux = str_replace("<nome>", $matches[1], $classDec);
                $this->codigo = str_replace($matches[0], $aux, $this->codigo);
            }
        }
    }

    protected function transpilaPrint()
    {
        $regex = $this->analise->getRegexPrint();
        // Transpila print
        if (preg_match_all($regex, $this->codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[0]); $i++)
            {
                $parametros = explode(',', $matches[1][$i]);

                $print = "";
                switch ($this->linguagem->getId())
                {
                    case 1:
                        $print = $matches[1][$i];
                        break;

                    case 2:
                        preg_match_all('/\%\.*\d*\w/', $parametros[0], $matchesPrint);

                        $string = $parametros[0];
                        for ($k = 0; $k < sizeof($matchesPrint[0]); $k++)
                            if ($k == sizeof($parametros))
                                $string = str_replace($matchesPrint[0][$k], '" + ' . $parametros[$k+1], $string);
                            else
                                $string = str_replace($matchesPrint[0][$k], '" + ' . $parametros[$k+1] . ' + "', $string);

                        $print = $string;
                        break;

                    case 3:
                        preg_match_all('/\%\.*\d*\w/', $parametros[0], $matchesPrint);

                        $string = $parametros[0];
                        for ($k = 0; $k < sizeof($matchesPrint[0]); $k++)
                            $string = str_replace($matchesPrint[0][$k], '${' .
                                str_replace(" ", "",$parametros[$k+1]) . '}', $string);

                        $print = $string;
                        break;

                    case 4:
                        preg_match_all('/\%\.*\d*\w/', $parametros[0], $matchesPrint);

                        $string = $parametros[0];
                        for ($k = 0; $k < sizeof($matchesPrint[0]); $k++)
                        {
                            $aux = str_replace("%", '{:', $matchesPrint[0][$k]) . "}";
                            $string = str_replace($matchesPrint[0][$k], $aux, $string);
                        }

                        $string .= ".format(";
                        for ($k = 1; $k < sizeof($parametros); $k++)
                        {
                            $string .= str_replace(" ", "", $parametros[$k]);
                            if ($k != sizeof($parametros)-1)
                                $string .= ',';
                        }

                        $string .= ")";

                        $print = $string;
                        break;

                    case 5:
                        preg_match_all('/\%\.*\d*\w/', $parametros[0], $matchesPrint);

                        $string = $parametros[0];
                        for ($k = 0; $k < sizeof($matchesPrint[0]); $k++)
                            if ($k == sizeof($parametros))
                                $string = str_replace($matchesPrint[0][$k], '" ++ ' . $parametros[$k+1], $string);
                            else
                                $string = str_replace($matchesPrint[0][$k], '" ++ ' . $parametros[$k+1] . ' ++ "', $string);

                        if (sizeof($parametros) != 1)
                            $string = "(" . $string . ")";

                        $print = $string;
                        break;

                    default:
                        break;
                }

                $aux = str_replace("<param>", $print, $this->linguagem->getPrints());
                if ($this->linguagem->getId() == 1 or $this->linguagem->getId() == 2)
                    $aux .= ";";

                $this->codigo = str_replace($matches[0][$i], $aux, $this->codigo);
            }
        }

    }

    public function functionFor()
    {
        $regex = $this->analise->getRegexFor();
        if (preg_match_all($regex, $this->codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[$i]); $i++)
            {
                $match = $matches[0][$i];
                $values = $this->analise->getValuesFor($matches, $i);

                $aux = self::transpilaFor($values);

                $this->codigo = str_replace($match, $aux, $this->codigo);
            }
        }
    }

    protected function functionReturn()
    {
        $regex = $this->analise->getRegexReturn();
        if (preg_match_all($regex, $this->codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[0]); $i++)
            {
                $aux= $this->transpilaReturn($matches[1][$i]);

                $this->codigo = str_replace($matches[0][$i], $aux, $this->codigo);
            }
        }
    }

    protected function functionDeclaracao()
    {
        $regex = $this->analise->getRegexDeclaration();
        if (preg_match_all($regex, $this->codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches)-1; $i++)
            {
                $values = $this->analise->getValuesDeclaration($matches, $i);

                $aux = self::transpilaDeclaracao($values);

                $this->codigo = str_replace($matches[0][$i], $aux, $this->codigo);
            }
        }
    }

    protected function functionAtribuicao()
    {
        if (preg_match_all("/([\w]+)\s?+([=\-+*\/]+)\s?+(.*)\;/", $this->codigo, $matches))
            $this->transpilaAtribuicao($matches);
    }

    protected function functionFuncao()
    {
        $regex = $this->analise->getRegexFunction();
        $delim = $this->analise->getDelimitador();
        if (preg_match_all($regex, $this->codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[0]); $i++)
            {
                $values = $this->analise->getValuesFunction($matches, $i);
                $aux = self::transpilaFuncao($values['tipo'], $values['nome'], $values['param'], $delim);

                $this->codigo = str_replace($matches[0][$i], $aux, $this->codigo);
            }
        }
    }

    protected function codigo_final()
    {
        $this->codigo =  trim($this->analise->formatar($this->codigo));
    }

    public function traduz()
    {
        $this->functionFor();
        $this->transpilaIfElses();
        $this->transpilaIF();
        $this->transpilaElse();
        $this->functionFuncao();
        $this->functionReturn();
        $this->functionDeclaracao();
        $this->functionAtribuicao();
        $this->transpilaClasse();
        $this->transpilaPrint();
        $this->codigo_final();

        return $this->codigo;
    }
}