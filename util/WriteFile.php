<?php

class WriteFile
{
    public static function saveFile($nome, $conteudo)
    {
        try {
            $fp = fopen($nome, 'w');

            fwrite($fp, json_encode($conteudo));
            fclose($fp);

            var_dump($conteudo);
            return "arquivo salvo com sucesso!";
        }
        catch (Exception $e)
        {
            return $e->getMessage();
        }
    }
}