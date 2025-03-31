<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $maquina->name }}
        </h2>
    </x-slot>

    <div class="py-12 flex items-start justify-center">
        <div class="container w-4/5 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="w-3xl sm:px-6 lg:px-8 mb-4">
                <div class="info py-6 mb-4">
                    <h1 class="text-4xl">{{$maquina->name}}</h1>
                    <h2 class="text-3xl">{{$maquina->url}}</h2>
                </div>

                <div class="flex flex-wrap gap-4 mb-4">
                    <div class="w-full md:w-1/2 p-4 bg-gray-50 rounded-lg shadow">
                        <h3 class="text-lg font-medium mb-2">Monitoramento da CPU</h3>
                        <div class="relative h-64">
                            <canvas id="cpuChart" class="absolute inset-0 w-full h-full"></canvas>
                        </div>
                    </div>
                    
                    <div class="w-full md:w-1/2 p-4 bg-gray-50 rounded-lg shadow">
                        <h3 class="text-lg font-medium mb-2">Uso de Memória</h3>
                        <div class="relative h-64">
                            <canvas id="memoryChart" class="absolute inset-0 w-full h-full"></canvas>
                        </div>
                    </div>
                </div>
                <div class="row processos py-4">
                    <table id="process">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>PID</th>
                                <th>CPU</th>
                                <th>Memória</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                header("Content-Type: application/json");
                                $url = "http://127.0.0.1:8000/estatisticas"; 
                                $ch = curl_init($url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                $response = json_decode(curl_exec($ch));
                            @endphp
                            @foreach($response->processos as $processo)
                                <tr>
                                    <td>{{$processo->nome}}</td>
                                    <td>{{$processo->PID}}</td>
                                    <td>{{$processo->CPU}}</td>
                                    <td>{{$processo->memoria}}</td>
                                    <td>
                                        <div>
                                            <button idmachine="{{$processo->PID}}" url="{{$maquina->url}}" class="continue-process focus:outline-none text-white bg-green-700 hover:bg-green-800 focus:ring-4 focus:ring-green-300 font-medium rounded-lg text-sm px-2.5 py-1.5 me-2  dark:bg-green-600 dark:hover:bg-green-700 dark:focus:ring-green-900"><i class="fa-solid fa-play"></i></button>
                                            <button idmachine="{{$processo->PID}}" url="{{$maquina->url}}" class="stop-process focus:outline-none text-white bg-orange-700 hover:bg-orange-800 focus:ring-4 focus:ring-orange-300 font-medium rounded-lg text-sm px-2.5 py-1.5 me-2  dark:bg-orange-600 dark:hover:bg-orange-700 dark:focus:ring-orange-900"><i class="fa-solid fa-xmark"></i></button>
                                            <button idmachine="{{$processo->PID}}" url="{{$maquina->url}}" class="kill-process focus:outline-none text-white bg-red-700 hover:bg-red-800 focus:ring-4 focus:ring-red-300 font-medium rounded-lg text-sm px-2.5 py-1.5 me-2  dark:bg-red-600 dark:hover:bg-red-700 dark:focus:ring-red-900"><i class="fa-solid fa-stop"></i></button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
    @push('scripts')
    <script type="module">
        import Chart from 'chart.js/auto';

        let cpuChart, memoryChart;
        const maxDataPoints = 15;
        const updateInterval = 3000; // 3 segundos

        async function fetchStats() {
            try {
                const response = await fetch('http://127.0.0.1:8000/estatisticas');
                return await response.json();
            } catch (error) {
                console.error('Erro na requisição:', error);
                return null;
            }
        }

        function initCharts() {
            const cpuCtx = document.getElementById('cpuChart');
            const memoryCtx = document.getElementById('memoryChart');

            const chartOptions = {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        ticks: {
                            callback: (value) => value + '%'
                        }
                    }
                }
            };

            cpuChart = new Chart(cpuCtx, {
                type: 'line',
                data: {
                    labels: Array(maxDataPoints).fill(''),
                    datasets: [{
                        label: 'Uso da CPU (%)',
                        data: [],
                        borderColor: '#3B82F6',
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderWidth: 2,
                        tension: 0.3
                    }]
                },
                options: chartOptions
            });

            memoryChart = new Chart(memoryCtx, {
                type: 'line',
                data: {
                    labels: Array(maxDataPoints).fill(''),
                    datasets: [{
                        label: 'Uso de Memória (%)',
                        data: [],
                        borderColor: '#10B981',
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        tension: 0.3
                    }]
                },
                options: chartOptions
            });
        }

        async function updateCharts() {
            const data = await fetchStats();
            if (!data) return;

            cpuChart.data.datasets[0].data = [
                ...cpuChart.data.datasets[0].data.slice(-maxDataPoints + 1),
                data.CPU
            ];

            const usedMemory = data.processos.reduce((sum, proc) => sum + proc.memoria, 0);
            const memoryPercent = (usedMemory / data.memoria * 100).toFixed(2);
            
            memoryChart.data.datasets[0].data = [
                ...memoryChart.data.datasets[0].data.slice(-maxDataPoints + 1),
                parseFloat(memoryPercent)
            ];

            cpuChart.update();
            memoryChart.update();
        }

        document.addEventListener('DOMContentLoaded', () => {
            initCharts();
            setInterval(updateCharts, updateInterval);
        });

        window.addEventListener('beforeunload', () => {
            if (cpuChart) cpuChart.destroy();
            if (memoryChart) memoryChart.destroy();
        });
    </script>
    @endpush
</x-app-layout>