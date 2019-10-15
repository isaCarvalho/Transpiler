<?php

class AnaliseHaskell extends Analise
{
    protected static function traduz($codigo)
    {
        return self::codigo_final($codigo);
    }

    public static function formatar($codigo)
    {
        $codigo = str_replace('{', '', $codigo);
        $codigo = str_replace('}', '', $codigo);
        $codigo = preg_replace("/\}$/s", "", $codigo);
        $codigo = str_replace("\n", '', $codigo);
        $codigo = preg_replace('/\s+\=/sm', ' =', $codigo);
        $codigo = preg_replace('/\s+\|/', "\n  |", $codigo);

        return $codigo;
    }
}