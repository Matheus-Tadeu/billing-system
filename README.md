# Sistema de Cobran�a

Este projeto consiste em dois micros servi�os projetados para lidar com a importa��o, gera��o e notifica��o de registros de cobran�a em grande escala.

## Microsservi�os

### 1. ms-import
- **Fun��o**: Processa arquivos e publica em uma fila.
- **Status**: Implementado.
- **Fluxo**:
    1. O usu�rio faz o upload de um arquivo CSV.
    2. O sistema salva as informa��es do arquivo.
  3. O sistema quebra o arquivo em lotes para processamento.
  4. O sistema verifica se os dados do lote j� existem no banco de dados.
  5. O sistema valida os dados do lote, separando os dados v�lidos dos inv�lidos.
  6. O sistema classifica os dados do lote para criar ou atualizar registros.
  7. O sistema divide o lote em sub-lotes e publica na fila.
  8. O sistema processa os sub-lotes e cria ou atualiza os registros no banco de dados de forma ass�ncrona ou s�ncrona.
  10. O sistema responde ao usu�rio com o status do processamento, informando a quantidade de registros v�lidos, inv�lidos e seus motivos. 
  11. O sistema consome em um topico o status da cria��o do boleto e atualiza o registro no banco de dados.

### 2. ms-bill-generation
- **Fun��o**: Processa os dados da fila para gera boletos e notifica��o.
- **Status**: Em desenvolvimento.
- **Fluxo**:
    1. O sistema consome os sub-lotes da fila.
    2. O sistema gera os boletos e notifica o usu�rio.
    3. O sistema responde publica em um topico o status da gera��o do boleto.
  4. O sistema notifica a pessoa em quest�o sobre a gera��o do boleto.

## Tecnologias Utilizadas
- PHP
- Laravel
- MongoDB
- RabbitMQ
- Docker
- Docker Compose
- PHPUnit
- Swagger

## Como Executar

Para iniciar todo o sistema, basta executar o seguinte comando:

```sh
docker-compose up -d
```

# MS-IMPORT

## Como acessar a documenta��o da API

Acesse a documenta��o da API atrav�s da seguinte URL: [Link para a documenta��o da API](http://localhost:9001/api/documentation)

Para atualizar a documenta��o da API, execute o seguinte comando:
```bash
docker exec -it billing-system_ms-import_1 bash -c "php artisan l5-swagger:generate"
```

## Como executar o processamento de arquivos CSV
```bash
docker exec -it billing-system_ms-import_1 bash -c "php artisan queue:work --queue=batch_save --daemon --sleep=3"
```

## Como acompanhar os logs do processamento
- Acesse o diret�rio ms-import/storage/logs/

## Como rodar os testes
```bash
docker exec -it billing-system_ms-import_1 bash -c "php artisan test"
```

## Vari�veis de Ambiente

- **CSV_BATCH_SIZE_PROCESS**: Define o n�mero de registros a serem processados em um �nico lote ao lidar com arquivos CSV.
- **PROCESS_SYNC**: Define se o processamento de arquivos CSV deve ser s�ncrono ou ass�ncrono, para arquivos pequenos pode ser usado como s�ncrono.
- **SUB_BATCH_SIZE**: Especifica o tamanho dos sub-lotes nos quais o lote principal � dividido para enviar para a fila responsavel por criar ou atualizar registros.

## RabbitMQ

Acesse a interface do RabbitMQ atrav�s da seguinte URL: [Link para a interface do RabbitMQ](http://localhost:15672/)
- Login: guest
- Senha: guest

# MS-BILL-GENERATION

## Como acessar a documenta��o da API

Acesse a documenta��o da API atrav�s da seguinte URL: [Link para a documenta��o da API](http://localhost:9002/api/documentation)

Para atualizar a documenta��o da API, execute o seguinte comando:
```bash
docker exec -it billing-system_ms-bill-generation_1 bash -c "php artisan l5-swagger:generate"
```

## Como acompanhar os logs do processamento
- Acesse o diret�rio ms-import/storage/logs/

## Como rodar os testes
```bash
docker exec -it billing-system_ms-bill-generation_1 bash -c "php artisan test"
```
