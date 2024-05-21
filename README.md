# Transaction API

## 1. Introdução

A Transaction API é uma API REST que realiza transferência de valores entre dois usuários.

## 2. Como Usar?

Para executar essa aplicação em seu ambiente local é necessário apenas o Docker.

### 2.1 Baixar projeto do Git

```sh
git clone git@github.com:felipegcavalcante/transaction-api.git
```

### 2.2 Definir variáveis de ambiente

Crie um arquivo `.env` a partir do arquivo `.env.example` com as suas credenciais.

```sh
cp .env.example .env
```

### 2.3 Subir o projeto no Docker

```sh
docker compose up -d
```

### 2.4 Instalar as dependências do projeto

```sh
docker compose exec php composer install
```

### 2.5 Criação da base de dados e tabelas

> O banco de dados e as tabelas necessárias bem como seus dados são criados durante a inicialização do contêiner `mysql`! 

### 2.6 Execução da transferência

```sh
curl --location --request POST 'http://localhost:8080/transfer' \
--header 'Content-Type: application/json' \
--data-raw '{
  "value": 10.0,
  "payer": 1,
  "payee": 2
}'
```

## 3. Documentação

A documentação completa do funcionamento da API de transferência entre usuários encontra-se na pasta raíz do projeto com o nome `openapi.yml`.

## 4. Erros retornados

| Tipo do Erro             | Descrição                                                                                       |
|--------------------------|-------------------------------------------------------------------------------------------------|
| `ValidationError`          | Ocorre quando o payload enviado é inválido.                                                     |
| `TransferNonPositiveValue` | Ocorre ao tentar transferir um valor não positivo.                                              |
| `CannotTransferToSameUser` | Ocorre ao tentar realizar uma transferência em que o usuário pagador é o mesmo que o recebedor. |
| `CannotTransferByMerchant` | Ocorre ao tentar transferir um valor a partir de um usuário lojista.                            |
| `InsufficientBalance`      | Ocorre quando o usuário pagador não possui saldo suficiente para realizar a transferência.      |
| `UnauthorizedTransfer`     | Ocorre quando a transferência não foi autorizada.                                               |
| `NotFoundUserById`         | Ocorre quando um usuário não existe.                                                            |
| `UnexpectedError`          | Ocorre quando há um erro inesperado.                                                            |
| `InternalError`            | Ocorre quando há um erro interno de servidor.                                                   |

## 5. Qualidade de código

Neste projeto foram usadas algumas ferramentas de análise estática e testes do código como PHPUnit, PHP_CodeSniffer, PHPStan e PHPMD.

### 5.1 Executar os testes

```sh
docker compose exec php composer phpunit
```

### 5.2 Executar o PHP_CodeSniffer

```sh
docker compose exec php composer phpcs
```

### 5.3 Executar o PHPStan

```sh
docker compose exec php composer phpstan
```

### 5.4 Executar o PHPMD

```sh
docker compose exec php composer phpmd
```

### 5.5 Executar todas as ferramentas de qualidade

```sh
docker compose exec php composer qa
```
