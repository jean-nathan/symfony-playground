# Docker Compose Explicado Linha por Linha

Este documento explica **detalhadamente** cada linha do arquivo `docker-compose.yml`, mostrando **o porquê** de cada configuração e **como funciona** internamente.

## Estrutura da Aplicação

O projeto usa 4 containers que trabalham juntos:
- **app**: Processa PHP/Symfony  
- **webserver**: Recebe requisições HTTP
- **db**: Armazena dados permanentemente
- **redis**: Cache rápido em memória

---

## Análise Detalhada - Linha por Linha

### 1. Versão do Docker Compose

```yaml
version: '3.8'
```

**O que faz**: Define qual versão do formato Docker Compose usar.

**Por que usar 3.8**:
- Suporta recursos modernos como `init`, `scale`, volumes externos
- Compatible com Docker Engine 19.03.0+
- Permite usar sintaxe mais limpa para volumes e redes

**Como funciona**: O Docker lê esta linha primeiro para saber como interpretar o resto do arquivo. Se usar sintaxe incompatível, gerará erro.

---

### 2. Início da Seção de Serviços

```yaml
services:
```

**O que faz**: Marca o início da definição de todos os containers.

**Por que é necessário**: É uma seção obrigatória onde cada container é definido como um "serviço".

**Como funciona**: Tudo que estiver indentado abaixo será considerado um serviço (container) diferente.

---

## SERVIÇO APP - Container Principal

### Definição do Serviço

```yaml
  app:
```

**O que faz**: Cria um serviço chamado "app".

**Por que esse nome**: Nome interno usado para comunicação entre containers. Outros containers podem acessar este usando `http://app:porta`.

**Como funciona**: O Docker cria um hostname interno chamado "app" na rede.

---

### Build Personalizado

```yaml
    build:
      context: .
      dockerfile: Dockerfile
```

**O que faz**: Constrói uma imagem customizada ao invés de usar uma pronta.

**Por que fazer build próprio**:
- Instalar PHP, Symfony, extensões específicas
- Configurar ambiente exatamente como precisa
- Incluir dependências da aplicação (Composer)

**Como funciona**:
- `context: .` → Docker usa pasta atual como base (pode acessar arquivos locais)
- `dockerfile: Dockerfile` → Procura arquivo "Dockerfile" na raiz
- Durante `docker-compose up`, executa `docker build` automaticamente

---

### Nome do Container

```yaml
    container_name: symfony_app
```

**O que faz**: Define nome específico para o container.

**Por que usar**: 
- Facilita debug (`docker logs symfony_app`)
- Evita nomes gerados automaticamente (tipo `projeto_app_1`)
- Permite conectar outros projetos a este container

**Como funciona**: Docker usa este nome no `docker ps` e comandos CLI.

---

### Política de Restart

```yaml
    restart: unless-stopped
```

**O que faz**: Define quando o container deve reiniciar.

**Opções disponíveis**:
- `no` → Nunca reinicia
- `always` → Sempre reinicia (mesmo após reboot do servidor)
- `on-failure` → Só reinicia se der erro
- `unless-stopped` → Reinicia sempre, exceto se parado manualmente

**Por que usar `unless-stopped`**: 
- Se o servidor reiniciar, container sobe automaticamente
- Se você parar manualmente (`docker stop`), não sobe sozinho
- Ideal para desenvolvimento onde você quer controle manual

**Como funciona**: Docker daemon monitora o container e executa ação baseada no exit code.

---

### Diretório de Trabalho

```yaml
    working_dir: /var/www
```

**O que faz**: Define pasta onde comandos serão executados dentro do container.

**Por que `/var/www`**:
- Padrão para aplicações web no Linux
- Nginx e Apache esperam código nesta pasta
- Facilita scripts que esperam esta estrutura

**Como funciona**: Quando você executa `docker exec symfony_app ls`, será como executar `cd /var/www && ls`.

---

### Volumes (Compartilhamento de Arquivos)

```yaml
    volumes:
      - ./:/var/www
      - ./docker/php/local.ini:/usr/local/etc/php/conf.d/local.ini
```

**Primeira linha: `./:/var/www`**

**O que faz**: Compartilha código fonte entre host e container.

**Por que é essencial**:
- Permite editar código no VSCode e ver mudanças imediatamente
- Sem isso, teria que reconstruir imagem a cada mudança
- Container "vê" exatamente os mesmos arquivos do seu computador

**Como funciona**: 
- `./` → Pasta atual no seu computador
- `/var/www` → Pasta dentro do container
- Docker monta um "bridge" entre as duas

**Segunda linha: `./docker/php/local.ini:/usr/local/etc/php/conf.d/local.ini`**

**O que faz**: Substitui configurações padrão do PHP.

**Por que customizar PHP**:
- Aumentar `memory_limit` para aplicações pesadas
- Configurar `upload_max_filesize` para uploads
- Habilitar extensões específicas do Symfony

**Como funciona**: Docker substitui o arquivo padrão pelo seu arquivo customizado.

---

### Rede de Comunicação

```yaml
    networks:
      - symfony_network
```

**O que faz**: Conecta container à rede personalizada.

**Por que criar rede própria**:
- Isolamento: só containers desta aplicação podem se comunicar
- Resolução DNS automática (app pode acessar db pelo nome)
- Controle sobre quem acessa o quê

**Como funciona**: Docker cria uma rede virtual onde containers podem se "falar" usando nomes.

---

### Dependências de Inicialização

```yaml
    depends_on:
      - db
      - redis
```

**O que faz**: Define ordem de inicialização dos containers.

**Por que é importante**:
- Symfony precisa conectar ao banco na inicialização
- Se banco não estiver pronto, aplicação falha
- Redis pode ser necessário para sessões

**Como funciona**: 
- Docker inicia primeiro `db` e `redis`
- Só depois inicia `app`
- **IMPORTANTE**: Não espera serviço estar "pronto", só "iniciado"

**Limitação**: `depends_on` não garante que MySQL já aceitou conexões, só que container iniciou.

---

## SERVIÇO WEBSERVER - Nginx

### Definição e Imagem

```yaml
  webserver:
    image: nginx:alpine
```

**O que faz**: Usa imagem pronta do Nginx baseada no Alpine Linux.

**Por que Nginx**:
- Extremamente rápido para servir arquivos estáticos (CSS, JS, imagens)
- Usa menos memória que Apache
- Excelente para fazer proxy para PHP-FPM

**Por que Alpine**:
- Imagem 10x menor (5MB vs 50MB)
- Mais segura (menos software = menos vulnerabilidades)
- Inicializa mais rápido

**Como funciona**: Docker baixa imagem do Docker Hub se não existir localmente.

---

### Mapeamento de Portas

```yaml
    ports:
      - "9000:80"
```

**O que faz**: Mapeia porta do seu computador para porta do container.

**Por que porta 9000**:
- Evita conflito se você já tem algo na porta 80
- Comum usar portas altas para desenvolvimento
- Facilita rodar vários projetos simultaneamente

**Como funciona**:
- `9000` → Porta no seu computador (host)
- `80` → Porta dentro do container
- Quando você acessa `localhost:9000`, Docker redireciona para porta 80 do Nginx

---

### Volumes do Nginx

```yaml
    volumes:
      - ./:/var/www
      - ./docker/nginx/default.conf:/etc/nginx/conf.d/default.conf
```

**Primeiro volume: `./:/var/www`**

**Por que Nginx precisa do código**:
- Servir arquivos estáticos diretamente (CSS, JS, imagens)
- Verificar se arquivo existe antes de repassar para PHP
- Melhor performance (não passa pelo PHP para arquivos simples)

**Segundo volume: Configuração customizada**

**O que está no `default.conf`**:
```nginx
server {
    listen 80;
    server_name localhost;
    root /var/www/public;  # Pasta pública do Symfony
    
    location / {
        try_files $uri /index.php$is_args$args;  # Se arquivo não existe, vai pro Symfony
    }
    
    location ~ \.php$ {
        fastcgi_pass app:9000;  # Repassa PHP para container "app"
        fastcgi_index index.php;
        # ... mais configurações
    }
}
```

**Como funciona o fluxo**:
1. Nginx recebe requisição HTTP
2. Se é arquivo estático (.css, .js), serve diretamente
3. Se é PHP, repassa para container `app` na porta 9000
4. Container `app` processa PHP e retorna resultado
5. Nginx retorna resposta para usuário

---

## SERVIÇO DB - MySQL

### Imagem e Versão

```yaml
  db:
    image: mysql:8.0
```

**Por que MySQL 8.0**:
- Performance muito melhor que 5.7
- JSON nativo (útil para campos JSON do Symfony)
- Window functions e CTEs
- Melhor suporte a UTF8MB4

**Alternativas**: `mysql:5.7` se precisar compatibilidade, `mariadb:10.6` como alternativa open source.

---

### Variáveis de Ambiente

```yaml
    environment:
      MYSQL_DATABASE: symfony_db
      MYSQL_ROOT_PASSWORD: root_password
      MYSQL_USER: symfony_user
      MYSQL_PASSWORD: symfony_password
```

**Como funciona a inicialização do MySQL**:

1. Container inicia e verifica se `/var/lib/mysql` está vazio
2. Se estiver vazio, é primeira execução
3. MySQL lê variáveis de ambiente e executa:
   - `CREATE DATABASE symfony_db`
   - `CREATE USER 'symfony_user'@'%' IDENTIFIED BY 'symfony_password'`
   - `GRANT ALL PRIVILEGES ON symfony_db.* TO 'symfony_user'@'%'`

**Por que criar usuário específico**:
- Root tem poder demais (pode acessar qualquer banco)
- Princípio do menor privilégio
- Se credenciais vazarem, dano é limitado

**Onde usar no Symfony** (`.env`):
```env
DATABASE_URL=mysql://symfony_user:symfony_password@db:3306/symfony_db
```

---

### Mapeamento de Porta

```yaml
    ports:
      - "3307:3306"
```

**Por que porta 3307**:
- Evita conflito se você tem MySQL instalado localmente na 3306
- Permite acessar banco com ferramentas externas (MySQL Workbench, phpMyAdmin)

**Como conectar externamente**:
- Host: `localhost:3307`
- Usuário: `symfony_user`
- Senha: `symfony_password`

---

### Volume Persistente

```yaml
    volumes:
      - mysql_data:/var/lib/mysql
```

**O que acontece sem volume**:
- Dados ficam dentro do container
- `docker-compose down` → todos os dados são perdidos
- Precisaria recriar banco toda vez

**Com volume nomeado**:
- Dados ficam no host Docker, fora do container
- Container pode ser deletado, dados permanecem
- Próxima execução reutiliza dados existentes

**Como funciona**:
- Docker cria pasta no host (geralmente `/var/lib/docker/volumes/`)
- Monta esta pasta como `/var/lib/mysql` no container
- MySQL salva bancos, tabelas, índices nesta pasta

---

## SERVIÇO REDIS

### Imagem Alpine

```yaml
  redis:
    image: redis:7-alpine
```

**Por que Redis 7**:
- Estruturas de dados mais avançadas
- Melhor performance
- Redis Functions (JavaScript dentro do Redis)

**Como Symfony usa Redis**:
1. **Cache**: Armazenar resultados de consultas pesadas
2. **Sessões**: Substituir sessões de arquivo (mais rápido, funciona com múltiplos servidores)
3. **Filas**: Processar jobs em background

---

### Porta Customizada

```yaml
    ports:
      - "6380:6379"
```

**Por que 6380**: Evita conflito com Redis local.

**Configuração no Symfony**:
```yaml
# config/packages/cache.yaml
framework:
    cache:
        app: cache.adapter.redis
        default_redis_provider: redis://redis:6379
```

---

## CONFIGURAÇÕES GLOBAIS

### Rede Personalizada

```yaml
networks:
  symfony_network:
    driver: bridge
```

**O que é uma rede bridge**:
- Cria rede virtual isolada
- Containers podem se comunicar por nome
- Isolados de outros projetos Docker

**Como funciona DNS interno**:
- Container `app` pode acessar `http://db:3306`
- Container `webserver` pode acessar `http://app:9000`
- Docker mantém tabela DNS interna

**Vantagens vs rede padrão**:
- Isolamento completo
- Controle sobre quem comunica com quem
- Nomes previsíveis

---

### Volume Persistente

```yaml
volumes:
  mysql_data:
    driver: local
```

**Driver local**: Armazena dados no filesystem do host Docker.

**Onde ficam os dados**:
- Linux: `/var/lib/docker/volumes/mysql_data/_data/`
- Windows: `\\wsl$\docker-desktop-data\version-pack-data\community\docker\volumes\`
- macOS: `~/Library/Containers/com.docker.docker/Data/vms/0/`

**Comandos úteis**:
```bash
# Ver volumes
docker volume ls

# Inspecionar volume
docker volume inspect mysql_data

# Backup
docker run --rm -v mysql_data:/data -v $(pwd):/backup alpine tar czf /backup/backup.tar.gz /data

# Restore
docker run --rm -v mysql_data:/data -v $(pwd):/backup alpine tar xzf /backup/backup.tar.gz -C /
```

---

## Fluxo Completo de Uma Requisição

1. **Usuário acessa** `http://localhost:9000/produto/123`

2. **Docker** redireciona para porta 80 do container `webserver`

3. **Nginx** recebe requisição:
   - Verifica se existe `/var/www/public/produto/123` (arquivo estático)
   - Não existe, aplica `try_files`
   - Redireciona para `/index.php?request=/produto/123`

4. **Nginx** repassa para **PHP-FPM**:
   - `fastcgi_pass app:9000` → envia para container `app`
   - Container `app` executa PHP/Symfony

5. **Symfony** processa:
   - Router identifica controller
   - Controller pode consultar banco: `mysql://symfony_user@db:3306/symfony_db`
   - Pode usar cache: `redis://redis:6379`

6. **Resposta volta**:
   - Symfony → PHP-FPM → Nginx → Docker → Usuário

---

## Comandos Essenciais

```bash
# Subir toda a stack
docker-compose up -d

# Ver logs em tempo real
docker-compose logs -f app

# Executar comandos Symfony
docker-compose exec app php bin/console doctrine:migrations:migrate

# Conectar ao banco
docker-compose exec db mysql -u symfony_user -p symfony_db

# Acessar Redis CLI
docker-compose exec redis redis-cli

# Reconstruir apenas um serviço
docker-compose build app
docker-compose up -d app

# Parar tudo e remover containers
docker-compose down

# Parar e remover volumes (CUIDADO: apaga dados!)
docker-compose down -v
```

---

## Problemas Comuns e Soluções

### Container app não conecta ao banco

**Problema**: `SQLSTATE[HY000] [2002] Connection refused`

**Causa**: Container `app` tenta conectar antes do MySQL estar pronto.

**Solução**: Usar `dockerize` ou `wait-for-it` no Dockerfile:
```dockerfile
RUN apt-get update && apt-get install -y wait-for-it
CMD wait-for-it db:3306 -- php-fpm
```

### Permissões de arquivo

**Problema**: `Permission denied` ao escrever cache/logs.

**Causa**: Container roda como root, arquivos ficam com owner errado.

**Solução**: Configurar USER no Dockerfile:
```dockerfile
RUN useradd -u 1000 -ms /bin/bash symfony
USER symfony
```

### Performance lenta no Windows/macOS

**Problema**: Volumes são lentos em sistemas não-Linux.

**Solução**: Usar volumes nomeados para pastas que mudam muito:
```yaml
volumes:
  - symfony_cache:/var/www/var/cache
  - symfony_logs:/var/www/var/log
```

