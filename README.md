Gerenciador de Tarefas e processos no Linux
=================
Rotas:

*   /api/estatisticas - GET, retorno: 
    {
        'memoria' => 2000,
        'CPU' => 200,
        'processos' => [
            {
                'PID' => 1,
                'Nome' => nome,
                'memoria' => 100,
                'CPU' => 100
            }
            ...
        ]
    }
*   /api/processos/{PID}/stop - DELETE, retorno: booleano
*   /api/processos/{PID}/kill - DELETE, retorno: booleano

*   /api/permissoes/{caminho} - GET, retorno: 
    [
        "-rw-r--r-- 1 root root 1554 Mar 26 10:30 /etc/passwd"
    ]
*   /api/permissoes/ - POST, parametros: caminho, usuario, grupo e permissao, retorno: booleano

Dependências
=================
*   fastapi uvicorn

Instalação 
=================
*   pip install fastapi uvicorn

Rodar api
=================
*   cd api
*   uvicorn main:app --reload
