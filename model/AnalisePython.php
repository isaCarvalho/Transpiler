<?php

class AnalisePython extends Analise
{
    public static function traduz($codigo)
    {
        $codigo = self::transpilaClasse("/class\s+([\w]+)\s?:/", $codigo);

        return self::codigo_final($codigo);
    }
}