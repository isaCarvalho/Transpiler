<?php
require_once "../model/Preencher.php";

extract($_GET);
extract($_POST);

$p = new Preencher($id_linguagem);

switch ($action) 
{
	case 'enviarFonte':
        require_once "../model/Analise.php";

        $result = Analise::analisar($cfonte, $lfonte, $ldestino);
        echo json_encode(["prototipo" => $result]);
        break;

	case 'carregarLinguagens':
        $p->preencherLinguagens();
        break;

    case 'carregarPrints':
        $p->preencherPrints();
        break;

    case 'carregarReturns':
        $p->preencherReturns();
        break;

	case 'carregarFunctions':
        $p->preencherFunctions();
        break;

	case 'carregarTipos':
        $p->preencherTipos();
        break;

    case 'carregarIfs':
        $p->preencherIfs();
        break;

	case 'carregarLoops':
        $p->preencherLoops();
        break;

    case 'carregarDeclaracoes':
        $p->preencherDeclaracoes();
        break;

    case 'carregarInformacoes':
        $p->preencherInformacoes();
        break;

	case 'carregarLegendas':
        $p->preencherLegendas();
        break;

	default:
        break;
}