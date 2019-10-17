<?php
	include_once "view/header.html";
?>

<!--LEMBRAR DE PROCURAR A BIBLIOTECA HIGHLIGHTING DO JAVASCRIPT-->

<script src="js/limpar.js"></script>

<body class="body">
	<nav class="home">
		<form id="formulario">			
			
			<p>
				<label for="lfonte" class="label">Origem</label>
				<select class="select" name="lfonte" id="lfonte">
					<option value="1">C</option>
					<option value="2">Java</option>
					<option value="3">Kotlin</option>
<!--					<option value="4">Python</option>-->
<!--					<option value="5">Haskell</option>-->
				</select>
				
	
				<label for="ldestino" class="label">Destino</label>
				<select class="select" name="ldestino" id="ldestino">
					<option value="1">C</option>
					<option value="2">Java</option>
					<option value="3">Kotlin</option>
					<option value="4">Python</option>
					<option value="5">Haskell</option>
				</select>
			</p>
			<p>
				<textarea class="text" placeholder="Código na linguagem de origem..." name="cfonte"></textarea>
                <pre name="cdestino"></pre>
			</p>

			<p>
				<input type="button" name="limpar" value="Limpar" class="button" onclick="limparCampos()">
				<input type="submit" id="buttonTranspilar" name="transpilar" value="Transpilar" class="button">
			</p>
		</form>
	</nav>
	
</body>

    <script src="js/carregarResultado.js"></script>
    <script>
        if ('serviceWorker' in navigator) {
            navigator.serviceWorker.register('sw.js')
                .then(function () {
                    console.log('service worker registered');
                    writeFile();
                })
                .catch(function () {
                    console.warn('service worker failed');
                });
        }
    </script>

    <script src="/sw.js"></script>
    <script src="/js/writeFile.js">writeFile()</script>

<?php
	include_once "view/footer.html";