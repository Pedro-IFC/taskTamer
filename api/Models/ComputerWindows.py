import psutil
import os
import stat
import time
import win32security
import win32api
from fastapi import HTTPException

from .Computer import Computer

class ComputerWindows(Computer):
    def getSO(self):
        return "Windows"

    def get_total_memory(self):
        return psutil.virtual_memory().total

    def get_memory_usage(self):
        return psutil.virtual_memory().used

    def get_cpu_usage(self):
        return psutil.cpu_percent(interval=1)

    def get_processes(self):
        processos = []
        for proc in psutil.process_iter(attrs=['pid', 'name', 'memory_info', 'cpu_percent']):
            try:
                info = proc.info
                processos.append({
                    'PID': info['pid'],
                    'nome': info['name'],
                    'memoria': info['memory_info'].rss, 
                    'CPU': info['cpu_percent'] 
                })
            except (psutil.NoSuchProcess, psutil.AccessDenied, psutil.ZombieProcess):
                continue

        return processos

    def stopProcess(self, PID):
        try:
            proc = psutil.Process(int(PID))
            proc.suspend()  
            return f"Processo {PID} suspenso com sucesso."
        except psutil.NoSuchProcess:
            return f"Erro: Processo {PID} não encontrado."
        except psutil.AccessDenied:
            return f"Erro: Permissão negada para suspender o processo {PID}."
        except Exception as e:
            return f"Erro ao suspender o processo {PID}: {e}"

    def killProcess(self, PID):
        try:
            proc = psutil.Process(int(PID))
            proc.terminate() 
            proc.wait(timeout=3)
            if proc.is_running():
                proc.kill()
            return f"Processo {PID} finalizado com sucesso."
        except psutil.NoSuchProcess:
            return f"Erro: Processo {PID} não encontrado."
        except psutil.AccessDenied:
            return f"Erro: Permissão negada para finalizar o processo {PID}."
        except Exception as e:
            return f"Erro ao finalizar o processo {PID}: {e}"
        
    def continueProcess(self, PID):
        try:
            proc = psutil.Process(int(PID))
            proc.resume()
            return f"Processo {PID} retomado com sucesso."
        except ValueError:
            return "Erro: O PID fornecido não é um número válido."
        except psutil.NoSuchProcess:
            return f"Erro: Processo {PID} não encontrado."
        except psutil.AccessDenied:
            return f"Erro: Permissão negada para retomar o processo {PID}."
        except Exception as e:
            return f"Erro ao retomar o processo {PID}: {e}"
            
    def get_permissoes_caminho(self, caminho):
        try:
            info = os.stat(caminho)
            permissoes_str = stat.filemode(info.st_mode)

            sd = win32security.GetFileSecurity(caminho, win32security.OWNER_SECURITY_INFORMATION)
            dono_sid = sd.GetSecurityDescriptorOwner()
            dono, _, _ = win32security.LookupAccountSid(None, dono_sid)

            sd = win32security.GetFileSecurity(caminho, win32security.GROUP_SECURITY_INFORMATION)
            grupo_sid = sd.GetSecurityDescriptorGroup()
            grupo, _, _ = win32security.LookupAccountSid(None, grupo_sid)

            tamanho = info.st_size
            data_modificacao = time.strftime("%b %d %H:%M", time.localtime(info.st_mtime))

            return {
                "permissoes": permissoes_str,
                "dono": dono,
                "grupo": grupo,
                "tamanho": tamanho,
                "data_modificacao": data_modificacao
            }
        except FileNotFoundError:
            return f"Erro: O caminho '{caminho}' não foi encontrado."
        except PermissionError:
            return f"Erro: Permissão negada para acessar '{caminho}'."
        except Exception as e:
            return f"Erro ao obter permissões de '{caminho}': {e}"
        

    
    def update_permissoes_caminho(self, caminho, permissao):
        try:
            permissions_int = int(permissao, 8)  # Converte de octal para inteiro
            os.chmod(caminho, permissions_int)
            return {"status": "Permissões atualizadas com sucesso"}
        except ValueError:
            raise HTTPException(status_code=400, detail="Permissões inválidas. Use formato octal (ex: 755)")
        except FileNotFoundError:
            raise HTTPException(status_code=404, detail="Arquivo/pasta não encontrado")
        except Exception as e:
            raise HTTPException(status_code=500, detail=str(e))


