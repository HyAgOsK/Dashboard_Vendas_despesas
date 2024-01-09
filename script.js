//iniciando script
$(document).ready(() => {
	$('#documentacao').on('click', () => {
       //$('#pagina').load('documentacao.html')
       /*
       $.get('documentacao.html', data => {
            $('#pagina').html(data)
        })
        */
        $.post('documentacao.html', data => { 
            $('#pagina').html(data)
        })
    })
	$('#suporte').on('click', () => {
        //$('#pagina').load('suporte.html')
        /*
        $.get('suporte.html', data => {
            $('#pagina').html(data)
        })*/
        $.post('suporte.html', data => {
            $('#pagina').html(data)
        })
    })
    $('#informacoes').on('click', () => {
        //$('#pagina').load('suporte.html')
        /*
        $.get('suporte.html', data => {
            $('#pagina').html(data)
        })*/
        $.post('incluir_informacoes.html', data => {
            $('#pagina').html(data)
        })
    })
    $('#competencia').change((e) => {
        //valor selecionado
        let compet = $(e.target).val()
        $.ajax({
            type: 'GET',
            url: 'app.php',
            data: `competencia=${compet}`,
            dataType: 'json',
            success: (dados) => {
                $('#numeroVendas').html(dados.numeroVendas)
                $('#totalVendas').html(dados.totalVendas)
                $('#clientesAtivos').html(dados.clientesAtivos)
                $('#clientesInativos').html(dados.clientesInativos)
                $('#qntddElogios').html(dados.qntddElogios)
                $('#qntddReclamacoes').html(dados.qntddReclamacoes)
                $('#totalDespesas').html(dados.totalDespesas)
                $('#totalSugestoes').html(dados.totalSugestoes)
                $('#todasVendasSomadas').html(dados.todasVendasSomadas)
                $('#todasDespesasSomadas').html(dados.todasDespesasSomadas)
                console.log(dados)
            },
            error: (erro) =>{
                console.log(erro);
            }
        })
    })

    $('form').submit((event) => {
        event.preventDefault()
        
        // Obtenha os dados do formulário
        let formData = $('form').serialize()

        // Envie os dados para o arquivo PHP utilizando AJAX
        $.ajax({
            type: 'POST',
            url: 'envio_dados.php',
            data: formData,
            success: (response) => {
                // Manipule a resposta do servidor conforme necessário
                console.log(response)
            },
            error: (error) => {
                console.log(error)
            }
        })
    })

})