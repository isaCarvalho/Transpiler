<?php

class AnaliseJava extends Analise
{
    public static function traduz($codigo)
    {
        /// Transpila um if
        $codigo = self::transpilaIF("/if\s?+\((.*?)\)\s?+\n?+\s?+\{/", $codigo);

        // Transpila um else
        $codigo = self::transpilaElse("/(else)\s?+\n?+\s?+\{/", $codigo);

        // Transpila else if
        $codigo = self::transpilaIfElses("/else\s?+if\s?\((.*)\)\s?+\{/", $codigo);

        // Transpila um metodo publico
        if (preg_match_all("/public\s+([\w]+)\s+([\w]+)\s?+\((.*?)\)\s?+\{/", $codigo, $matches))
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

        $codigo = self::transpilaClasse("/public\s+class\s+([\w]+)\s?\{/", $codigo);

        return self::codigo_final($codigo);
    }
}