import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

$(document).ready(function () {
    $('#process').DataTable({
        "paging": true,         // Habilita paginação
        "searching": true,      // Habilita pesquisa
        "ordering": true,       // Habilita ordenação
        "info": true,           // Exibe informações da tabela
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
        }
    });

    // Eventos para os botões de ação
    $(".stop").click(function() {
        let pid = $(this).data("pid");
        alert("Parando processo: " + pid);
    });

    $(".continue").click(function() {
        let pid = $(this).data("pid");
        alert("Continuando processo: " + pid);
    });

    $(".kill").click(function() {
        let pid = $(this).data("pid");
        alert("Matando processo: " + pid);
    });
});
