<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="w-3xl sm:px-6 lg:px-8">
            <div class="flex gap-4"> <!-- Adicionando flex para alinhar lado a lado e gap para espaçamento -->
                <div class="max-[75%] w-full bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <form action="{{route('register-machine')}}" method="POST">
                            @csrf
                            <div class="flex items-start justify-between gap-4"> <!-- Alinha os elementos pelo topo -->
                                <div class="flex flex-col w-1/2">
                                    <x-input-label for="name" :value="__('Nome')" />
                                    <x-text-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('url')" required autofocus />
                                </div>
                                <div class="flex flex-col w-1/2">
                                    <x-input-label for="url" :value="__('URL')" />
                                    <x-text-input id="url" class="block mt-1 w-full" type="text" name="url" :value="old('url')" required autofocus />
                                </div>
                                <div class="flex flex-col w-1/4">
                                    <x-input-label for="password" :value="__('PASSWORD')" />
                                    <x-text-input id="password" class="block mt-1 w-full" type="password" name="password" :value="old('password')" required autofocus />
                                </div>
                                <div class="w-1/4 flex items-end"> <!-- Alinha o botão corretamente -->
                                    <x-primary-button class="w-full">
                                        {{ __('Salvar') }}
                                    </x-primary-button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
                <div class="max-w-[25%] w-full bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        @foreach ($maquinas as $maquina)
                            <div class="py-1" >
                                <div class="flex items-start justify-between gap-4">
                                    <a href="{{route('dashboard.maquina', $maquina->id)}}">{{ $maquina->name }} -> {{ $maquina->url }}</a>
                                    <a href="{{route('deletar.maquina', $maquina->id)}}">X</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
