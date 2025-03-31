import './bootstrap';
import Alpine from 'alpinejs';
import Chart from 'chart.js/auto';
import $ from 'jquery';
import DataTable from 'datatables.net-dt';
import Swal from 'sweetalert2';

// Seção Alpine.js (mantida intacta)
window.Alpine = Alpine;
Alpine.start();

// Seção Gráficos (suas modificações)
let cpuChart, memoryChart;
let updateInterval;
const maxDataPoints = 15;

const initializeCharts = () => {
    const cpuCtx = document.getElementById('cpuChart');
    const memoryCtx = document.getElementById('memoryChart');
    
    if (!cpuCtx || !memoryCtx) return false;

    const chartOptions = {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                max: 100,
                ticks: {
                    callback: value => value + '%'
                }
            }
        }
    };

    cpuChart = new Chart(cpuCtx.getContext('2d'), {
        type: 'line',
        data: {
            labels: Array(maxDataPoints).fill(''),
            datasets: [{
                label: 'Uso da CPU (%)',
                data: [],
                borderColor: '#3B82F6',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: chartOptions
    });

    memoryChart = new Chart(memoryCtx.getContext('2d'), {
        type: 'line',
        data: {
            labels: Array(maxDataPoints).fill(''),
            datasets: [{
                label: 'Uso de Memória (%)',
                data: [],
                borderColor: '#10B981',
                backgroundColor: 'rgba(16, 185, 129, 0.1)',
                borderWidth: 2,
                tension: 0.3,
                fill: true
            }]
        },
        options: chartOptions
    });

    return true;
};

const updateCharts = async () => {
    try {
        const response = await fetch('http://127.0.0.1:8000/estatisticas');
        const data = await response.json();

        // Atualizar CPU
        cpuChart.data.datasets[0].data = [
            ...cpuChart.data.datasets[0].data.slice(-maxDataPoints + 1),
            data.CPU
        ];

        // Calcular memória
        const usedMemory = data.processos.reduce((sum, proc) => sum + proc.memoria, 0);
        const memoryPercent = (usedMemory / data.memoria * 100).toFixed(2);
        
        memoryChart.data.datasets[0].data = [
            ...memoryChart.data.datasets[0].data.slice(-maxDataPoints + 1),
            parseFloat(memoryPercent)
        ];

        cpuChart.update();
        memoryChart.update();

    } catch (error) {
        console.error('Erro ao atualizar gráficos:', error);
    }
};

// Seção Processos (versão original do colega)
$(document).ready(function () {
    // DataTable
    $('#process').DataTable({
        "paging": true,
        "searching": true,
        "ordering": true,
        "info": true,
        "language": {
            "url": "https://cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json"
        }
    });

    // Handlers originais
    $(".continue-process").click(function() {
        const url = $(this).attr("url");
        const idMachine = $(this).attr("idmachine");
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
        const url = $(this).attr("url");
        const idMachine = $(this).attr("idmachine");
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
        const url = $(this).attr("url");
        const idMachine = $(this).attr("idmachine");
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

    // Inicializar gráficos
    if (initializeCharts()) {
        updateCharts();
        setInterval(updateCharts, 3000);
    }
});

// Cleanup
window.addEventListener('beforeunload', () => {
    clearInterval(updateInterval);
    if (cpuChart) cpuChart.destroy();
    if (memoryChart) memoryChart.destroy();
});