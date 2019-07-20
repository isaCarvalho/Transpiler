<?php

abstract class Linguagem
{
    const C = 1;
    const JAVA = 2;
    const KOTLIN = 3;
    const PYTHON = 4;
    const HASKELL = 5;

    public function take_out_key($codigo)
    {
        return str_replace(['{', '}'], ['', ''], $codigo);
    }
}