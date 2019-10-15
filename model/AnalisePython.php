<?php

class AnalisePython extends Analise
{
    protected static function traduz($codigo)
    {
        $codigo = self::transpilaClasse("/class\s+([\w]+)\s?:/", $codigo);

        return self::codigo_final($codigo);
    }

    public static function formatar($codigo)
    {
        $codigo = str_replace('{', '', $codigo);
        $codigo = str_replace('}', '', $codigo);
        $codigo = preg_replace("/\}$/s", "", $codigo);

        return $codigo;
    }

}