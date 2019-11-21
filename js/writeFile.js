function writeFile()
{
    fetch('API/*')
        .then(response => response.json())
        .then(content => {

            let data = new FormData();
            data.append("content", JSON.stringify(content));

            fetch(`saveFile`, { method: 'post',
                body: data
            })
                .then(() => {
                    console.log("arquivo salvo com sucesso!")
                });
        })
        .catch(response => console.log('erro ao salvar o arquivo: ' + response))
}