import psutil
import os
import stat
import pwd
import grp
import time
from .Computer import Computer

class ComputerLinux(Computer):
    def getSO(self):
        return "Linux"
    def get_total_memory(self):
        return psutil.virtual_memory().total

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
            permissoes = stat.filemode(info.st_mode)
            dono = pwd.getpwuid(info.st_uid).pw_name
            grupo = grp.getgrgid(info.st_gid).gr_name
            tamanho = info.st_size
            data_modificacao = time.strftime("%b %d %H:%M", time.localtime(info.st_mtime))
            return {
                "permissoes": permissoes,
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
        
    def update_permissoes_caminho(self, caminho, permissoes):
        try:
            modo_numerico = 0
            mapeamento = {'r': 4, 'w': 2, 'x': 1, '-': 0}
            if len(permissoes) != 9:
                return "Erro: A string de permissões deve ter exatamente 9 caracteres (ex: 'rwxr----x')."
            for i, char in enumerate(permissoes):
                if char not in mapeamento:
                    return f"Erro: Caractere inválido '{char}' nas permissões."
                if i % 3 == 0:
                    modo_numerico *= 10  # Move o valor para a esquerda
                modo_numerico += mapeamento[char]
            os.chmod(caminho, int(str(modo_numerico), 8))
            return f"Permissões de '{caminho}' alteradas para '{permissoes}'."
        except FileNotFoundError:
            return f"Erro: O caminho '{caminho}' não foi encontrado."
        except PermissionError:
            return f"Erro: Permissão negada para modificar '{caminho}'."
        except Exception as e:
            return f"Erro ao processar '{caminho}': {e}"