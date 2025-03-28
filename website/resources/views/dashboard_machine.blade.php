<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $maquina->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-3xl sm:px-6 lg:px-8">
            <div class="flex items-start justify-between">
                <div class="grafico1">
                    cpu
                </div>
                <div class="grafico2">
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
                <div class="info">
                    <h3>{{$maquina->name}}</h3>
                    <h3>{{$maquina->url}}</h3>
                </div>
            </div>
            <div class="row processos">
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
