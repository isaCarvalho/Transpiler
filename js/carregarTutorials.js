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
	carregarDados(id);
	nav.appendChild(navLing);
}

function createTable(inner = ``)
{
	let table = document.createElement('table');
	table.innerHTML = inner;

	return table;
}

function carregarDados(id)
{
	fetch(`../control/?action=API&id=${id}`)
		.then(response => response.json())
		.then(dados => {

			// Primeiras informacoes
			let tableInfo = createTable(`<tr>
												<th>ID</th>
												<th>Nome</th>
												<th>Paradigma</th>
											</tr>`);
			let trInfo = document.createElement('tr');
			trInfo.innerHTML = `<td>${dados.id}</td>
								<td>${dados.nome}</td>
								<td>${dados.paradigma}</td>`;
			tableInfo.appendChild(trInfo);
			navLing.appendChild(tableInfo);

			// Descricao da Linguagem
			let tableDesc = createTable(`<tr><th>Descricao</th></tr>`);
			let trDesc = document.createElement('tr');
			trDesc.innerHTML = `<td>${dados.descricao}. Documentação: <a href="${dados.documentacao}">${dados.documentacao}</a></td>`;
			tableDesc.appendChild(trDesc);
			navLing.appendChild(tableDesc);

			let tableTipos = createTable(`<tr>
										<th>Tipo</th>
									  	<th>Descrição</th>
									  	<th>Tamanho</th>
							  		</tr>`);

			dados.tipos.forEach(tipo => {
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

			dados.loops.forEach(loop => {
				let trLoop = document.createElement('tr');
				trLoop.innerHTML = `<td>${loop.descricao}</td>`;

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
			trCond.innerHTML = `<td>${dados.if}</td>
								<td>${dados.else}</td>
								<td>${dados.elseif}</td>`;
			tableCond.appendChild(trCond);
			navLing.appendChild(tableCond);

			// Constroi a tabela de bnf de funcao
			let tableFunc = createTable(`<tr><th>Função</th></tr>`);
			let trFunc = document.createElement('tr');
			trFunc.innerHTML = `<td>${dados.funcao}</td>`;
			tableFunc.appendChild(trFunc);
			navLing.appendChild(tableFunc);

			// Constroi a tabela de bnf de impressoes
			let tableImp = createTable(`<tr><th>Impressão de dados na tela</th></tr>`);
			let trImp = document.createElement('tr');
			trImp.innerHTML = `<td>${dados.impressao}</td>`;
			tableImp.appendChild(trImp);
			navLing.appendChild(tableImp);

			// Constroi a tabela de bnf de retornos
			let tableRet = createTable(`<tr><th>Comando de retorno</th></tr>`);
			let trRet = document.createElement('tr');
			trRet.innerHTML = `<td>${dados.retorno}</td>`;
			tableRet.appendChild(trRet);
			navLing.appendChild(tableRet);

			// tabela de declaracoes
			let tableDec = createTable(`<tr><th>Declaracao</th></tr>`);
			let tr = document.createElement('tr');
			tr.innerHTML = `<td>${dados.declaracao}</td>`;
			tableDec.appendChild(tr);
			navLing.appendChild(tableDec);
		})
}