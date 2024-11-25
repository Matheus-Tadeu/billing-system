# Sistema de Cobrança

Este projeto consiste em dois micros serviços projetados para lidar com a importação, geração e notificação de registros de cobrança em grande escala.

## Microsserviços

### 1. ms-import
- **Função**: Processa arquivos e publica em uma fila.
- **Status**: Implementado.
- **Fluxo**:
    1. O usuário faz o upload de um arquivo CSV.
    2. O sistema salva as informações do arquivo.
  3. O sistema quebra o arquivo em lotes para processamento.
  4. O sistema verifica se os dados do lote já existem no banco de dados.
  5. O sistema valida os dados do lote, separando os dados válidos dos inválidos.
  6. O sistema classifica os dados do lote para criar ou atualizar registros.
  7. O sistema divide o lote em sub-lotes e publica na fila.
  8. O sistema processa os sub-lotes e cria ou atualiza os registros no banco de dados de forma assíncrona ou síncrona.
  10. O sistema responde ao usuário com o status do processamento, informando a quantidade de registros válidos, inválidos e seus motivos. 
  11. O sistema consome em um topico o status da criação do boleto e atualiza o registro no banco de dados.

### 2. ms-bill-generation
- **Função**: Processa os dados da fila para gera boletos e notificação.
- **Status**: Em desenvolvimento.
- **Fluxo**:
    1. O sistema consome os sub-lotes da fila.
    2. O sistema gera os boletos e notifica o usuário.
    3. O sistema responde publica em um topico o status da geração do boleto.
  4. O sistema notifica a pessoa em questão sobre a geração do boleto.

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

## Como acessar a documentação da API

Acesse a documentação da API através da seguinte URL: [Link para a documentação da API](http://localhost:9001/api/documentation)

Para atualizar a documentação da API, execute o seguinte comando:
```bash
docker exec -it billing-system_ms-import_1 bash -c "php artisan l5-swagger:generate"
```

## Como executar o processamento de arquivos CSV
```bash
docker exec -it billing-system_ms-import_1 bash -c "php artisan queue:work --queue=batch_save --daemon --sleep=3"
```

## Como acompanhar os logs do processamento
- Acesse o diretório ms-import/storage/logs/

## Como rodar os testes
```bash
docker exec -it billing-system_ms-import_1 bash -c "php artisan test"
```

## Variáveis de Ambiente

- **CSV_BATCH_SIZE_PROCESS**: Define o número de registros a serem processados em um único lote ao lidar com arquivos CSV.
- **PROCESS_SYNC**: Define se o processamento de arquivos CSV deve ser síncrono ou assíncrono, para arquivos pequenos pode ser usado como síncrono.
- **SUB_BATCH_SIZE**: Especifica o tamanho dos sub-lotes nos quais o lote principal é dividido para enviar para a fila responsavel por criar ou atualizar registros.

## RabbitMQ

Acesse a interface do RabbitMQ através da seguinte URL: [Link para a interface do RabbitMQ](http://localhost:15672/)
- Login: guest
- Senha: guest

# MS-BILL-GENERATION

## Como acessar a documentação da API

Acesse a documentação da API através da seguinte URL: [Link para a documentação da API](http://localhost:9002/api/documentation)

Para atualizar a documentação da API, execute o seguinte comando:
```bash
docker exec -it billing-system_ms-bill-generation_1 bash -c "php artisan l5-swagger:generate"
```

## Como acompanhar os logs do processamento
- Acesse o diretório ms-import/storage/logs/

## Como rodar os testes
```bash
docker exec -it billing-system_ms-bill-generation_1 bash -c "php artisan test"
```
