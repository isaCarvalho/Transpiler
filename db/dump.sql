/** 
Isabela S. de Carvalho
Banco de dados do GoTo Transpiler
*/

/* Criação da Base de dados */

create database transpiler;

use transpiler;

/* Criação das Tabelas */

-- Tabela que guarda os paradigmas das linguagens de programação
create table paradigmas (
id int primary key auto_increment,
nome varchar(255) not null
);

-- Tabela que guarda as linguagens e possui chave estrangeira para seu respectivo paradigma
create table linguagens (
id int primary key auto_increment,
nome varchar(255) not null,
id_paradigma int,
constraint fk_linguagens_id_paradigma foreign key (id_paradigma) references paradigmas(id) on delete cascade on update cascade
);

-- Tabela que guarda o BNF dos ifs e suas respectivas linguagens
create table ifs ( 
id int primary key auto_increment,
descricao varchar(255) not null,
id_linguagem int not null,
constraint fk_ifs_id_linguagens foreign key (id_linguagem) references linguagens(id) on delete cascade on update cascade
);

-- Tabela que guarda o BNF de funções e suas respectivas linguagens
create table functions (
id int primary key auto_increment, 
descricao varchar(255) not null,
id_linguagem int not null, 
constraint fk_functions_id_linguagens foreign key (id_linguagem) references linguagens(id) on delete cascade on update cascade
);

-- Tabela que guarda informações sobre os tipos primitivos e suas respectivas linguagens
create table tipos (
id int primary key auto_increment,
tipo varchar(255) not null, 
descricao varchar(255) default 'não informado',
tamanho int default 0,
id_linguagem int not null,
constraint fk_tipos_id_linguagens foreign key (id_linguagem) references linguagens(id) on delete cascade on update cascade
);

-- Tabela que guarda o BNF de loops e suas respectivas linguagens
create table loops (
id int primary key auto_increment,
descricao varchar(500) not null, 
id_linguagem int not null,
constraint fk_loops_id_linguagens foreign key (id_linguagem) references linguagens(id) on delete cascade on update cascade
);

-- Tabela de legendas
create table legendas (
id int primary key auto_increment,
nome varchar(255) not null,
descricao varchar(500)
);

/* Inserção de dados nas tabelas */

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
('if (<exp>) { <commands> }', 1), 
('if (<exp>) { <commands> }', 2), 
('if (<exp>) { <commands> }', 3), 
('if <exp>: <commands>', 4), 
('<exp> = <commands>', 5);

-- Inserção de Funções
insert into functions (descricao, id_linguagem) values  
('<tipo> <nome> (<tipo> <param>, ...) { <commands> }', 1), 
('public <tipo> <nome> (<tipo> <param>, ...) { <commands> }', 2), 
('fun <nome> (<param> : <tipo>, ...) : <tipo> { <commands> }', 3), 
('def <nome>(<param>, ...): <commands>', 4), 
('<nome> <param> ... = <commands>', 5);

-- Inserção de tipos primitivos
insert into tipos (tipo, descricao, tamanho, id_linguagem) values
('char', 'Caracter', 8, 1),
('unsigned char', 'Caracter', 8, 1),
('short int', 'Inteiro', 16, 1),
('int', 'Inteiro', 32, 1),
('long int', 'Inteiro', 64, 1),
('float', 'Ponto flutuante', 32, 1),
('double', 'Ponto flutuante', 64, 1);

insert into tipos (tipo, id_linguagem) values
('void', 1);

insert into tipos (tipo, descricao, tamanho, id_linguagem) values
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
('Byte', 'Inteiro', 8, 3);

insert into tipos (tipo, id_linguagem) values
('Char', 3),
('Boolean', 3),
('Unit', 3);

insert into tipos (tipo, descricao, id_linguagem) values
('int', 'Inteiro', 4),
('float', 'Ponto flutuante', 4),
('bool', 'Booleano', 4),
('str', 'Cadeia de caracteres', 4),
('Char', 'Caracter', 5),
('Bool', 'Booleano', 5),
('Double', 'Ponto flutuante', 5),
('Float', 'Ponto flutuante', 5),
('Integer', 'Inteiro', 5),
('Int', 'Inteiro', 5),
('String', 'Cadeia de caracteres', 5);

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
('<inicio>', 'Limite inferior - inicio de um loop'),
('<fim>', 'Limite superior - fim de um loop'),
('<nome>', 'Nome de função/variável');

-- Inserção de loops 

insert into loops (descricao, id_linguagem) values
('for (<tipo> <var> = <inicio>; <var> <cond> <fim>; <incr>) { <commands> }', 1),
('while (<exp>) { <commands> } ', 1),
('do { <commands> } while (<exp>)', 1),
('for (<tipo> <var> = <inicio>; <var> <cond> <fim>; <incr>) { <commands> }', 2),
('for (<tipo> <var>: <collections>)', 2),
('while (<exp>) { <commands> }', 2),
('do { <commands> } while (<exp>)', 2),
('for (<var> : <tipo> in <collections>) { <commands> }', 3),
('for (<var> : <tipo> in <inicio>..<fim>)', 3),
('while (<exp>) { <commands> }', 3),
('do { <commands> } while (exp)', 3),
('for <var> in <collections>: <commands>', 4),
('for <var> in range(<inicio>, <fim>, <step>): <commands>', 4),
('while <exp>: <commands>', 4),
('não informado', 5);

/* Consultas */

select linguagens.nome, paradigmas.nome from linguagens, paradigmas where linguagens.id_paradigma = paradigmas.id;

select nome, descricao from linguagens, ifs where linguagens.id = ifs.id_linguagem;

select linguagens.nome, functions.descricao from linguagens, functions where linguagens.id = functions.id_linguagem;

select linguagens.nome, tipos.tipo, tipos.descricao, tipos.tamanho from linguagens, tipos where linguagens.id = tipos.id_linguagem;

select linguagens.nome, loops.descricao from linguagens, loops where linguagens.id = loops.id_linguagem;
