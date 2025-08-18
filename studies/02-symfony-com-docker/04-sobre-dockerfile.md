# Docker por Baixo dos Panos - Como Funciona na Prática

## 🏗️ Como o Docker Constrói sua Imagem (Layer por Layer)

Imagine que cada linha do Dockerfile cria uma "camada" como se fosse um sanduíche:

```
┌─────────────────────────┐
│ CMD ["php-fpm"]         │ ← Camada 8: Como iniciar
├─────────────────────────┤
│ EXPOSE 9000            │ ← Camada 7: Documentação
├─────────────────────────┤
│ RUN chown -R www-data  │ ← Camada 6: Permissões
├─────────────────────────┤
│ WORKDIR /var/www       │ ← Camada 5: Diretório
├─────────────────────────┤
│ COPY composer          │ ← Camada 4: Ferramentas
├─────────────────────────┤
│ RUN docker-php-ext...  │ ← Camada 3: Extensões PHP
├─────────────────────────┤
│ RUN apt-get install... │ ← Camada 2: Dependências
├─────────────────────────┤
│ FROM php:8.2-fpm       │ ← Camada 1: Base
└─────────────────────────┘
```

## 🔍 O Que Acontece em Cada Comando

### 1. FROM php:8.2-fmp - "Escolhendo a Base"
```bash
# Por baixo dos panos:
# Docker baixa uma imagem que já tem:
# - Sistema operacional Linux (Debian)
# - PHP 8.2 compilado e instalado
# - PHP-FPM configurado
# - Usuário www-data criado
```

**Analogia:** É como comprar um computador que já vem com sistema operacional instalado, em vez de montar tudo do zero.

### 2. RUN apt-get update - "Atualizando a Lista de Programas"
```bash
# O que realmente acontece:
apt-get update
# ↓
# Conecta nos servidores Debian
# Baixa lista atual de pacotes disponíveis
# Atualiza o "catálogo" local
```

**Analogia:** É como atualizar a lista de apps disponíveis na Play Store.

### 3. RUN apt-get install - "Instalando Ferramentas"
```bash
# Para cada pacote, o Docker:
# 1. Baixa o arquivo .deb
# 2. Descompacta
# 3. Copia arquivos para locais corretos
# 4. Configura dependências
# 5. Registra no sistema

# Exemplo prático:
apt-get install -y git
# ↓
# Baixa git_2.30.deb
# Instala em /usr/bin/git
# Cria links simbólicos
# Registra no PATH
```

### 4. docker-php-ext-install - "Compilando Extensões PHP"
```bash
# O que acontece internamente:
docker-php-ext-install pdo_mysql
# ↓
# 1. Baixa código fonte da extensão
# 2. Compila usando gcc (como fazer um programa)
# 3. Cria arquivo pdo_mysql.so
# 4. Copia para /usr/local/lib/php/extensions/
# 5. Adiciona 'extension=pdo_mysql' no php.ini
```

**Analogia:** É como instalar um plugin no seu editor de texto - adiciona funcionalidades novas.

### 5. COPY --from=composer - "Copiando de Outra Imagem"
```bash
# Docker internamente:
# 1. Puxa temporariamente a imagem 'composer:latest'
# 2. Localiza o arquivo /usr/bin/composer nessa imagem
# 3. Copia esse arquivo para a nossa imagem
# 4. Descarta a imagem temporária
```

**Analogia:** É como pedir emprestado uma ferramenta de um vizinho e fazer uma cópia para você.

### 6. WORKDIR /var/www - "Mudando de Pasta"
```bash
# Internamente executa:
mkdir -p /var/www  # Cria a pasta se não existir
cd /var/www        # Muda para essa pasta
# Define como diretório padrão para próximos comandos
```

### 7. RUN chown -R - "Mudando Dono dos Arquivos"
```bash
# Sistema de arquivos Linux:
# Cada arquivo tem: dono, grupo, permissões
chown -R www-data:www-data /var/www
# ↓
# Percorre recursivamente todos arquivos/pastas
# Muda dono para: usuário 'www-data', grupo 'www-data'
# www-data = usuário criado especificamente para servidores web
```

## 🧠 Conceitos Fundamentais

### Sistema de Camadas (Layers)
```bash
# Cada RUN/COPY/ADD cria uma nova camada
# Docker salva apenas as DIFERENÇAS entre camadas
# Se uma camada não mudou, reutiliza do cache

# Exemplo:
RUN apt-get update           # Camada A (50MB)
RUN apt-get install git      # Camada B (+20MB) = 70MB total
RUN apt-get install curl     # Camada C (+5MB)  = 75MB total
```

### Cache do Docker
```bash
# Docker "lembra" de cada camada:
# Se você mudar apenas a última linha do Dockerfile,
# as camadas anteriores são reutilizadas (muito mais rápido!)

# Por isso a ordem importa:
# ✅ Coisas que mudam pouco no início
# ❌ Coisas que mudam muito no início
```

### Namespaces e Isolamento
```bash
# Cada container é como uma "bolha" isolada:
# - Tem seu próprio sistema de arquivos
# - Tem seus próprios processos
# - Tem sua própria rede (por padrão)
# - MAS compartilha o kernel do host
```

## 🔧 Comandos Principais e O Que Fazem

### FROM - "Escolher Base"
- Baixa imagem do Docker Hub
- Pode ser sistema operacional puro (ubuntu, debian)
- Ou imagem especializada (php:8.2-fpm, node:18, nginx)

### RUN - "Executar Comando Durante Build"
- Executa comando no shell do container
- Cria uma nova camada com as mudanças
- Use para instalar pacotes, compilar código

### COPY/ADD - "Copiar Arquivos"
- COPY: copia arquivos do seu computador para o container
- ADD: faz a mesma coisa + pode baixar URLs + descompactar

### WORKDIR - "Mudar Diretório"
- Como fazer 'cd' no terminal
- Afeta todos os próximos comandos

### EXPOSE - "Documentar Porta"
- NÃO abre a porta automaticamente
- Apenas documenta qual porta a aplicação usa
- Para abrir de verdade: `docker run -p 8080:9000`

### CMD/ENTRYPOINT - "Como Iniciar"
- CMD: comando padrão (pode ser sobrescrito)
- ENTRYPOINT: comando fixo (sempre executa)

## 🎯 Exemplo Prático: "Traduzindo" seu Dockerfile

```dockerfile
FROM php:8.2-fpm
# "Pegue um computador com PHP 8.2 e servidor web já instalados"

RUN apt-get update && apt-get install -y git curl...
# "Atualize a lista de programas e instale ferramentas extras"

RUN docker-php-ext-install pdo pdo_mysql...
# "Compile e instale plugins PHP para banco de dados, imagens, etc"

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
# "Pegue emprestado o Composer de outro container e copie aqui"

WORKDIR /var/www
# "Vá para a pasta /var/www e fique lá"

RUN chown -R www-data:www-data /var/www
# "Mude o dono de todos os arquivos para o usuário do servidor web"

EXPOSE 9000
# "Este container usa a porta 9000 (só documentação)"

CMD ["php-fpm"]
# "Quando iniciar o container, execute o servidor PHP"
```

## 🚀 Dicas para Entender Melhor

1. **Teste cada comando separadamente:**
```bash
# Entre num container e teste:
docker run -it php:8.2-fpm bash
apt-get update
apt-get install -y git
# Veja o que acontece!
```

2. **Use docker history para ver as camadas:**
```bash
docker history sua-imagem
# Mostra cada camada e seu tamanho
```

3. **Inspecione imagens:**
```bash
docker inspect php:8.2-fmp
# Mostra toda a configuração da imagem
```

4. **Entenda que é como um "receita de bolo":**
- Cada linha é um passo
- A ordem importa
- Se um passo falha, para tudo
- Cada passo constrói sobre o anterior
