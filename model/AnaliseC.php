<?php

class AnaliseC extends Analise
{
    public static function traduz($codigo)
    {
        // Transpila um else
        $codigo = self::transpilaElse("/(else)\s?+\n?+\s?+\{/", $codigo);

        // Transpila else if
        $codigo = self::transpilaIfElses("/else\s?+if\s?\((.*)\)\s?+\{/", $codigo);

        // Transpila um if
        $codigo = self::transpilaIf("/if\s?+\((.*?)\)\s?+\n?+\s?+\{/", $codigo);

        // Transpila uma funcao
        if (preg_match_all("/([\w]+[^else])\s([\w]+)\s?\((.*?)\)\s?+\{/", $codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[0]); $i++)
            {
                $aux = self::transpilaFuncao($matches[1][$i], $matches[2][$i], $matches[3][$i]);

                $codigo = str_replace($matches[0][$i], $aux, $codigo);
            }
        }
        // Transpila um for padrao
        $codigo = self::functionFor($codigo);

        // Transpila uma declaracao de variavel
        $codigo = self::functionDeclaracao($codigo);

        // Transpila return
        $codigo = self::functionReturn($codigo);

        // Transpila atribuicoes
        $codigo = self::functionAtribuicao($codigo);

        // Transpila print
        if (preg_match_all("/printf\((.*?)\)\;/", $codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[0]); $i++)
            {
                $parametros = explode(',', $matches[1][$i]);

                switch (self::$ling_destino->getId())
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
//                        var_dump($string);

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

                $aux = str_replace("<param>", $print, self::$ling_destino->getPrints());
                if (self::$ling_destino->getId() == 1 or self::$ling_destino->getId() == 2)
                    $aux .= ";";

                $codigo = str_replace($matches[0][$i], $aux, $codigo);
            }
        }

        return self::codigo_final($codigo);
    }
}