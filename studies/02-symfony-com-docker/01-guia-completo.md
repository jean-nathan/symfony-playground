# 🎯 Guia Completo: Symfony 6.4 com Docker do Zero

## 📋 **O QUE VAMOS CONSTRUIR**

Imagine que você está montando uma **casa completa** para sua aplicação:
- **Quarto (Container PHP)**: Onde o Symfony "mora" e executa
- **Sala de visitas (Nginx)**: Recebe os visitantes (usuários)
- **Despensa (MySQL)**: Guarda todos os dados
- **Geladeira (Redis)**: Acesso rápido a itens usados frequentemente

---

## 🏗️ **PASSO A PASSO DETALHADO**

### 1️⃣ **PREPARANDO O TERRENO**

```bash
mkdir meu-projeto-symfony && cd meu-projeto-symfony
```

**O que faz:**
- `mkdir meu-projeto-symfony`: Cria uma pasta chamada "meu-projeto-symfony"
- `&&`: Significa "E DEPOIS faça isso"
- `cd meu-projeto-symfony`: Entra dentro da pasta criada

**O que você tem agora:**
```
meu-projeto-symfony/          ← Você está aqui
(pasta vazia)
```

---

### 2️⃣ **CRIANDO A ESTRUTURA DE PASTAS**

```bash
mkdir -p docker/nginx docker/php
```

**O que faz:**
- `mkdir -p`: Cria pastas E subpastas de uma vez
- `docker/nginx`: Pasta para configurações do servidor web
- `docker/php`: Pasta para configurações do PHP

**O que você tem agora:**
```
meu-projeto-symfony/
├── docker/
│   ├── nginx/               ← Configurações do servidor web
│   └── php/                 ← Configurações do PHP
```

---

### 3️⃣ **CRIANDO OS ARQUIVOS DE CONFIGURAÇÃO**

Agora você vai criar 5 arquivos importantes:

#### 📄 **Dockerfile** (Na raiz do projeto)
```dockerfile
FROM php:8.2-fpm
# ↑ Pego uma "caixa" com PHP 8.2 já instalado

RUN apt-get update && apt-get install -y \
    git curl libpng-dev libxml2-dev zip unzip
# ↑ Instalo ferramentas que preciso (como instalar programas no computador)

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# ↑ Copio o Composer (gerenciador de pacotes do PHP) para dentro

WORKDIR /var/www
# ↑ Defino que a pasta de trabalho é /var/www

RUN chown -R www-data:www-data /var/www
# ↑ Dou as permissões corretas para o servidor web

EXPOSE 9000
# ↑ Digo que a porta 9000 estará disponível

CMD ["php-fpm"]
# ↑ Comando que roda quando o container iniciar
```

**FUNÇÃO:** Este arquivo é a "receita" para criar seu container PHP personalizado.

#### 📄 **docker-compose.yml** (Na raiz do projeto)
```yaml
version: '3.8'

services:
  # Container da aplicação PHP/Symfony
  app:
    build:
      context: .              # Usa o Dockerfile desta pasta
      dockerfile: Dockerfile
    container_name: symfony_app
    volumes:
      - ./:/var/www          # Liga sua pasta atual com /var/www do container
    networks:
      - symfony_network      # Conecta na rede interna
    depends_on:
      - db                   # Só inicia depois que o banco subir
      - redis

  # Container do servidor web (Nginx)
  webserver:
    image: nginx:alpine      # Usa imagem pronta do Nginx
    container_name: symfony_nginx
    ports:
      - "9000:80"           # Porta 9000 do seu Mac = porta 80 do container
    volumes:
      - ./:/var/www         # Compartilha arquivos com container PHP
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
    networks:
      - symfony_network
    depends_on:
      - app

  # Container do banco de dados
  db:
    image: mysql:8.0
    container_name: symfony_mysql
    environment:             # Variáveis de configuração
      MYSQL_DATABASE: symfony_db
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_USER: symfony_user
      MYSQL_PASSWORD: symfony_password
    ports:
      - "3307:3306"         # Porta 3307 do Mac = 3306 do container
    volumes:
      - mysql_data:/var/lib/mysql  # Dados persistem mesmo se container for removido
    networks:
      - symfony_network

  # Container do Redis (cache)
  redis:
    image: redis:7-alpine
    container_name: symfony_redis
    ports:
      - "6380:6379"
    networks:
      - symfony_network

networks:
  symfony_network:           # Rede interna para containers conversarem
    driver: bridge

volumes:
  mysql_data:               # Volume para dados do MySQL
    driver: local
```

**FUNÇÃO:** Este é o "maestro" que coordena todos os containers trabalhando juntos.

#### 📄 **docker/nginx/default.conf**
```nginx
server {
    listen 80;                          # Escuta na porta 80
    index index.php index.html;         # Busca por estes arquivos primeiro
    root /var/www/public;               # Pasta raiz dos arquivos web
    
    location ~ \.php$ {                 # Quando alguém pede arquivo .php
        fastcgi_pass app:9000;          # Encaminha para container PHP
        fastcgi_index index.php;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    }
    
    location / {                        # Para todas outras requisições
        try_files $uri $uri/ /index.php?$query_string;  # Tenta arquivo, senão vai pro index.php
    }
}
```

**FUNÇÃO:** Ensina o Nginx como servir seu site e quando chamar o PHP.

#### 📄 **docker/php/local.ini**
```ini
upload_max_filesize = 40M        ; Arquivos até 40MB podem ser enviados
post_max_size = 40M              ; Formulários até 40MB
memory_limit = 256M              ; PHP pode usar até 256MB de RAM
max_execution_time = 300         ; Script pode rodar até 5 minutos
display_errors = On              ; Mostra erros na tela (desenvolvimento)
```

**FUNÇÃO:** Personaliza como o PHP se comporta (limites, configurações).

#### 📄 **.env** (Na raiz do projeto)
```bash
APP_ENV=dev                      # Ambiente de desenvolvimento
APP_SECRET=sua-chave-secreta     # Chave para criptografia

# Conexão com banco (IMPORTANTE: usar porta interna 3306)
DATABASE_URL="mysql://symfony_user:symfony_password@db:3306/symfony_db?serverVersion=8.0"

# Conexão com Redis (IMPORTANTE: usar porta interna 6379)
REDIS_URL=redis://redis:6379
```

**FUNÇÃO:** Guarda configurações secretas e específicas do ambiente.

---

### 4️⃣ **SUBINDO O AMBIENTE DOCKER**

```bash
docker-compose up -d --build
```

**O que cada parte faz:**
- `docker-compose`: Comando principal para gerenciar múltiplos containers
- `up`: "Suba/Inicie" os containers
- `-d`: "Detached" = roda em background (você não fica "preso" no terminal)
- `--build`: "Construa/Reconstrua" as imagens antes de subir

**O que acontece internamente:**
1. 🏗️ Constrói a imagem do container PHP usando seu Dockerfile
2. 📦 Baixa imagens prontas (Nginx, MySQL, Redis)
3. 🌐 Cria rede interna `symfony_network`
4. 💾 Cria volume `mysql_data` para persistir dados
5. 🚀 Inicia todos os 4 containers na ordem correta
6. 🔗 Conecta tudo na rede interna

**Verificar se deu certo:**
```bash
docker-compose ps
```

**Deve mostrar:**
```
NAME               STATUS         PORTS
symfony_app        Up             9000/tcp
symfony_nginx      Up             0.0.0.0:9000->80/tcp  
symfony_mysql      Up             0.0.0.0:3307->3306/tcp
symfony_redis      Up             0.0.0.0:6380->6379/tcp
```

---

### 5️⃣ **CRIANDO O PROJETO SYMFONY**

```bash
docker-compose exec app composer create-project symfony/skeleton:"6.4.*" /tmp/symfony-temp
```

**O que faz:**
- `docker-compose exec app`: "Execute um comando DENTRO do container 'app'"
- `composer create-project`: "Crie um novo projeto usando Composer"
- `symfony/skeleton:"6.4.*"`: "Use a versão 6.4 do Symfony (estrutura mínima)"
- `/tmp/symfony-temp`: "Crie na pasta temporária /tmp/symfony-temp"

**Por que em /tmp primeiro?**
Porque o Composer não consegue criar projeto em pasta que já tem arquivos (nossa pasta /var/www tem os arquivos Docker).

```bash
docker-compose exec app sh -c "mv /tmp/symfony-temp/* /var/www/ && mv /tmp/symfony-temp/.* /var/www/ 2>/dev/null || true"
```

**O que faz:**
- `sh -c`: "Execute este comando no shell"
- `mv /tmp/symfony-temp/* /var/www/`: Move todos os arquivos visíveis
- `&&`: "E depois..."
- `mv /tmp/symfony-temp/.* /var/www/`: Move arquivos ocultos (começam com .)
- `2>/dev/null || true`: "Se der erro, ignore" (alguns arquivos ocultos podem não existir)

```bash
docker-compose exec app rm -rf /tmp/symfony-temp
```

**O que faz:**
- `rm -rf`: "Remove forçadamente"
- `/tmp/symfony-temp`: A pasta temporária (limpeza)

```bash
docker-compose exec app chown -R www-data:www-data /var/www
```

**O que faz:**
- `chown -R`: "Mude o dono recursivamente"
- `www-data:www-data`: "Novo dono é o usuário do servidor web"
- `/var/www`: "De toda a pasta do projeto"

**Por que isso é importante?**
O servidor web precisa ter permissão para ler/escrever os arquivos.

---

### 6️⃣ **INSTALANDO DEPENDÊNCIAS**

```bash
docker-compose exec app composer install
```

**O que faz:**
- Lê o arquivo `composer.json` (criado pelo Symfony)
- Baixa todas as bibliotecas que o Symfony precisa
- Coloca tudo na pasta `vendor/`
- Cria o arquivo `composer.lock` (versões exatas instaladas)

**O que você tem agora:**
```
meu-projeto-symfony/
├── vendor/                  ← Todas as bibliotecas do Symfony
├── src/                     ← Seu código PHP
├── public/                  ← Arquivos públicos (CSS, JS, imagens)
├── config/                  ← Configurações do Symfony
├── composer.json            ← Lista de dependências
├── composer.lock            ← Versões exatas instaladas
└── ... (outros arquivos Symfony)
```

---

### 7️⃣ **CONFIGURANDO BANCO DE DADOS**

```bash
docker-compose exec app php bin/console doctrine:database:create
```

**O que faz:**
- `php bin/console`: "Execute um comando do Symfony"
- `doctrine:database:create`: "Crie o banco de dados"
- Lê as configurações do `.env`
- Conecta no MySQL e cria o banco `symfony_db`

**Se der erro "database exists":** Está tudo certo, o banco já foi criado!

---

## 🌐 **TESTANDO SE FUNCIONOU**

Abra seu navegador e vá para: **http://localhost:9000**

**Você deve ver:** A página de boas-vindas do Symfony! 🎉

---

## 🔄 **FLUXO DE TRABALHO DIÁRIO**

### **Iniciar trabalho:**
```bash
cd meu-projeto-symfony
docker-compose up -d
```

### **Parar trabalho:**
```bash
docker-compose down
```

### **Ver logs se algo der errado:**
```bash
docker-compose logs -f app
```

### **Executar comandos Symfony:**
```bash
# Criar um controller
docker-compose exec app php bin/console make:controller HomeController

# Limpar cache
docker-compose exec app php bin/console cache:clear

# Ver todas as rotas
docker-compose exec app php bin/console debug:router
```

### **Instalar pacotes:**
```bash
# Por exemplo, instalar o Twig
docker-compose exec app composer require symfony/twig-bundle
```

---

## 🗂️ **ESTRUTURA FINAL DO PROJETO**

```
meu-projeto-symfony/
├── docker/                          ← Configurações Docker
│   ├── nginx/
│   │   └── default.conf            ← Como Nginx serve o site
│   └── php/
│       └── local.ini               ← Configurações PHP
├── vendor/                          ← Bibliotecas (NÃO editar)
├── src/                            ← SEU CÓDIGO aqui!
│   ├── Controller/                 ← Controllers
│   ├── Entity/                     ← Modelos/Entidades
│   └── ...
├── public/                         ← Arquivos públicos
│   ├── index.php                   ← Ponto de entrada
│   └── ...
├── config/                         ← Configurações Symfony
├── templates/                      ← Templates Twig
├── docker-compose.yml              ← Orquestrador containers
├── Dockerfile                      ← Receita container PHP
├── .env                           ← Configurações ambiente
├── composer.json                  ← Dependências
└── README.md                      ← Documentação
```

---

## 🎯 **PARA USAR COMO TEMPLATE**

### **1. Salve esta estrutura como template:**
```bash
# Copie toda a pasta para usar como base
cp -r meu-projeto-symfony/ template-symfony-docker/
cd template-symfony-docker/

# Remova arquivos específicos do projeto anterior
rm -rf vendor/ var/ .env.local
```

### **2. Para novo projeto:**
```bash
# Copie o template
cp -r template-symfony-docker/ meu-novo-projeto/
cd meu-novo-projeto/

# Suba o ambiente
docker-compose up -d --build

# Instale dependências
docker-compose exec app composer install

# Configure banco
docker-compose exec app php bin/console doctrine:database:create
```

---

## ⚠️ **PONTOS IMPORTANTES**

### **URLs de Conexão:**
- **No .env (Symfony)**: Use nomes dos containers (`db:3306`, `redis:6379`)
- **Do seu Mac**: Use localhost (`localhost:3307`, `localhost:6380`)

### **Volumes:**
- Seus arquivos ficam sincronizados automaticamente
- Mudanças no código aparecem imediatamente
- Dados do MySQL persistem mesmo se parar containers

### **Rede Interna:**
- Containers conversam pelos nomes (`app`, `db`, `redis`)
- Não precisam das portas externas para conversar entre si

### **Comandos sempre dentro do container:**
```bash
# ✅ CORRETO
docker-compose exec app composer require symfony/mailer

# ❌ ERRADO (não funciona se não tiver PHP local)
composer require symfony/mailer
```

---

## 🚀 **PRONTO!**

Agora você tem:
- ✅ Ambiente completo Symfony 6.4
- ✅ PHP 8.2 + Nginx + MySQL + Redis
- ✅ Template reutilizável para qualquer projeto
- ✅ Conhecimento para resolver problemas
- ✅ Fluxo de trabalho profissional

**Este setup funciona igual em qualquer Mac e pode ser facilmente adaptado para Linux/Windows!** 🎯
