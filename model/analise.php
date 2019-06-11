<?php

require_once "Query.php";

// por enquanto apenas para funções
function analisar($texto, $id_fonte, $id_destino)
{
	$q = new Query();

	$array = explode(' ', $texto);
	foreach ($array as $word) 
		echo "$word";

	echo "\n---------------------------------\n";


	//pega os tipos primitivos na lingugagem fonte
	$tipos_f = $q->select("tipo", "tipos", "id_linguagem = ?", [$id_fonte]);

	//pega os tipos primitivos na linguagem destino
	$tipos_d = $q->select("tipo", "tipos", "id_linguagem = ?", [$id_destino]);

	var_dump($tipos);

	return $q->select("*", "linguagens", "id = ?", [$id_fonte]);
}