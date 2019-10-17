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
	fetch("../files/linguagens.json")
		.then(response => response.json())
		.then(linguagens => {
			linguagens = JSON.parse(linguagens);

			let p = document.createElement('p');
			 p.innerHTML = `<label for="linguagem" class="label">Selecione a linguagem </label>`;

			let select = document.createElement('select');
			select.className = "selectT";
			select.name = "linguagem";
			select.id = "linguagem";

			linguagens.forEach(linguagem => {
				// faz o parse da linguagem para o objeto
				linguagem = JSON.parse(linguagem);

				let option = document.createElement('option');
				option.innerHTML = `${linguagem.nome}`;
				option.value=`${linguagem.id}`;

				select.appendChild(option)
			});

			select.addEventListener('click', event => {
				carregarTexto(JSON.parse(linguagens[select.selectedIndex]));
			});

			p.appendChild(select);

			nav.appendChild(p);
		})
}

// Chama a funcao de acordo com o texto de cada linguagem de acordo com o id da linguagem
function carregarTexto(id)
{
	navLing.innerHTML = '';
	carregarDados(id);
	nav.appendChild(navLing);
}

function createTable(inner = ``)
{
	let table = document.createElement('table');
	table.innerHTML = inner;

	return table;
}

function carregarDados(ling)
{
	// Primeiras informacoes
	let tableInfo = createTable(`<tr>
										<th>ID</th>
										<th>Nome</th>
										<th>Paradigma</th>
									</tr>`);
	let trInfo = document.createElement('tr');
	trInfo.innerHTML = `<td>${ling.id}</td>
						<td>${ling.nome}</td>
						<td>${ling.paradigma}</td>`;
	tableInfo.appendChild(trInfo);
	navLing.appendChild(tableInfo);

	// Descricao da Linguagem
	let tableDesc = createTable(`<tr><th>Descricao</th></tr>`);
	let trDesc = document.createElement('tr');
	trDesc.innerHTML = `<td>${ling.descricao}. Documentação: <a href="${ling.documentacao}" target="_blank">${ling.documentacao}</a></td>`;
	tableDesc.appendChild(trDesc);
	navLing.appendChild(tableDesc);

	let tableTipos = createTable(`<tr>
								<th>Tipo</th>
								<th>Descrição</th>
								<th>Tamanho</th>
							</tr>`);

	ling.tipos.forEach(tipo => {
		let trTipo = document.createElement('tr');

		let tamanho;
		if (tipo.tamanho === 0)
			tamanho = 'não informado';
		else
			tamanho = tipo.tamanho + ' bits';

		trTipo.innerHTML = `<td>${tipo.tipo}</td>
						<td>${tipo.descricao}</td>
						<td>${tamanho}</td>
						`;

		tableTipos.appendChild(trTipo);
	});
	navLing.appendChild(tableTipos);

	let tableLoops = createTable(`<tr>
								<th>Laços de Repetição</th>
							</tr>`);

	ling.loops.forEach(loop => {
		let trLoop = document.createElement('tr');
		trLoop.innerHTML = `<td>${loop.descricao.replace(['<', '>'], ['&lt', '&gt'])}</td>`;

		tableLoops.appendChild(trLoop)
	});

	navLing.appendChild(tableLoops);

	// Constroi a tabela de condicionais
	let tableCond = createTable(`<tr>
									<th>If</th>
									<th>Else</th>
									<th>Else If</th>
								</tr>`);
	let trCond = document.createElement('tr');
	trCond.innerHTML = `<td>${ling.if.replace(['<', '>'], ['&lt', '&gt'])}</td>
						<td>${ling.else.replace(['<', '>'], ['&lt', '&gt'])}</td>
						<td>${ling.elseif.replace(['<', '>'], ['&lt', '&gt'])}</td>`;
	tableCond.appendChild(trCond);
	navLing.appendChild(tableCond);

	// Constroi a tabela de bnf de funcao
	let tableFunc = createTable(`<tr><th>Função</th></tr>`);
	let trFunc = document.createElement('tr');
	trFunc.innerHTML = `<td>${ling.funcao.replace(['<', '>'], ['&lt', '&gt'])}</td>`;
	tableFunc.appendChild(trFunc);
	navLing.appendChild(tableFunc);

	// Constroi a tabela de bnf de impressoes
	let tableImp = createTable(`<tr><th>Impressão de ling na tela</th></tr>`);
	let trImp = document.createElement('tr');
	trImp.innerHTML = `<td>${ling.impressao.replace(['<', '>'], ['&lt', '&gt'])}</td>`;
	tableImp.appendChild(trImp);
	navLing.appendChild(tableImp);

	// Constroi a tabela de bnf de retornos
	let tableRet = createTable(`<tr><th>Comando de retorno</th></tr>`);
	let trRet = document.createElement('tr');
	trRet.innerHTML = `<td>${ling.retorno.replace(['<', '>'], ['&lt', '&gt'])}</td>`;
	tableRet.appendChild(trRet);
	navLing.appendChild(tableRet);

	// tabela de declaracoes
	let tableDec = createTable(`<tr><th>Declaracao</th></tr>`);
	let tr = document.createElement('tr');
	tr.innerHTML = `<td>${ling.declaracao.replace(['<', '>'], ['&lt', '&gt'])}</td>`;
	tableDec.appendChild(tr);
	navLing.appendChild(tableDec);
}