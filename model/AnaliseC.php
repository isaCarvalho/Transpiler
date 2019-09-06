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

        if (preg_match_all("/printf\((.*?)\)\;/", $codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[0]); $i++)
            {
                $parametros = explode(',', $matches[1][$i]);
                var_dump($parametros);

                $aux = str_replace("<param>", $matches[1][$i], self::$ling_destino->getPrints());

                $codigo = str_replace($matches[0][$i], $aux, $codigo);
            }
        }

        return self::codigo_final($codigo);
    }
}