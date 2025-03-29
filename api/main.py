from fastapi import FastAPI
from Models import Computer, ComputerLinux
from fastapi.middleware.cors import CORSMiddleware
origins = ["*"]

actualComputer: Computer = ComputerLinux()

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

@app.post("/processos/{PID}/stop")
def parar_processo(PID):
    return actualComputer.stopProcess(PID)

@app.post("/processos/{PID}/continue")
def parar_processo(PID):
    return actualComputer.continueProcess(PID)

@app.post("/processos/{PID}/kill")
def matar_processo(PID):
    return actualComputer.killProcess(PID)

@app.get("/permissoes/")
def get_permissoes_caminho(caminho):
    return actualComputer.get_permissoes_caminho(caminho)

@app.put("/permissoes/")
def update_permissoes(caminho, permissoes):
    return actualComputer.update_permissoes_caminho(caminho, permissoes)


app.add_middleware(
    CORSMiddleware,
    allow_origins=origins,  # Permitir apenas essas origens
    allow_credentials=True,
    allow_methods=["*"],  # Permitir todos os métodos (GET, POST, PUT, DELETE, etc.)
    allow_headers=["*"],  # Permitir todos os cabeçalhos
)