let form = document.getElementById('formulario');

form.addEventListener("submit", (event) => {
    event.preventDefault()
    
    let body = new FormData(event.target)

    fetch('control/controler.php?action=enviarFonte', {method: "post", body})
    .then(response => response.json())
    .then(response => {
        let destino = document.querySelector("pre[name=cdestino]");
        console.log(response);

        let array = response.prototipo.split('\n');
        console.log(array);

        /**Terminar o contador de linhas*/
        // let lineCounter = 0;
        array.forEach(line => {
            // let counter = document.createElement('span');
            // counter.innerHTML = ++lineCounter;
            // destino.appendChild(counter);

            let code = document.createElement('code');
            code.innerHTML =`<span>${line}</span>`;
            destino.appendChild(code);

            let br = document.createElement('br');
            destino.appendChild(br);
        });
    })
})
