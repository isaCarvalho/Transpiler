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

-- Tabela paradigmas
create table paradigmas (
id serial primary key,
nome varchar(255) not null
);

create sequence paradigmas_seq increment 1 minvalue 1 start 1;
alter table paradigmas alter column id set default nextval('paradigmas_seq');

-- Tabela linguagens
create table linguagens (
id int primary key,
nome varchar(255) not null,
id_paradigma int references paradigmas(id) on delete cascade on update cascade
);

create sequence linguagens_seq increment 1 minvalue 1 start 1;
alter table linguagens alter column id set default nextval('linguagens_seq');

-- Tabela ifs
create table ifs ( 
id int primary key,
descricao varchar(255) not null,
id_linguagem int not null references linguagens(id) on delete cascade on update cascade
);

create sequence ifs_seq increment 1 minvalue 1 start 1;
alter table ifs alter column id set default nextval('ifs_seq');

-- Tabela functions
create table functions (
id int primary key, 
descricao varchar(255) not null,
id_linguagem int not null references linguagens(id) on delete cascade on update cascade
);

create sequence functions_seq increment 1 minvalue 1 start 1;
alter table functions alter column id set default nextval('functions_seq');

-- Tabela tipos
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
create table loops (
id int primary key,
descricao varchar(500) not null, 
id_linguagem int not null references linguagens(id) on delete cascade on update cascade
);

create sequence loops_seq increment 1 minvalue 1 start 1;
alter table loops alter column id set default nextval('loops_seq');

-- Tabela legendas
create table legendas (
id int primary key,
nome varchar(255) not null,
descricao varchar(500)
);

create sequence legendas_seq increment 1 minvalue 1 start 1;
alter table legendas alter column id set default nextval('legendas_seq');

-- Tabela de declaracoes
create table declaracoes (
id int primary key,
descricao varchar(255) not null,
id_linguagem int references linguagens(id)
);

create sequence declaracoes_seq increment 1 minvalue 1 start 1;
alter table declaracoes alter column id set default nextval('declaracoes_seq');

-- Tabela de returns
create table returns(
id int primary key,
descricao varchar(255) not null,
id_linguagem int references linguagens(id)
);

create sequence returns_seq increment 1 minvalue 1 start 1;
alter table returns alter column id set default nextval('returns_seq');

/* Inserção de dados nas tabelas */

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

-- Inserção dos paradigmas
insert into paradigmas (nome) values 
('Funcional'),
('Procedural'),
('Orientacao a Objetos');

-- Inserção de linguagens
insert into linguagens (nome, id_paradigma) values
('C', 2),
('Java', 3),
('Kotlin', 3),
('Python 3', 3),
('Haskell', 1);

-- Inserção de IFs
insert into ifs (descricao, id_linguagem) values 
('if (<exp>)', 1), 
('if (<exp>)', 2), 
('if (<exp>)', 3), 
('if <exp>:', 4), 
('| <exp> =', 5);

-- Inserção de Funções
insert into functions (descricao, id_linguagem) values  
('<tipo> <nome> (<param>)', 1), 
('public <tipo> <nome> (<param>)', 2), 
('fun <nome> (<param>) : <tipo>', 3), 
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
('for (<tipo> <var> = <inicio>; <var> <cond> <fim>; <incr>)', 1),
('while (<exp>)', 1),
('do { <commands> } while (<exp>)', 1),
('for (<tipo> <var> = <inicio>; <var> <cond> <fim>; <incr>)', 2),
('for (<tipo> <var>: <collections>)', 2),
('while (<exp>)', 2),
('do { <commands> } while (<exp>)', 2),
('for (<var> : <tipo> in <collections>)', 3),
('for (<var> : <tipo> in <inicio>..<fim>)', 3),
('while (<exp>)', 3),
('do { <commands> } while (exp)', 3),
('for <var> in <collections>:', 4),
('for <var> in range(<inicio>, <fim>, <step>):', 4),
('while <exp>:', 4),
('não informado', 5);

/* Consultas */

select linguagens.nome, paradigmas.nome from linguagens, paradigmas where linguagens.id_paradigma = paradigmas.id;

select nome, descricao from linguagens, ifs where linguagens.id = ifs.id_linguagem;

select linguagens.nome, functions.descricao from linguagens, functions where linguagens.id = functions.id_linguagem;

select linguagens.nome, tipos.tipo, tipos.descricao, tipos.tamanho from linguagens, tipos where linguagens.id = tipos.id_linguagem;

select linguagens.nome, loops.descricao from linguagens, loops where linguagens.id = loops.id_linguagem;
