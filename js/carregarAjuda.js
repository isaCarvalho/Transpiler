let body = document.querySelector('body')

let nav = document.getElementById('ajuda')

console.log(nav)

function carregarLegendas()
{
	fetch(`../files/legendas.json`)
		.then(response => response.json())
		.then(legendas => {
			let table = document.createElement('table')
			table.innerHTML = `<tr>
									<th>Nome</th>
									<th>Descrição</th>
								</tr>`;

			legendas.forEach(legenda => {
				let tr = document.createElement('tr');
				tr.innerHTML = `<td>${legenda.nome}</td>
								<td>${legenda.descricao}</td>`;
			
				table.appendChild(tr);

			});

			nav.appendChild(table);
		})
}