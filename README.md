# Goto - Transpiler

Este transpilador é o projeto da monitoria de Linguagens de Programação da UFF, no semestre de 2019.1
Ele foi pensado para traspilar pequenos trexos de códigos simples e ajudar na sintaxe básica de cada linguagem.

## Home

A página inicial é onde o usuário escreverá o código de origem, e selecionará a linguagem de origem e de destino.
A saída será este mesmo código, na linguagem de destino.

- Exemplo:

Código em C (origem):

`int soma (int n1, int n2) {
return n1+n2;
}`

Código transpilado em Kotlin (destino):

`fun soma (n1: Int, n2: Int) : Int { return n1+n2 }`

## Tutoriais

A página de tutoriais conta com:

- Um pequeno resumo da história de cada linguagem;
- Link para a sua documentação;
- BNF de ifs, elses, loops, funções, etc;

## API

Para utilizar a API: http://goto-transpiler.herokuapp.com/control/?action=API&id=ID_LINGUAGEM.
Susbtitua "ID_LINGUAGEM" pelo id da lingugagem desejada ou por um '*' para retornar os dados de todas as linguagens.

## Ajuda

A página de ajuda contém um pequeno manual dos simbolos usados nos BNFs das linguagens.

## Observações

- Ferramentas de desenvolvimento:

1. PHP 7.2
2. PostgreSQL 11

- Autoria

Isabela Carvalho 
