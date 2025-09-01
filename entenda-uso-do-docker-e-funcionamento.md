# 🍽️ Projeto Symfony com Docker: O Kit de Cozinha Portátil

Bem-vindo ao nosso projeto\! Aqui, usamos o Docker para garantir que todos os desenvolvedores tenham exatamente o mesmo ambiente de trabalho. Isso acaba com o famoso "Mas no meu computador funciona\!".

### 🍳 A Analogia da Cozinha

Imagine que cada computador é uma cozinha diferente, com utensílios e ingredientes variados. Para garantir que nosso prato (o projeto) saia perfeito, o Docker nos permite empacotar nossa cozinha ideal e enviá-la para qualquer lugar.

  * **`Dockerfile`**: É a **"receita de bolo"** para criar um dos nossos utensílios (o servidor PHP). Ele diz exatamente quais ingredientes usar e como prepará-los.
  * **`docker-compose.yml`**: É o **"cardápio"** do nosso jantar completo. Ele lista todos os "pratos" (contêineres como PHP, Nginx, Banco de Dados) que precisamos e como eles devem trabalhar juntos na mesa.
  * **`composer.json` e `composer.lock`**: São a **"lista de compras"** do nosso projeto. O `.json` é a lista geral, e o `.lock` é a lista travada com as marcas e versões exatas de cada ingrediente, garantindo que o sabor seja sempre o mesmo.

-----

## 🛠️ Sua Etapa (Criação do Projeto)

Esta seção é para você, que está configurando o projeto pela primeira vez.

### 1\. Pré-requisitos

Certifique-se de ter o [Docker](https://www.docker.com/get-started) e o [Git](https://git-scm.com/) instalados em seu computador.

### 2\. Prepare o "Kit de Cozinha"

Crie os arquivos essenciais na raiz do seu projeto.

**`docker-compose.yml` (O Cardápio)**

```yaml
version: '3.8'

services:
  nginx:
    image: nginx:stable-alpine
    container_name: symfony_nginx
    ports:
      - "8080:80"  # Mapeia a porta 8080 do seu computador para a porta 80 do contêiner
    volumes:
      - ./:/var/www/html
      - ./docker/nginx/nginx.conf:/etc/nginx/conf.d/default.conf # Arquivo de configuração do Nginx
    depends_on:
      - php

  php:
    build:
      context: .
      dockerfile: Dockerfile
    container_name: symfony_php
    volumes:
      - ./:/var/www/html
    # Depende do banco de dados para iniciar
    depends_on:
      - database

  database:
    image: mysql:8.0
    container_name: symfony_mysql
    restart: always
    environment:
      MYSQL_ROOT_PASSWORD: my_secret_password
      MYSQL_DATABASE: my_symfony_db
    volumes:
      - db_data:/var/lib/mysql

volumes:
  db_data: {}
```

**`Dockerfile` (A Receita do PHP)**

```dockerfile
# Use a versão 8.2 do PHP com FPM e Alpine Linux
FROM php:8.2-fpm-alpine

# Instale algumas dependências do sistema
RUN apk add --no-cache git unzip libpng-dev

# Instale as extensões do PHP que o Symfony precisa
RUN docker-php-ext-install pdo_mysql gd zip

# Instale o Composer (o gerenciador de pacotes do PHP)
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Defina o diretório de trabalho padrão dentro do contêiner
WORKDIR /var/www/html
```

**`docker/nginx/nginx.conf` (O manual do Nginx)**

```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/html/public;

    location / {
        try_files $uri /index.php$is_args$args;
    }

    location ~ ^/index\.php(/|$) {
        fastcgi_pass php:9000;
        fastcgi_split_path_info ^(.+\.php)(/.*)$;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        fastcgi_param DOCUMENT_ROOT $realpath_root;
    }

    location ~ /\.ht {
        deny all;
    }
}
```

### 3\. A Instalação do Projeto

Ligue o ambiente Docker e instale o Symfony **dentro do contêiner**.

```bash
# 1. Ligue os contêineres e construa a imagem do PHP
docker-compose up -d --build

# 2. Instale o esqueleto do Symfony dentro do contêiner
docker-compose exec php composer create-project symfony/skeleton .

# 3. Instale o Twig e outras dependências que você precisa
docker-compose exec php composer require twig
```

### 4\. Sincronize com o GitHub

Seu projeto agora está configurado e pronto para o próximo passo. Certifique-se de que os arquivos `composer.json` e `composer.lock` foram gerados e os envie para o seu repositório.

```bash
git add .
git commit -m "Instalacao inicial do Symfony com Docker"
git push origin main
```

-----

## 🚀 Etapa do Outro Desenvolvedor

Esta seção é para quem já tem o projeto clonado.

### 1\. Pré-requisitos

Certifique-se de ter o [Docker](https://www.docker.com/get-started) e o [Git](https://git-scm.com/) instalados.

### 2\. Inicie o "Kit de Cozinha"

Navegue até a pasta do projeto no seu terminal.

```bash
cd seu-projeto-aqui
```

### 3\. Ligue a Cozinha\!

Com um único comando, o Docker fará todo o trabalho de montar o ambiente.

```bash
docker-compose up -d --build
```

  * `up`: Liga os serviços (`php`, `nginx`, `database`).
  * `-d`: Roda em segundo plano.
  * `--build`: Garante que a imagem do contêiner PHP seja construída a partir do `Dockerfile`.

### 4\. Instale os "Ingredientes" (Dependências)

Agora que o ambiente está ligado, use o Composer **dentro do contêiner** para baixar todas as bibliotecas do projeto.

```bash
docker-compose exec php composer install
```

  * O Composer vai ler o `composer.lock` e instalar exatamente as mesmas versões de dependências que o projeto original.

### 5\. Ajustes Finais (Banco de Dados)

Se o projeto usar banco de dados, você pode precisar criar o banco e rodar as migrações:

```bash
# Cria o banco de dados
docker-compose exec php bin/console doctrine:database:create

# Executa as migrações (se houver)
docker-compose exec php bin/console doctrine:migrations:migrate
```

### 6\. Pronto\!

Acesse seu projeto no navegador:

```
http://localhost:8080
```

Seu ambiente de desenvolvimento está pronto, e você pode começar a trabalhar no código sem se preocupar com versões de PHP, Nginx ou MySQL\!
