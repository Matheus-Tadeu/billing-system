# Sistema de Cobran�a

Este projeto consiste em tr�s microsservi�os (MS) projetados para lidar com a importa��o, gera��o e notifica��o de registros de cobran�a.

## Microsservi�os

### 1. ms-import
- **Fun��o**: Importa arquivos e os publica em uma fila.
- **Status**: Implementado.
- **Tecnologias**: PHP, Laravel, Redis, Docker.

### 2. ms-bill-generation (em breve)
- **Fun��o**: L� a fila do `ms-import`, gera boletos e publica o status em outra fila.
- **Status**: Em breve.
- **Tecnologias**: PHP, Laravel, Redis, Docker.

### 3. ms-notification (em breve)
- **Fun��o**: Consome a fila do `ms-bill-generation` e notifica o cliente com base nos dados.
- **Status**: Em breve.
- **Tecnologias**: PHP, Laravel, Redis, Docker.

### 4. ms-import (funcionalidade adicional - em breve) 
- **Fun��o**: L� a fila do `ms-bill-generation` e atualiza os registros para "processado".
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