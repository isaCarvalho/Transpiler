<?php
	include_once "header.html";
?>

<body class="body">
	<nav id="form">
		<form>
			<p>
				<label for="linguagem">Linguagem de destino</label>
				<select class="select" name="linguagem">
					<option>Python</option>
					<option>Java</option>
					<option>Kotlin</option>
				</select>
			</p>
			<p>
				<textarea class="text" placeholder="Código em C..."></textarea>
				<textarea class="text" placeholder="Código na linguagem de destino..."></textarea>
			</p>

			<p>
				<input type="button" name="limpar" value="Limpar" class="button">
				<input type="submit" name="enviar" value="Enviar" class="button">	
			</p>
		</form>
	</nav>
	
</body>

<?php
	include_once "footer.html";