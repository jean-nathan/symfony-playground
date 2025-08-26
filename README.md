# 📚 Symfony Playground

Projeto de estudos e prática do framework **Symfony 6** com Docker, focado em aprendizado de configuração, migrações e desenvolvimento.

## 🚀 Instalação e Configuração

### 1. Clone o repositório
```bash
git clone <repository-url>
cd symfony-playground
```

### 2. Instale Docker e Docker Compose
Baixe e instale o [Docker](https://docs.docker.com/get-docker/) e [Docker Compose](https://docs.docker.com/compose/install/) para gerenciar os containers da aplicação.

### 3. Configure as variáveis de ambiente
```bash
cp .env.example .env.local
```
**Por que:** O `.env.local` contém configurações específicas do seu ambiente local (senhas, URLs) que não devem ser versionadas.

### 4. Suba os containers
```bash
docker-compose up -d
```
**Por que:** Inicia todos os serviços (PHP, Nginx, MySQL, Redis) em background de forma isolada e reproduzível.

### 5. Instale as dependências PHP
```bash
docker-compose exec app composer install
```
**Por que:** Instala todas as dependências do Symfony dentro do container PHP usando as versões exatas do `composer.lock`.

### 6. Acesse a aplicação
Abra no navegador: [http://localhost:9000](http://localhost:9000)

## 🗂 Estrutura do Projeto

```text
symfony-playground/
├── docker/               # Configurações Docker (Nginx, PHP)
├── src/                  # Código-fonte da aplicação
├── studies/              # Anotações e estudos versionados
├── .env.example          # Template de variáveis de ambiente
├── composer.json         # Dependências com versões travadas
├── docker-compose.yml    # Orquestração dos containers
└── Dockerfile            # Imagem PHP personalizada
```

## 🛠 Comandos Úteis

```bash
# Parar containers
docker-compose down

# Ver logs
docker-compose logs app

# Acessar container PHP
docker-compose exec app bash

# Executar comando Symfony dentro do container
php bin/console cache:clear
```

# Executar comandos Symfony fora do container
docker-compose exec app php bin/console cache:clear
```

## 📝 Observações

- Versões travadas no `composer.json` garantem builds reproduzíveis
- Arquivos `.env.local` não são commitados por segurança
- Pasta `studies/` contém anotações de aprendizado versionadas
