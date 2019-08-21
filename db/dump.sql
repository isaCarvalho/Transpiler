/** 
Isabela S. de Carvalho
Script para a base de dados */
-- USE COLLATE = UTF8

-- DROP DATABASE transpiler;

CREATE DATABASE transpiler
    WITH 
    OWNER = postgres
    ENCODING = 'UTF8'
    LC_COLLATE = 'pt_BR.UTF-8'
    LC_CTYPE = 'pt_BR.UTF-8'
    TABLESPACE = pg_default
    CONNECTION LIMIT = -1;

COMMENT ON DATABASE transpiler
    IS 'Base de Dados para o GoTo Transpiler';

/* Criação das tabelas */

--- Tabela paradigmas
drop table if exists paradigmas;
drop sequence if exists paradigmas_seq;

create table paradigmas (
id serial primary key,
nome varchar(255) not null
);

create sequence paradigmas_seq increment 1 minvalue 1 start 1;
alter table paradigmas alter column id set default nextval('paradigmas_seq');

-- Tabela linguagens
drop table if exists linguagens;
drop sequence if exists linguagens_seq;

create table linguagens (
id int primary key,
nome varchar(255) not null,
descricao varchar(10000000),
documentacao varchar(1000),
id_paradigma int references paradigmas(id) on delete cascade on update cascade
);

create sequence linguagens_seq increment 1 minvalue 1 start 1;
alter table linguagens alter column id set default nextval('linguagens_seq');

-- Tabela ifs
drop table if exists ifs;
drop sequence if exists ifs_seq;

create table ifs (
id int primary key,
descricao varchar(255) not null,
id_linguagem int not null references linguagens(id) on delete cascade on update cascade
);

create sequence ifs_seq increment 1 minvalue 1 start 1;
alter table ifs alter column id set default nextval('ifs_seq');

-- Tabela functions
drop table if exists functions;
drop sequence if exists functions_seq;

create table functions (
id int primary key,
descricao varchar(255) not null,
id_linguagem int not null references linguagens(id) on delete cascade on update cascade
);

create sequence functions_seq increment 1 minvalue 1 start 1;
alter table functions alter column id set default nextval('functions_seq');

-- Tabela tipos
drop table if exists tipos;
drop sequence if exists tipos_seq;

create table tipos (
id int primary key,
tipo varchar(255) not null,
descricao varchar(255) default 'não informado',
tamanho int default 0,
id_linguagem int not null references linguagens(id) on delete cascade on update cascade
);

create sequence tipos_seq increment 1 minvalue 1 start 1;
alter table tipos alter column id set default nextval('tipos_seq');

-- Tabela loops
drop table if exists loops;
drop sequence if exists loops_seq;

create table loops (
id int primary key,
descricao varchar(500) not null,
id_linguagem int not null references linguagens(id) on delete cascade on update cascade
);

create sequence loops_seq increment 1 minvalue 1 start 1;
alter table loops alter column id set default nextval('loops_seq');

-- Tabela legendas
drop table if exists legendas;
drop sequence if exists legendas_seq;

create table legendas (
id int primary key,
nome varchar(255) not null,
descricao varchar(500)
);

create sequence legendas_seq increment 1 minvalue 1 start 1;
alter table legendas alter column id set default nextval('legendas_seq');

-- Tabela de declaracoes
drop table if exists declaracoes;
drop sequence if exists declaracoes_seq;

create table declaracoes (
id int primary key,
descricao varchar(255) not null,
id_linguagem int references linguagens(id)
);

create sequence declaracoes_seq increment 1 minvalue 1 start 1;
alter table declaracoes alter column id set default nextval('declaracoes_seq');

-- Tabela de returns
drop table if exists returns;
drop sequence if exists returns_seq;

create table returns(
id int primary key,
descricao varchar(255) not null,
id_linguagem int references linguagens(id)
);

create sequence returns_seq increment 1 minvalue 1 start 1;
alter table returns alter column id set default nextval('returns_seq');

-- Tabela de elses
drop table if exists elses;
drop sequence if exists elses_seq;

create table elses (
id int primary key,
descricao varchar(255) not null,
id_linguagem int references linguagens(id)
);

create sequence elses_seq increment 1 minvalue 1 start 1;
alter table elses alter column id set default nextval('elses_seq');

drop table if exists else_ifs;
drop sequence if exists else_ifs_seq;

create table else_ifs (
id serial primary key,
descricao varchar(255) not null,
id_linguagem int references linguagens(id)
);

create sequence else_ifs_seq increment 1 minvalue 1 start 1;
alter table else_ifs alter column id set default nextval('else_ifs_seq');

/* Inserção de dados nas tabelas */

-- Inserção dos paradigmas
insert into paradigmas (nome) values
('Funcional'),
('Procedural'),
('Orientacao a Objetos');

-- Inserção de linguagens
insert into linguagens (nome, documentacao, id_paradigma) values
('C','http://www.cplusplus.com/', 2),
('Java','https://www.oracle.com/technetwork/java/index.html', 3),
('Kotlin','https://kotlinlang.org/docs/reference/', 3),
('Python 3','https://docs.python.org/3/', 3),
('Haskell','https://www.haskell.org/documentation/', 1);

-- Inserção dos retornos
insert into returns (descricao, id_linguagem) values
('return <valor>;', 1),
('return <valor>;', 2),
('return <valor>', 3),
('return <valor>', 4),
('= <valor>', 5);

-- Inserção de declarações
insert into declaracoes (descricao, id_linguagem) values
('<tipo> <nome> = <valor>;', 1),
('<tipo> <nome> = <valor>;', 2),
('<nome> : <tipo> = <valor>', 3),
('<nome> = <valor>', 4),
('let <nome> = <valor>', 5);

-- Inserção de IFs
insert into ifs (descricao, id_linguagem) values
('if (<exp>) {', 1),
('if (<exp>) {', 2),
('if (<exp>) {', 3),
('if <exp>:', 4),
('| <exp> ', 5);

-- Inserção de elses
insert into elses (descricao, id_linguagem) values
('else {', 1),
('else {', 2),
('else {', 3),
('else:', 4),
('| otherwise ', 5);

insert into else_ifs (descricao, id_linguagem) values
('else if (<exp>) {', 1),
('else if (<exp>) {', 2),
('else if (<exp>) {', 3),
('elif <exp>:', 4),
('| <exp> ', 5);

-- Inserção de Funções
insert into functions (descricao, id_linguagem) values
('<tipo> <nome> (<param>) {', 1),
('public <tipo> <nome> (<param>) {', 2),
('fun <nome> (<param>) : <tipo> {', 3),
('def <nome>(<param>):', 4),
('<nome> <param> ', 5);

-- Inserção de tipos primitivos
insert into tipos (tipo, descricao, tamanho, id_linguagem) values
('char', 'Caracter', 8, 1),
('unsigned char', 'Caracter', 8, 1),
('short int', 'Inteiro', 16, 1),
('int', 'Inteiro', 32, 1),
('long int', 'Inteiro', 64, 1),
('float', 'Ponto flutuante', 32, 1),
('double', 'Ponto flutuante', 64, 1),
('void', 'Vazio', 0, 1),
('byte', 'Inteiro', 8, 2),
('short', 'Inteiro', 16, 2),
('int', 'Inteiro', 32, 2),
('long', 'Inteiro', 64, 2),
('float', 'Ponto flutuante', 32, 2),
('double', 'Ponto flutuante', 64, 2),
('char', 'Caracter', 16, 2),
('boolean', 'Booleano', 1, 2),
('Double', 'Ponto flutuante', 64, 3),
('Float', 'Ponto flutuante', 32, 3),
('Long', 'Inteiro', 64, 3),
('Int', 'Inteiro', 32, 3),
('Short', 'Inteiro', 16, 3),
('Byte', 'Inteiro', 8, 3),
('Char', 'Caracter', 16, 3),
('Boolean', 'Booleano', 1, 3),
('Unit', 'Vazio', 0, 3),
('int', 'Inteiro', 32, 4),
('float', 'Ponto flutuante', 32, 4),
('bool', 'Booleano', 1, 4),
('str', 'Caractere',16,  4),
('Char', 'Caracter',16, 5),
('Bool', 'Booleano',1,  5),
('Double', 'Ponto flutuante',64, 5),
('Float', 'Ponto flutuante',32, 5),
('Int', 'Inteiro',32, 5),
('String', 'Caracter',16, 5);

-- Inserção de legendas

insert into legendas (nome, descricao) values
('<commands>', 'Bloco de comandos'),
('<var>', 'Variável'),
('<exp>', 'Expressão condicional'),
('<param>', 'Parametros'),
('<collections>', 'Coleções de Dados'),
('<step>', 'Passo do loop'),
('<tipo>', 'Tipo de dados'),
('<incr>', 'Incremento'),
('<decr>', 'Decremento'),
('<cond>', 'Condição'),
('<inicio>', 'Limite inferior ou inicio de um loop'),
('<fim>', 'Limite superior ou fim de um loop'),
('<nome>', 'Nome de função ou variável');

-- Inserção de loops

insert into loops (descricao, id_linguagem) values
('for (<tipo> <var> = <inicio>; <var> <cond> <fim>; <var><incr>) {', 1),
('while (<exp>)', 1),
('do {', 1),
('for (<tipo> <var> = <inicio>; <var> <cond> <fim>; <var><incr>) {', 2),
('for (<tipo> <var>: <collections>) {', 2),
('while (<exp>)', 2),
('do {', 2),
('for (<var> : <tipo> in <collections>) {', 3),
('for (<var> : <tipo> in <inicio>..<fim>) {', 3),
('while (<exp>)', 3),
('do {', 3),
('for <var> in <collections>:', 4),
('for <var> in range(<inicio>, <fim>, <step>):', 4),
('while <exp>:', 4),
('não informado', 5);

/* Modificações */

update linguagens set descricao = 'C é uma linguagem de programação de paradigma procedural, estruturada e que serviu de ' ||
 'base para várias outras linguagens de programação de alto nível tais como C++, C#, Java, Pyhton, dentre outras. ' ||
  'Apesar do surgimento de linguagens mais novas para diferentes propósitos (JavaScript, para desenvolvimento de web, ' ||
   'por exemplo), C ainda é amplamente utilizada, como nos sistemas Unix (Linux, Mac, etc). A linguagem C foi ' ||
    'desenvolvida no ano de 1972 por Dennis Ritchie, que é conhecido como fundador da linguagem.' where id = 1;
update linguagens set descricao = 'A linguagem Java foi desenvolvida na Sun Microsystems sob a liderança de Bill Joy e ' ||
 'James Gosling, na década de 1990. Dos membros originais de um pequeno time de programadores em Aspen, James Goslin ' ||
  'é o que será para sempre reconhecido como pai de Java. No ano de 1993, com a explosão da Internet, e em ' ||
   'particular da World Wide Web, surgia a necessidade de uma linguagem robusta, de arquitetura independente e ' ||
    'orientada a objetos. Desta forma, nasceu Java. Em 2009, a Sun Microsystems foi comprada pela Oracle, ' ||
     'que é a atual proprietária da linguagem Java. Por ser uma linguagem portável e multiplataforma, Java é ' ||
      'utilizada na web e é uma das linguagens oficiais do sistema operacional Android, da Google.' where id = 2;
update linguagens set descricao = 'Kotlin é uma linguagem recente, de propósito geral, orientada a objetos, ' ||
 'desenvolvida pela JetBrains para interoperar completamente com Java. Um dos seus princípios é Null Safety, a ' ||
  'fim de eliminar NullPointerException do Java. O código em Kotlin é compilado e executado pela JVM (Java Virtual ' ||
   'Machine), e ela foi projetada para ser uma linguagem com uma sintaxe mais simples. Em 2011, a JetBrains ' ||
    'anunciou o projeto Kotlin. Seu nome foi inspirado pela Ilha de Kotlin, localizada no Golfo da Finlândia, ' ||
     'próximo aos escritórios da JetBrains. Em 2017, no Google I/O, foi anunciada a parceira com a JetBrains ' ||
      'para incorporar Kotlin como linguagem oficial do Android. Alguns aplicativos Android desenvolvidos em ' ||
       'Kotlin são: Evernote, Airbnb, Netflix, Pinterest, Adobe Reader, Twitter, dentre outros.' where id = 3;
update linguagens set descricao = 'A linguagem Python teve sua implementação iniciada em dezembro de 1989, ' ||
 'por Guido Van Rossum. Ele é o principal autor da linguagem e publicou o código para a alt.sources em fevereiro ' ||
  'de 1991. O nome "Python" foi inspirado pela série de televisão da BBC "Monty Python''s Flying Circus". ' ||
   'Ela é uma linguagem orientada a objetos e caracterizada por sua sintaxe simples. A versão 2.0 de Pyhton ' ||
    'foi lançada em outubro de 2000, e já contava com recursos como coletor de lixo (além da contagem de ' ||
     'referencia). Sua versão 3.0 foi lançada em dezembro de 2008, com muitas modificações incompatíveis ' ||
      'com a versão 2.0. Atualmente a linguagem está em sua versão 3.9 em desenvolvimento. *GoTo - ' ||
       'Transpiler transpila códigos escritos em Pyhton 3.' where id = 4;
update linguagens set descricao = 'Haskell é uma linguagem de programação elaborada em 1987, ' ||
 'de paradigma puramente funcional, estaticamente tipada, preguiçosa - isto é, haskell só executará funções e ' ||
  'calculos quando for forçado a mostrar resultados. Além disso, alguns princípios como ausência de efeitos ' ||
   'colaterais são incorporados em Haskell. A linguagem foi nomeada por Haskell Brooks Curry. Haskell é baseada ' ||
    'no cálculo lambda, por esta razão, seu logotipo é um lambda.' where id = 5;

/* Consultas */

select linguagens.nome, paradigmas.nome from linguagens, paradigmas where linguagens.id_paradigma = paradigmas.id;

select nome, descricao from linguagens, ifs where linguagens.id = ifs.id_linguagem;

select linguagens.nome, functions.descricao from linguagens, functions where linguagens.id = functions.id_linguagem;

select linguagens.nome, tipos.tipo, tipos.descricao, tipos.tamanho from linguagens, tipos where linguagens.id = tipos.id_linguagem;

select linguagens.nome, loops.descricao from linguagens, loops where linguagens.id = loops.id_linguagem;
