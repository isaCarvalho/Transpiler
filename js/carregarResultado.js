let form = document.getElementById('formulario');

form.addEventListener("submit", (event) => {
    event.preventDefault()
    
    let body = new FormData(event.target)

    fetch('control/controler.php?action=enviarFonte', {method: "post", body})
    .then(response => response.json())
    .then(response => {
        let destino = document.querySelector("pre[name=cdestino]");
        console.log(response)

        // destino.value = response.prototipo

        destino.innerHTML = `<code>${response.prototipo}</code>`

        // response.forEach(line => {
        //     let code = document.createElement('<code>');
        //     code.innerHTML =`<span>${line}</span>`;
        //     destino.appendChild(code);
        // });
    })
})
