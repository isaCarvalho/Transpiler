<?php

class View
{
    /*
     * Metodo que renderiza a pagina
     */
    public function render(String $pagina)
    {
        include_once "view/paginas/header.html";

        $path = "view/paginas/" . $pagina . '.html';
        include_once $path;

        include_once "view/paginas/footer.html";
    }
}