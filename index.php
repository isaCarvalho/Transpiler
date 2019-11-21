<?php
include "control/index.php";

class View
{
    public function render(String $pagina, $header = true, $footer = true)
    {
        if ($header)
            include "view/header.html";

        $path = "view/" . $pagina . '.html';
        include "$path";

        if ($footer)
            include "view/footer.html";

    }
}