// cria o elemento body
let body = document.querySelector("body");

// cria a nav que conterá o select e posteriormente as informações das linguagens
let nav = document.createElement('nav');
nav.className = 'selecao';
nav.name = 'selecao';

let navLing = document.createElement('nav');

// adiciona nav ao body
body.appendChild(nav);

// Carrega o componente select e o preenche com os dados vindos do banco de dados
function carregarSelect()
{
	fetch("../control/controler.php?action=carregarLinguagens")
		.then(response => response.json())
		.then(linguagens => {
			let p = document.createElement('p');
			 p.innerHTML = `<label for="linguagem" class="label">Selecione a linguagem </label>`;

			let select = document.createElement('select');
			select.className = "selectT";
			select.name = "linguagem";

			linguagens.forEach(linguagem => {
				let option = document.createElement('option');
				option.innerHTML = `${linguagem.nome}`;
				option.value=`${linguagem.id}`;

				select.appendChild(option)
			});

			select.addEventListener('click', event => {
				carregarTexto(select.selectedIndex+1);
			})

			p.appendChild(select);		

			nav.appendChild(p);
		})
}

// Chama a funcao de acordo com o texto de cada linguagem de acordo com o id da linguagem
function carregarTexto(id)
{
	navLing.innerHTML = '';

	carregarInformacoes(id);
	carregarFunctions(id);
	carregarTipos(id);
	carregarIfs(id);
	carregarLoops(id);
	carregarDeclaracoes(id);

	nav.appendChild(navLing);
}

function createTable(inner = ``)
{
	let table = document.createElement('table');
	table.innerHTML = inner;

	return table;
}

// carrega o BNF das funcoes em cada linguagem
function carregarFunctions(id)
{
	fetch(`../control/controler.php?action=carregarFunctions&id_linguagem=${id}`)
		.then(response => response.json())
		.then(functions => {

			let tableFunctions = createTable(`<tr><th>Funções</th></tr>`);

			var tr = document.createElement('tr');
			tr.innerHTML = `<td>${functions.descricao}</td>`;

			tableFunctions.appendChild(tr);

			navLing.appendChild(tableFunctions);
		})
}

// Carrega os tipos primitivos de cada linguagem
function carregarTipos(id)
{
	fetch(`../control/controler.php?action=carregarTipos&id_linguagem=${id}`)
		.then(response => response.json())
		.then(tipos => {

			let tableTipos = createTable(`<tr>
										<th>Tipo</th>
									  	<th>Descrição</th>
									  	<th>Tamanho</th>
							  		</tr>`)

			tipos.forEach(tipo => {
				var tr = document.createElement('tr');

				var tamanho;
				if (tipo.tamanho == 0)
					tamanho = 'não informado';
				else
					tamanho = tipo.tamanho + ' bits';

				tr.innerHTML = `<td>${tipo.tipo}</td>
								<td>${tipo.descricao}</td>
								<td>${tamanho}</td>
								`;

				tableTipos.appendChild(tr);
			});

			navLing.appendChild(tableTipos);
		})
}

// Carrega os ifs de cada linguagem
function carregarIfs(id)
{
	fetch(`../control/controler.php?action=carregarIfs&id_linguagem=${id}`)
		.then(response => response.json())
		.then(ifs => {

			let tableIfs = createTable(`<tr>
								  	<th>Expressões Condicionais</th>
							  	</tr>`);

			var tr = document.createElement('tr');
			tr.innerHTML = `<td>${ifs.descricao}</td>`;

			tableIfs.appendChild(tr);

			navLing.appendChild(tableIfs);
		})
}

// carrega os loops de cada linguagem
function carregarLoops(id)
{
	fetch(`../control/controler.php?action=carregarLoops&id_linguagem=${id}`)
		.then(response => response.json())
		.then(loops => {

			let tableLoops = createTable(`<tr>
										<th>Laços de Repetição</th>
									</tr>`);

			loops.forEach(loop => {
				var tr = document.createElement('tr');
				tr.innerHTML = `<td>${loop.descricao}</td>`;

				tableLoops.appendChild(tr)
			});

			navLing.appendChild(tableLoops);
		})
}

function carregarDeclaracoes(id)
{
	fetch(`../control/controler.php?action=carregarDeclaracoes&id_linguagem=${id}`)
		.then(response => response.json())
		.then(declaracoes => {

			let tableDecl = createTable(`<tr><th>Declaração</th></tr>`);

			let tr = document.createElement('tr');
			tr.innerHTML = `<td>${declaracoes.descricao}</td>`;

			tableDecl.appendChild(tr);

			navLing.appendChild(tableDecl);
		})
}

function carregarInformacoes(id)
{
	fetch(`../control/controler.php?action=carregarInformacoes&id_linguagem=${id}`)
		.then(response => response.json())
		.then(informacoes => {

			let tableDesc = createTable(`<tr><th>A linguagem ${informacoes.nome}</th></tr>
								   <tr><td class="desc">${informacoes.descricao}
								   Documentação: ${informacoes.documentacao}</td></tr>`);

			navLing.appendChild(tableDesc);
		})
}