<?php

class AnaliseKotlin extends Analise
{
    protected static function traduz($codigo)
    {
        // Transpila um if
        $codigo = self::transpilaIF("/if\s?+\((.*?)\)\s?+\n?+\s?+\{/", $codigo);

        // Transpila um else
        $codigo = self::transpilaElse("/(else)\s?+\n?+\s?+\{/", $codigo);

        // Transpila else if
        $codigo = self::transpilaIfElses("/else\s?+if\s?\((.*)\)\s?+\{/", $codigo);

        // Transpila um metodo em Kotlin
        if (preg_match_all("/fun\s+([\w]+)\s?+\((.*?)\)\s?+\:?\s?+([\w]+)?\s?+\{/", $codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[0]); $i++)
            {
                //O array de matches contém o tipo de retorno, nome da função e parametros, respectivamente.
                $aux = self::transpilaFuncao($matches[3][$i], $matches[1][$i], $matches[2][$i], ':');

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

                $aux = self::transpilaFor($values);

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

                $aux = self::transpilaDeclaracao($values);

                $codigo = str_replace($matches[0][$i], $aux, $codigo);
            }
        }
        // Transpila return
        if (preg_match_all("/return\s+?(.*?)\;?\s+?\}/", $codigo, $matches))
        {
            for ($i = 0; $i < sizeof($matches[0]); $i++)
            {
                $aux = self::transpilaReturn($matches[1][$i]);

                $codigo = str_replace($matches[0][$i], $aux, $codigo);
            }
        }

        $codigo = self::transpilaClasse("/class\s+([\w]+)\s?\{/", $codigo);

        return self::codigo_final($codigo);
    }
}