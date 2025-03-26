import psutil
from .Computer import Computer

class ComputerLinux(Computer):
    def get_total_memory():
        """Retorna a memória total do sistema em bytes."""
        return psutil.virtual_memory().total

    def get_cpu_usage():
        """Retorna o percentual de uso da CPU."""
        return psutil.cpu_percent(interval=1)

    def get_processes():
        """Retorna uma lista de processos ativos com PID, Nome, Memória e CPU."""
        processos = []
        for proc in psutil.process_iter(attrs=['pid', 'name', 'memory_info', 'cpu_percent']):
            try:
                info = proc.info
                processos.append({
                    'PID': info['pid'],
                    'Nome': info['name'],
                    'memoria': info['memory_info'].rss, 
                    'CPU': info['cpu_percent'] 
                })
            except (psutil.NoSuchProcess, psutil.AccessDenied, psutil.ZombieProcess):
                continue

        return processos
