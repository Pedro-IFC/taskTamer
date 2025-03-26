from fastapi import FastAPI

app = FastAPI()
@app.get("/")
def get_estatisticas():
    return {"Bem vindo!"}

@app.get("/estatisticas")
def get_estatisticas():
    return {
        'memoria' : 2000,
        'CPU' : 200,
        'processos' : {
            {
                'PID' : 1,
                'Nome' : "nome",
                'memoria' : 100,
                'CPU' : 100
            }
        }
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
def get_permissoes_caminho(item: dict):
    return True

