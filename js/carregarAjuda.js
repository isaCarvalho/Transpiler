// LEMBRAR DE CORRIGIR ISSO AQUI

let body = document.querySelector('body')

let nav = document.querySelector('nav')

function carregarLegendas()
{
	fetch(`../control/controler.php?action=carregarLegendas`)
		.then(response => response.json())
		.then(legendas => {
			let table = document.createElement('table')
			table.innerHTML = `<tr>
									<th>Nome</th>
									<th>Descrição</th>
								</tr>`;

			legendas.forEach(legenda => {
				let tr = createElement('tr');
				tr.innerHTML = `<td>${legenda.nome}</td>
								<td>${legenda.descricao}</td>`;
			
				table.appendChild(tr);

			});

			nav.appendChild(table);
		})
}