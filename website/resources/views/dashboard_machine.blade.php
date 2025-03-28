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
                <div class="flex items-start justify-between mb-4">
                    <div class="container w-1/2 grafico1">
                        cpu
                    </div>
                    <div class="container w-1/2 grafico2">
                        memoria
                    </div>
                </div>
                <div class="flex items-start justify-between">
                    <div>
                        Grafico memória
                    </div>
                    <div>
                        Grafico CPU
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
                                $url = "http://127.0.0.1:5000/estatisticas"; // URL da API FastAPI
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
                                            parar
                                            continuar
                                            matar
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script>
        const url = "{{ $maquina->url }}";

        fetch("http://"+url+"/estatisticas")
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Erro HTTP! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                document.querySelector("table#process tbody");

            }) // Exibe os dados no console
            .catch(error => console.error("Erro na requisição:", error));
    </script>
</x-app-layout>
