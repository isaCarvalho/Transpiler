<?php
	include_once "header.html";
?>

<body class="body">
	<nav class="selecao">
		<form method="post" action="control/controler.php?action=enviarFonte">
			<p>
				<label for="fonte">Origem</label>
				<select class="select" name="lfonte">
					<option value="1">C</option>
					<option value="2">Java</option>
					<option value="3">Kotlin</option>
					<option value="4">Python</option>
					<option value="5">Haskell</option>
				</select>

				<label for="destino">Destino</label>
				<select class="select" name="ldestino">
					<option value="1">C</option>
					<option value="2">Java</option>
					<option value="3">Kotlin</option>
					<option value="4">Python</option>
					<option value="5">Haskell</option>
				</select>
			</p>
			<p>
				<textarea class="text" placeholder="Código em C..." name="cfonte"></textarea>
				<textarea class="text" placeholder="Código na linguagem de destino..." name="cdestino" id="cDestino"></textarea>
			</p>

			<p>
				<input type="button" name="limpar" value="Limpar" class="button">
				<input type="submit" name="enviar" value="Enviar" class="button">	
			</p>
		</form>
	</nav>
	
</body>

<script src="js/carregarCodigo.js"></script>>

<?php
	include_once "footer.html";