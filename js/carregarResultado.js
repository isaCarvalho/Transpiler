let form = document.getElementById('formulario');

form.addEventListener("submit", (event) => {
    event.preventDefault()
    
    let body = new FormData(event.target)

    fetch('control/controler.php?action=enviarFonte', {method: "post", body})
    .then(response => response.json())
    .then(response => {
        let destino = document.querySelector("pre[name=cdestino]");
        console.log(response);
        
        destino.innerHTML = '';

        // divide as linhas do codigo
        let array = response.prototipo.split('\n');
        console.log(array);

        // contador de linhas
        let lineCounter = 0;
        array.forEach(line => {
            let linha = document.createElement('nav');
            linha.className = "line";

            let counter = document.createElement('span');
            counter.className = "number";
            counter.innerHTML = ++lineCounter;
            linha.appendChild(counter);

            let code = document.createElement('code');
            code.innerHTML =`<span>${line}</span>`;
            linha.appendChild(code);


            destino.appendChild(linha);
        });
    })
})
