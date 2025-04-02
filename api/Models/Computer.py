from abc import ABC, abstractmethod
class Computer(ABC):

    @abstractmethod
    def getSO(self):
        pass

    @abstractmethod
    def get_total_memory(self):
        pass
    
    @abstractmethod
    def get_memory_usage(self):
        pass


    @abstractmethod
    def get_cpu_usage(self):
        pass

    @abstractmethod
    def get_processes(self):
        pass

    @abstractmethod
    def stopProcess(self, PID):
        pass
    @abstractmethod
    def killProcess(self, PID):
        pass
    @abstractmethod
    def continueProcess(self, PID):
        pass
    @abstractmethod
    def get_permissoes_caminho(self, caminho):
        pass
    @abstractmethod
    def update_permissoes_caminho(self, caminho, permissao):
        pass