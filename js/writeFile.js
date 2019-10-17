function writeFile()
{
    fetch('../control/?action=API&id=*')
        .then(response => response.json())
        .then(content => {

            let data = new FormData();
            data.append("content", JSON.stringify(content));

            fetch(`../control/?action=saveFile`, { method: 'post',
                body: data
            })
                .then(() => {
                    console.log("arquivo salvo com sucesso!")
                });
        })
        .catch(response => console.log('erro ao salvar o arquivo: ' + response))
}