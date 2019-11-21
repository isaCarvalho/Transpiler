<?php
include "control/index.php";

class View
{
    public function render(String $pagina)
    {
        include_once "view/header.html";

        $path = "view/" . $pagina . '.html';
        include_once $path;

        include_once "view/footer.html";
    }
}