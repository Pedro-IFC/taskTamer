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
    $(".continue-process").click(function() {
        let url = $(this).attr("url");
        let idMachine = $(this).attr("idmachine");
        $.ajax({
            url: url + "/processos/" + idMachine + "/continue",
            method: 'POST',
            dataType: 'json',
            success: function(response) {
                Swal.fire({
                    icon: "success",
                    title: "Processo continuado com sucesso",
                });
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Algo deu errado!",
                });
            }
        });
    });
    $(".stop-process").click(function() {
        let url = $(this).attr("url");
        let idMachine = $(this).attr("idmachine");
        $.ajax({
            url: url + "/processos/" + idMachine + "/stop",
            method: 'POST',
            success: function(response) {
                Swal.fire({
                    icon: "success",
                    title: "Processo pausado com sucesso",
                });
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Algo deu errado!",
                });
            }
        });
    });
    $(".kill-process").click(function() {
        let url = $(this).attr("url");
        let idMachine = $(this).attr("idmachine");
        $.ajax({
            url: url + "/processos/" + idMachine + "/kill",
            method: 'POST',
            success: function(response) {
                Swal.fire({
                    icon: "success",
                    title: "Processo morto com sucesso",
                });
            },
            error: function(xhr, status, error) {
                Swal.fire({
                    icon: "error",
                    title: "Oops...",
                    text: "Algo deu errado!",
                });
            }
        });
    });
});

