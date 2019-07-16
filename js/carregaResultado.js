let form = document.getElementById('formulario');

form.addEventListener("submit", (event) => {
    event.preventDefault()
    
    let body = new FormData(event.target)

    fetch('control/controler.php?action=enviarFonte', {method: "post", body})
    .then(response => response.json())
    .then(response => {
        let destino = document.querySelector("textarea[name=cdestino]");
        console.log(response)

        destino.value = response.prototipo
    })
})
