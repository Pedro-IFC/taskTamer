<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $maquina->name }}
        </h2>
        @php
            header("Content-Type: application/json");
            $url = $maquina->url."/estatisticas";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $response = json_decode(curl_exec($ch));
        @endphp
    </x-slot>
    <div id="url" style="display: none">{{$maquina->url}}</div>
    <div class="py-12 flex items-start justify-center">
        <div class="container w-6/7 bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="w-3xl sm:px-6 lg:px-8 mb-4">
                <div class="flex flex-wrap justify-between mt-8">
                    <div class="info md:w-1/6">
                        <h1 class="text-4xl">{{$maquina->name}}</h1>
                        <h1 class="text-2xl">Sistema: {{$response->SO}}</h1>
                        <h2 class="text-1xl">{{$maquina->url}}</h2>
                    </div>
                    <div class="w-full md:w-2/6 p-4 bg-gray-50 rounded-lg shadow">
                        <h3 class="text-lg font-medium mb-2">Monitoramento da CPU</h3>
                        <div class="relative h-64">
                            <canvas id="cpuChart" class="absolute inset-0 w-full h-full"></canvas>
                        </div>
                    </div>
                    <div class="w-full md:w-2/6 p-4 bg-gray-50 rounded-lg shadow">
                        <h3 class="text-lg font-medium mb-2">Uso de Memória</h3>
                        <div class="relative h-64">
                            <canvas id="memoryChart" class="absolute inset-0 w-full h-full"></canvas>
                        </div>
                    </div>
                </div>
                <div class="w-6/7 flex flex-wrap justify-between py-10">
                    <div class="block">
                        <input class="w-full" type="text" id="verify-permission" placeholder="Digite o caminho">
                        <div id="retorno-permissao"></div>
                    </div>
                    <div class="block">
                        <input type="text" id="usuario" placeholder="Usuário">
                    </div>
                    <div class="block">
                        <input type="text" id="permissao_usuario" placeholder="Permissao de usuário">
                    </div>
                    <div class="block">
                        <input type="text" id="gruoo" placeholder="Grupo">
                    </div>
                    <div class="block">
                        <input type="text" id="permissao_grupo" placeholder="Permissao de grupo">
                    </div>
                    <div class="block">
                        <button id="atualizar-permissao" type="button" class="text-gray-900 bg-white border border-gray-300 focus:outline-none hover:bg-gray-100 focus:ring-4 focus:ring-gray-100 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-gray-800 dark:text-white dark:border-gray-600 dark:hover:bg-gray-700 dark:hover:border-gray-600 dark:focus:ring-gray-700">Atualizar Permissão</button>
                    </div>
                </div>
                <div class="block">
                    <button id="atualizar-processos" class="focus:outline-none text-white bg-purple-700 hover:bg-purple-800 focus:ring-4 focus:ring-purple-300 font-medium rounded-lg text-sm px-5 py-2.5 mb-2 dark:bg-purple-600 dark:hover:bg-purple-700 dark:focus:ring-purple-900">Atualizar registros</button>
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
</x-app-layout>
