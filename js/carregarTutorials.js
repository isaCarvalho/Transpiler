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
	fetch("../control/?action=carregarLinguagens")
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
	carregarPrints(id);
	carregarReturns(id);

	nav.appendChild(navLing);
}

function createTable(inner = ``)
{
	let table = document.createElement('table');
	table.innerHTML = inner;

	return table;
}

function carregarDados(url, title)
{
	fetch(url)
		.then(response => response.json())
		.then(dado => {
			let table = createTable(`<tr><th>${title}</th></tr>`);

			let tr = document.createElement('tr');
			tr.innerHTML = `<td>${dado.descricao}</td>`;

			table.appendChild(tr);

			navLing.appendChild(table);
		})
}

// carrega o BNF de prints em cada linguagem
function carregarPrints(id)
{
	carregarDados(`../control/?action=carregarPrints&id_linguagem=${id}`, "Prints");
}

// carrega o BNF de retornos em cada linguagem
function carregarReturns(id)
{
	carregarDados(`../control/?action=carregarReturns&id_linguagem=${id}`, "Returns");
}

// carrega o BNF das funcoes em cada linguagem
function carregarFunctions(id)
{
	carregarDados(`../control/?action=carregarFunctions&id_linguagem=${id}`, "Funções")
}

// Carrega os tipos primitivos de cada linguagem
function carregarTipos(id)
{
	fetch(`../control/?action=carregarTipos&id_linguagem=${id}`)
		.then(response => response.json())
		.then(tipos => {

			let tableTipos = createTable(`<tr>
										<th>Tipo</th>
									  	<th>Descrição</th>
									  	<th>Tamanho</th>
							  		</tr>`)

			tipos.forEach(tipo => {
				let tr = document.createElement('tr');

				let tamanho;
				if (tipo.tamanho === 0)
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
	carregarDados(`../control/?action=carregarIfs&id_linguagem=${id}`, "Expressões Condicionais");
}

// carrega os loops de cada linguagem
function carregarLoops(id)
{
	fetch(`../control/?action=carregarLoops&id_linguagem=${id}`)
		.then(response => response.json())
		.then(loops => {

			let tableLoops = createTable(`<tr>
										<th>Laços de Repetição</th>
									</tr>`);

			loops.forEach(loop => {
				let tr = document.createElement('tr');
				tr.innerHTML = `<td>${loop.descricao}</td>`;

				tableLoops.appendChild(tr)
			});

			navLing.appendChild(tableLoops);
		})
}

function carregarDeclaracoes(id)
{
	carregarDados(`../control/?action=carregarDeclaracoes&id_linguagem=${id}`, "Declaração");
}

function carregarInformacoes(id)
{
	fetch(`../control/?action=carregarInformacoes&id_linguagem=${id}`)
		.then(response => response.json())
		.then(informacoes => {

			let tableDesc = createTable(`<tr><th>A linguagem ${informacoes.nome}</th></tr>
				<tr><td class="desc">${informacoes.descricao}
				Documentação: <a href="${informacoes.documentacao}" target="_blank">${informacoes.documentacao}</a></td></tr>`);

			navLing.appendChild(tableDesc);
		})
}