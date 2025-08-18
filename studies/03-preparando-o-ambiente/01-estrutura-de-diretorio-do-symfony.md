# Guia Completo - Estrutura de Diretórios Symfony

Este documento apresenta uma visão detalhada da estrutura de pastas e arquivos em um projeto Symfony, explicando o propósito, funcionamento e importância de cada componente.

## 📁 Estrutura de Diretórios

### 1. Pasta `bin/`
**O Centro de Comandos da Aplicação**

A pasta `bin` é como a "sala de controle" do seu projeto Symfony. É aqui que ficam os executáveis e scripts que controlam diferentes aspectos da aplicação.

**Principais características:**
- Contém a biblioteca principal do Symfony
- Local dos comandos de linha de comando (CLI)
- Ponto de entrada para operações administrativas

**Comando principal:**
```bash
bin/console
```

**Analogia:** Imagine a pasta `bin` como o painel de controle de um avião. Assim como um piloto usa diferentes botões e comandos para controlar a aeronave, você usa os comandos em `bin/console` para controlar sua aplicação Symfony.

**Funcionamento interno:**
- O arquivo `console` é um script PHP que carrega o framework
- Ele registra todos os comandos disponíveis (cache:clear, doctrine:migrations, etc.)
- Quando você executa `bin/console [comando]`, ele processa a requisição e executa a lógica correspondente

**Por que é necessário:** Sem esta pasta, você não teria como executar comandos essenciais como limpeza de cache, criação de entidades, execução de migrações de banco de dados, entre outros.

---

### 2. Pasta `config/`
**O DNA da Aplicação**

A pasta `config` contém toda a configuração que define como sua aplicação se comporta. É onde você "ensina" ao Symfony como ele deve funcionar.

**Principais características:**
- Arquivos de configuração em YAML, XML ou PHP
- Configurações por ambiente (dev, prod, test)
- Definições de serviços, roteamento, segurança

**Estrutura típica:**
```
config/
├── packages/          # Configurações de bundles/pacotes
├── routes/           # Definições de rotas
├── services.yaml     # Configuração de serviços
├── packages.yaml     # Configurações gerais
└── bundles.php      # Registro de bundles
```

**Analogia:** É como o manual de instruções de um eletrodoméstico complexo. Assim como você configura sua TV para receber os canais corretos, a pasta `config` diz ao Symfony quais bancos de dados usar, como processar requisições, quais rotas existem, etc.

**Funcionamento interno:**
- Durante o boot da aplicação, o Symfony lê esses arquivos
- Eles são compilados em uma estrutura otimizada no cache
- As configurações determinam como os serviços são instanciados e conectados

**Por que é necessário:** Sem configuração adequada, o Symfony não saberia como conectar ao banco de dados, quais rotas responder, como autenticar usuários, ou como processar requisições.

---

### 3. Pasta `public/`
**A Vitrine da Aplicação**

A pasta `public` é o único diretório que deve ser acessível pelo servidor web. É a "porta de entrada" pública da sua aplicação.

**Principais características:**
- Único diretório exposto ao servidor web
- Contém assets estáticos (CSS, JS, imagens)
- Contém o `index.php` (front controller)

**Arquivo `index.php` detalhado:**
```php
<?php

use App\Kernel;

require_once dirname(__DIR__).'/vendor/autoload_runtime.php';

return function (array $context) {
    return new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG']);
};
```

**Análise linha por linha:**
1. `use App\Kernel;` - Importa a classe principal da aplicação
2. `require_once dirname(__DIR__).'/vendor/autoload_runtime.php';` - Carrega o autoloader do Composer que gerencia todas as classes
3. `return function (array $context)` - Retorna uma função que será executada pelo runtime do Symfony
4. `new Kernel($context['APP_ENV'], (bool) $context['APP_DEBUG'])` - Cria uma nova instância do kernel com as variáveis de ambiente

**Analogia:** É como a recepção de um grande hotel. Todo visitante (requisição HTTP) deve passar por ali primeiro. A recepção (index.php) recebe o visitante, verifica suas credenciais (variáveis de ambiente) e direciona para o local apropriado (controladores).

**Funcionamento interno:**
- Servidor web (Apache/Nginx) direciona todas as requisições para `public/index.php`
- O arquivo carrega o autoloader e cria uma instância do Kernel
- O Kernel processa a requisição e retorna uma resposta
- Assets estáticos são servidos diretamente pelo servidor web

**Por que é necessário:** É o ponto de entrada único que garante segurança (outros diretórios ficam inacessíveis) e centraliza o processamento de todas as requisições.

---

### 4. Pasta `src/`
**O Coração da Aplicação**

A pasta `src` é onde você escreve o código específico da sua aplicação. É aqui que a mágica acontece!

**Principais características:**
- Contém todo o código PHP personalizado
- Organizado seguindo a arquitetura MVC
- Namespace padrão: `App\`

**Estrutura típica:**
```
src/
├── Controller/      # Controladores (recebem requisições)
├── Entity/         # Entidades (modelos de dados)
├── Repository/     # Repositories (acesso a dados)
├── Service/        # Serviços (lógica de negócio)
├── Form/          # Classes de formulários
├── EventListener/ # Ouvintes de eventos
└── Kernel.php     # Kernel da aplicação
```

**Analogia:** É como a cozinha de um restaurante. Os controladores são os garçons que recebem os pedidos, as entidades são os ingredientes organizados, os repositories são os armários onde você busca os ingredientes, e os serviços são os chefs que preparam os pratos.

**Funcionamento interno:**
- Controladores processam requisições HTTP e retornam respostas
- Entidades representam dados e suas regras de negócio
- Repositories abstraem o acesso ao banco de dados
- Serviços contêm lógica complexa e reutilizável

**Por que é necessário:** É onde você implementa a funcionalidade específica da sua aplicação. Sem esta pasta, você teria apenas um framework vazio.

---

### 5. Pasta `var/`
**O Depósito de Trabalho Interno**

A pasta `var` é o espaço de trabalho interno do Symfony, onde ele armazena arquivos temporários e logs.

**Subpastas principais:**

#### `var/cache/`
**O Acelerador da Aplicação**

- **Função:** Armazena versões compiladas e otimizadas de configurações, templates e outras estruturas
- **Benefício:** Melhora drasticamente a performance evitando reprocessamento

**Analogia:** É como a memória RAM do seu computador. Informações frequentemente acessadas ficam "prontas para uso" para acelerar o sistema.

**Funcionamento interno:**
- Symfony compila configurações YAML em arrays PHP otimizados
- Templates Twig são compilados em classes PHP
- Metadados de entidades são processados e armazenados
- Em produção, o cache raramente é limpo; em desenvolvimento, é limpo automaticamente quando detecta mudanças

#### `var/log/`
**O Diário da Aplicação**

- **Função:** Registra todas as atividades, erros, avisos e informações de debug
- **Estrutura:** Arquivos separados por ambiente e data

**Tipos de logs típicos:**
```
var/log/
├── dev.log        # Logs do ambiente de desenvolvimento
├── prod.log       # Logs do ambiente de produção
└── test.log       # Logs do ambiente de testes
```

**Analogia:** É como a caixa preta de um avião. Registra tudo que acontece para que, se algo der errado, você possa investigar e entender o que causou o problema.

**Por que é necessário:** Cache acelera a aplicação drasticamente, e logs são essenciais para debugging e monitoramento em produção.

---

### 6. Pasta `vendor/`
**A Biblioteca Externa**

A pasta `vendor` contém todas as dependências de terceiros gerenciadas pelo Composer.

**Principais características:**
- Gerenciada automaticamente pelo Composer
- Contém o framework Symfony e outras bibliotecas
- Não deve ser versionada no Git
- Regenerada a cada instalação

**Estrutura típica:**
```
vendor/
├── symfony/           # Framework Symfony
├── doctrine/          # ORM Doctrine
├── twig/             # Motor de templates
├── monolog/          # Sistema de logs
├── autoload.php      # Autoloader principal
└── composer/         # Metadados do Composer
```

**Analogia:** É como uma grande biblioteca pública. Você não precisa escrever todos os livros (código) do zero - pode usar livros (bibliotecas) que outros autores já escreveram e testaram.

**Funcionamento interno:**
- Composer analisa o `composer.json` e resolve dependências
- Baixa e instala pacotes de repositórios como Packagist
- Gera um autoloader que permite usar classes sem `require` manual
- Gerencia versões e compatibilidades automaticamente

**Por que é necessário:** Permite reutilizar código testado e maduro, evitando reinventar a roda e focando na lógica específica da sua aplicação.

---

## 📄 Arquivos do Diretório Raiz

### 7. Arquivo `.env`
**O Configurador de Ambiente**

Contém variáveis de ambiente que controlam comportamentos específicos da aplicação.

**Variáveis típicas:**
```env
APP_ENV=dev                          # Ambiente (dev/prod/test)
APP_DEBUG=true                       # Mode debug ativo/inativo
DATABASE_URL=mysql://user:pass@host  # String de conexão do banco
MAILER_DSN=smtp://localhost:25       # Configuração de email
```

**Analogia:** É como as configurações do seu smartphone. Você pode definir se quer modo escuro, quais notificações receber, qual idioma usar, etc. O `.env` define essas "preferências" para sua aplicação.

**Funcionamento interno:**
- Carregado durante o boot da aplicação
- Valores são acessíveis via `$_ENV` e `getenv()`
- Podem ser sobrescritos por arquivos `.env.local` ou variáveis do sistema
- Diferentes arquivos para diferentes ambientes (.env.dev, .env.prod)

**Por que é necessário:** Permite configurar a aplicação sem alterar código, facilitando deploy em diferentes ambientes.

---

### 8. Arquivo `composer.json`
**O Manifesto do Projeto**

Define metadados do projeto e suas dependências.

**Estrutura típica:**
```json
{
    "name": "meu-projeto/symfony-app",
    "type": "project",
    "require": {
        "symfony/framework-bundle": "^6.0",
        "doctrine/orm": "^2.12"
    },
    "require-dev": {
        "phpunit/phpunit": "^9.5"
    },
    "autoload": {
        "psr-4": {
            "App\\": "src/"
        }
    }
}
```

**Seções importantes:**
- **require:** Dependências necessárias em produção
- **require-dev:** Dependências apenas para desenvolvimento
- **autoload:** Como carregar classes automaticamente
- **scripts:** Comandos personalizados

**Analogia:** É como a lista de ingredientes e instruções de uma receita. Diz exatamente o que você precisa (dependências) e como preparar (autoload, scripts).

**Funcionamento interno:**
- Composer lê este arquivo para saber o que instalar
- Resolve conflitos de versão entre dependências
- Configura autoloading baseado nas regras PSR-4
- Executa scripts em diferentes momentos do processo

**Por que é necessário:** Define de forma declarativa todas as dependências do projeto, permitindo instalação consistente em qualquer ambiente.

---

### 9. Arquivo `composer.lock`
**O Garantidor de Consistência**

Arquivo gerado automaticamente que "congela" as versões exatas de todas as dependências.

**Principais características:**
- Gerado automaticamente pelo Composer
- Contém versões exatas de todas as dependências
- Inclui checksums para verificação de integridade
- Deve ser versionado no Git

**Exemplo de entrada:**
```json
{
    "name": "symfony/console",
    "version": "v6.0.8",
    "source": {
        "type": "git",
        "url": "https://github.com/symfony/console.git",
        "reference": "0d00aa289215353aa8746a31bb7ddce1f3a4e6a3"
    },
    "dist": {
        "type": "zip",
        "url": "https://api.github.com/repos/symfony/console/zipball/0d00aa289215353aa8746a31bb7ddce1f3a4e6a3",
        "shasum": "d8ca2fb1a2d9f8e8b0d4b4b05b5b8b8e1234567"
    }
}
```

**Analogia:** É como uma foto instantânea da sua estante de livros. Enquanto o `composer.json` diz "quero livros de ficção científica", o `composer.lock` diz exatamente "quero 'Fundação' de Isaac Asimov, edição de 1951, ISBN específico".

**Funcionamento interno:**
- Quando você executa `composer install` sem o lock, ele resolve as dependências e cria o lock
- Com o lock presente, `composer install` usa as versões exatas especificadas
- `composer update` recalcula dependências e atualiza o lock
- Inclui hash de integridade para detectar alterações

**Por que é necessário:** Garante que todos os desenvolvedores e servidores de produção usem exatamente as mesmas versões, evitando bugs relacionados a diferenças de versão.

---

## 🔄 Fluxo de Funcionamento

**Como tudo se conecta:**

1. **Requisição chega** → `public/index.php`
2. **Index.php carrega** → `vendor/autoload.php` e cria `App\Kernel`
3. **Kernel lê** → configurações de `config/`
4. **Kernel roteia** → para controlador em `src/Controller/`
5. **Controlador processa** → usando serviços, entidades, repositories
6. **Sistema registra** → atividades em `var/log/`
7. **Cache acelera** → operações frequentes via `var/cache/`

---

## 💡 Dicas Importantes

- **Nunca edite** arquivos em `vendor/` - suas mudanças serão perdidas
- **Sempre versione** `composer.lock` no Git
- **Mantenha** `.env` com valores de desenvolvimento, use `.env.local` para valores sensíveis
- **Limpe o cache** regularmente em desenvolvimento: `bin/console cache:clear`
- **Monitore logs** em produção para identificar problemas rapidamente
