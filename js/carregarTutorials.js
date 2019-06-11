let body = document.querySelector("body");

function carregarSelect()
{
	fetch("../control/controler.php?action=carregarLinguagens")
		.then(response => response.json())
		.then(linguagens => {
			let nav = document.createElement('nav');
			nav.innerHTML = `<nav name="selecao" class="selecao" id="selecao">
							<p>                                               
								<label for="linguagem">Selecione a linguagem</label>
								<select class="selectT" name="linguagem">
								</select>
								<input type="button" class="search" value="Search" onclick="carregarTipos()">
							</p>
							</nav>`;

			linguagens.forEach(linguagem => {
				let option = document.createElement('option');
				option.innerHTML = `<option value="${linguagem.id}">${linguagem.nome}</option>`;

				nav.appendChild(option)
			});

			body.appendChild(nav);	
		})
}

function carregarTipos()
{
	fetch("../control/controler.php?action=carregarTipos")
		.then(response => response.json())
		.then(tipos => {
			let table = document.createElement('table');
			table.innerHTML = `<table name="tipos" id="tipos" class="tipos">
								<tr>
									<th>ID</th>
								  	<th>Descricao</th>
								  	<th>Tamanho</th>
							  	</tr>`;

			tipos.forEach(tipo => {
				let tr = document.createElement('tr');
				tr.innerHTML = ` <tr>
									<td>${tipo.id}</td>
									<td>${tipo.descricao}</td>
									<td>${tipo.tamanho}</td>
								</tr>
				`;

				table.appendChild(tr);
			});

			body.appendChild(table);
		})
}