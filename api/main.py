from fastapi import FastAPI
from Models import Computer, ComputerLinux

actualComputer: Computer = ComputerLinux

app = FastAPI()
@app.get("/")
def inicial():
    return {"Bem vindo!"}

@app.get("/estatisticas")
def get_estatisticas():
    return {
        'memoria' : actualComputer.get_total_memory(),
        'CPU' : actualComputer.get_cpu_usage(),
        'processos' : actualComputer.get_processes()
    }

@app.delete("/processos/{PID}/stop")
def parar_processo():
    return True

@app.delete("/processos/{PID}/kill")
def matar_processo():
    return True

@app.get("/permissoes/{caminho}")
def get_permissoes_caminho():
    return [
        "-rw-r--r-- 1 root root 1554 Mar 26 10:30 /etc/passwd"
    ]

@app.post("/permissoes/{caminho}")
def get_permissoes_caminho(item):
    return True

