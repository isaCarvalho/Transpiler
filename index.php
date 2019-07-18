<?php
	include_once "header.html";
?>

<script src="js/limpar.js"></script>

<body class="body">
	<nav class="selecao">
		<form id="formulario">			
			
			<p>
				<label for="fonte" class="label">Origem</label>
				<select class="select" name="lfonte">
					<option value="1">C</option>
					<option value="2">Java</option>
					<option value="3">Kotlin</option>
					<option value="4">Python</option>
					<option value="5">Haskell</option>
				</select>
				
	
				<label for="destino" class="label">Destino</label>
				<select class="select" name="ldestino">
					<option value="1">C</option>
					<option value="2">Java</option>
					<option value="3">Kotlin</option>
					<option value="4">Python</option>
					<option value="5">Haskell</option>
				</select>
			</p>
			<p>
				<textarea class="text" placeholder="Código na linguagem de origem..." name="cfonte"></textarea>
				<textarea class="text" placeholder="Código na linguagem de destino..." name="cdestino" id="cDestino"></textarea>
			</p>

			<p>
				<input type="button" name="limpar" value="Limpar" class="button" onclick="limparCampos()">
				<input type="submit" name="transpilar" value="Transpilar" class="button">	
			</p>
		</form>
	</nav>
	
</body>

<script src="js/carregaResultado.js"></script>

<?php
	include_once "footer.html";