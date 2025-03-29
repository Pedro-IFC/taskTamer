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
                                $url = "http://".$maquina->url."/estatisticas"; // URL da API FastAPI
                                $ch = curl_init($url);
                                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                                $response = json_decode(curl_exec($ch));
                            @endphp
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
</x-app-layout>
