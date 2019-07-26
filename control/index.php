<?php
require_once "../model/Preencher.php";

extract($_GET);
extract($_POST);

switch ($action) 
{
	case 'enviarFonte':
        require_once "../model/analise.php";

        $result = analisar($cfonte, $lfonte, $ldestino);
        echo json_encode(["prototipo" => $result]);
        break;
	
	case 'carregarLinguagens':
        $p = new Preencher($id_linguagem);
        $p->preencherLinguagens();
        break;

	case 'carregarFunctions':
        $p = new Preencher($id_linguagem);
        $p->preencherFunctions();
        break;

	case 'carregarTipos':
        $p = new Preencher($id_linguagem);
        $p->preencherTipos();
        break;

    case 'carregarIfs':
        $p = new Preencher($id_linguagem);
        $p->preencherIfs();
        break;

	case 'carregarLoops':
        $p = new Preencher($id_linguagem);
        $p->preencherLoops();
        break;

    case 'carregarDeclaracoes':
        $p = new Preencher($id_linguagem);
        $p->preencherDeclaracoes();
        break;

    case 'carregarInformacoes':
        $p = new Preencher($id_linguagem);
        $p->preencherInformacoes();
        break;

	case 'carregarLegendas':
        $p = new Preencher($id_linguagem);
        $p->preencherLegendas();
        break;

	default:
        break;
}