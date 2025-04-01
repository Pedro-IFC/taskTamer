import Alpine from 'alpinejs';

window.Alpine = Alpine;
Alpine.start();
let cpuChart, memoryChart;
const updateInterval = 400;
const maxDataPoints = 10;

const urlGeral = "http://"+document.querySelector('#url').innerHTML;

const initializeCharts = () => {
    const cpuCtx = document.getElementById('cpuChart')?.getContext('2d');
    const memoryCtx = document.getElementById('memoryChart')?.getContext('2d');

    if (!cpuCtx || !memoryCtx) return false;

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: { callback: value => value + '%' }
            }
        }
    };

    const createChart = (ctx, label, borderColor, backgroundColor) => new Chart(ctx, {
        type: 'line',
        data: {
            labels: Array(maxDataPoints).fill(''),
            datasets: [{
                label,
                data: [],
                borderColor,
                backgroundColor,
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: chartOptions
    });

    cpuChart = createChart(cpuCtx, 'Uso da CPU (%)', '#3B82F6', 'rgba(59, 130, 246, 0.1)');
    memoryChart = createChart(memoryCtx, 'Uso de Memória (%)', '#10B981', 'rgba(16, 185, 129, 0.1)');

    return true;
};

const updateChart = (chart, value) => {
    const dataset = chart.data.datasets[0];
    
    dataset.data.push(value);
    
    if (dataset.data.length > maxDataPoints) {
        dataset.data.shift(); // Remove o primeiro elemento dos dados
    }

    chart.update();
};

const updateCharts = async () => {
    try {
        const response = await fetch(urlGeral + "/estatisticas");
        const data = await response.json();
        if (!cpuChart || !memoryChart) return;
        updateChart(cpuChart, data.CPU);
        const memoryPercent = ((data.memoria_usada * 100 / data.memoria) ).toFixed(2);
        updateChart(memoryChart, parseFloat(memoryPercent));
    } catch (error) {
        console.error('Erro ao atualizar gráficos:', error);
    }
};



$(document).ready(function () {
    $('#process').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
        }
    });

    if (initializeCharts()) {
        updateCharts();
        setInterval(updateCharts, 3000);
    }
});

const atualizarProcessos = async () => {
    try {
        const response = await fetch(urlGeral + "/estatisticas");
        const data = await response.json();

        if ($.fn.DataTable.isDataTable("#process")) {
            $("#process").DataTable().clear().destroy();
        }
        let tabela = $("#process tbody");
        tabela.empty();

        data.processos.forEach(processo => {
            tabela.append(`
                <tr>
                    <td>${processo.nome}</td>
                    <td>${processo.PID}</td>
                    <td>${processo.CPU}</td>
                    <td>${processo.memoria}</td>
                    <td>
                        <div>
                            <button idmachine="${processo.PID}" url="${urlGeral}" class="continue-process focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-2.5 py-1.5 me-2 dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-900">
                                <i class="fa-solid fa-play"></i>
                            </button>
                            <button idmachine="${processo.PID}" url="${urlGeral}" class="stop-process focus:outline-none text-white bg-orange-700 hover:bg-orange-800 focus:ring-4 focus:ring-orange-300 font-medium rounded-lg text-sm px-2.5 py-1.5 me-2 dark:bg-orange-600 dark:hover:bg-orange-700 dark:focus:ring-orange-900">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                            <button idmachine="${processo.PID}" url="${urlGeral}" class="kill-process focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-2.5 py-1.5 me-2 dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900">
                                <i class="fa-solid fa-stop"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `);
        });

        adicionarEventosBotoes();
        $("#process").DataTable({
            paging: true,
            searching: true,
            ordering: true,
            info: true,
            language: {
                url: "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
            }
        });
    } catch (error) {
        console.error("Erro ao atualizar processos:", error);
    }
};
const adicionarEventosBotoes = () => {
    $(".continue-process").click(function() {
        const url = $(this).attr("url");
        const idMachine = $(this).attr("idmachine");
        $.post(url + "/processos/" + idMachine + "/continue", function(response) {
            Swal.fire({ title: response});
        }).fail(function() {
            Swal.fire({ icon: "error", title: "Oops...", text: "Algo deu errado!" });
        });
        atualizarProcessos();
    });
    $(".stop-process").click(function() {
        const url = $(this).attr("url");
        const idMachine = $(this).attr("idmachine");
        $.post(url + "/processos/" + idMachine + "/stop", function(response) {
            Swal.fire({ title: response});
        }).fail(function() {
            Swal.fire({ icon: "error", title: "Oops...", text: "Algo deu errado!" });
        });
        atualizarProcessos();
    });
    $(".kill-process").click(function() {
        const url = $(this).attr("url");
        const idMachine = $(this).attr("idmachine");
        $.post(url + "/processos/" + idMachine + "/kill", function(response) {
            Swal.fire({ title: response});
        }).fail(function() {
            Swal.fire({ icon: "error", title: "Oops...", text: "Algo deu errado!" });
        });
        atualizarProcessos();
    });
};

$("#atualizar-processos").on("click", async function () {
    Swal.fire({
        title: "Carregando...",
        text: "Atualizando lista de processos",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    await atualizarProcessos();

    Swal.close();
})

window.addEventListener('beforeunload', () => {
    clearInterval(updateInterval);
    if (cpuChart) cpuChart.destroy();
    if (memoryChart) memoryChart.destroy();
});

adicionarEventosBotoes()


$("#verify-permission").on("change", function(){
    Swal.fire({
        title: "Carregando...",
        text: "Atualizando lista de processos",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    $.get(urlGeral + "/permissoes/", {"caminho": $("#verify-permission").val()}, function(response) {
        let html = "";
        if(response.data_modificacao){
            html = `<table border="1" cellspacing="0" cellpadding="5">
                <tr><th>Propriedade</th><th>Valor</th></tr>
                <tr><td>Data Modificação</td><td>${response.data_modificacao}</td></tr>
                <tr><td>Dono</td><td>${response.dono}</td></tr>
                <tr><td>Grupo</td><td>${response.grupo}</td></tr>
                <tr><td>Permissões</td><td>${response.permissoes}</td></tr>
                <tr><td>Tamanho</td><td>${response.tamanho}</td></tr>
            </table>`;
        }
        $("#retorno-permissao").html(html);
        Swal.close();
    });
});
$("#atualizar-permissao").on("click", function(){
    Swal.fire({
        title: "Carregando...",
        text: "Atualizando lista de processos",
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    $.ajax({
        url: urlGeral + "/permissoes/",
        type: "PUT",
        contentType: "application/json",
        data: JSON.stringify({
            caminho: $("#verify-permission").val(),
            usuario: $("#usuario").val(),
            permissao_usuario: $("#permissao_usuario").val(),
            grupo: $("#grupo").val(),
            permissao_grupo: $("#permissao_grupo").val()
        }),
        success: function(response) {
            console.log(response.data);
            Swal.close();
            $.get(urlGeral + "/permissoes/", {"caminho": $("#verify-permission").val()}, function(response) {
                let html = "";
                if(response.data_modificacao){
                    html = `<table border="1" cellspacing="0" cellpadding="5">
                        <tr><th>Propriedade</th><th>Valor</th></tr>
                        <tr><td>Data Modificação</td><td>${response.data_modificacao}</td></tr>
                        <tr><td>Dono</td><td>${response.dono}</td></tr>
                        <tr><td>Grupo</td><td>${response.grupo}</td></tr>
                        <tr><td>Permissões</td><td>${response.permissoes}</td></tr>
                        <tr><td>Tamanho</td><td>${response.tamanho}</td></tr>
                    </table>`;
                }
                $("#retorno-permissao").html(html);
            });
        },
        error: function(xhr, status, error) {
            console.error("Erro ao atualizar permissões:", error);
        }
    });
});
