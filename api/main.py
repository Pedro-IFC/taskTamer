from fastapi import FastAPI
from Models import Computer, ComputerWindows

actualComputer: Computer = ComputerWindows()

app = FastAPI()
@app.get("/")
def inicial():
    return {"Bem vindo!"}

@app.get("/estatisticas")
def get_estatisticas():
    return {
        'SO' : actualComputer.getSO(),
        'memoria' : actualComputer.get_total_memory(),
        'CPU' : actualComputer.get_cpu_usage(),
        'processos' : actualComputer.get_processes()
    }

@app.delete("/processos/{PID}/stop")
def parar_processo(PID):
    return actualComputer.stopProcess(PID)

@app.put("/processos/{PID}/continue")
def parar_processo(PID):
    return actualComputer.continueProcess(PID)

@app.delete("/processos/{PID}/kill")
def matar_processo(PID):
    return actualComputer.killProcess(PID)

@app.get("/permissoes/")
def get_permissoes_caminho(caminho):
    return actualComputer.get_permissoes_caminho(caminho)

@app.put("/permissoes/")
def update_permissoes(caminho, permissoes):
    return actualComputer.update_permissoes_caminho(caminho, permissoes)

