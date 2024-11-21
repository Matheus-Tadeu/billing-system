# Sistema de Cobrança

Este projeto consiste em três microsserviços (MS) projetados para lidar com a importação, geração e notificação de registros de cobrança.

## Microsserviços

### 1. ms-import
- **Função**: Importa arquivos e os publica em uma fila.
- **Status**: Implementado.
- **Tecnologias**: PHP, Laravel, Redis, Docker.

### 2. ms-bill-generation (em breve)
- **Função**: Lê a fila do `ms-import`, gera boletos e publica o status em outra fila.
- **Status**: Em breve.
- **Tecnologias**: PHP, Laravel, Redis, Docker.

### 3. ms-notification (em breve)
- **Função**: Consome a fila do `ms-bill-generation` e notifica o cliente com base nos dados.
- **Status**: Em breve.
- **Tecnologias**: PHP, Laravel, Redis, Docker.

### 4. ms-import (funcionalidade adicional - em breve) 
- **Função**: Lê a fila do `ms-bill-generation` e atualiza os registros para "processado".
- **Status**: Em breve.
- **Tecnologias**: PHP, Laravel, Redis, Docker.

## Tecnologias Utilizadas
- PHP
- Laravel
- Redis
- Docker
- Docker Compose

## Como Executar

Para iniciar todo o sistema, basta executar o seguinte comando:

```sh
docker-compose up -d
```