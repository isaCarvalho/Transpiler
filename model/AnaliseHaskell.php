<?php

class AnaliseHaskell extends Analise
{
    public static function traduz($codigo)
    {
        return $codigo;
    }

    public static function formatar($codigo)
    {
        $codigo = str_replace('{', '', $codigo);
        $codigo = str_replace('}', '', $codigo);
        $codigo = preg_replace("/\}$/s", "", $codigo);
        $codigo = str_replace("\n", '', $codigo);
        $codigo = preg_replace('/\s+\=/sm', ' =', $codigo);

        return $codigo;
    }
}