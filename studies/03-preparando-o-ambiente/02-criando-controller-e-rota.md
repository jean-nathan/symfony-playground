# Documentação - Controllers e Rotas no Symfony

## Índice

1. [Introdução](#introducao)
2. [Controllers](#controllers)
   - [O que são Controllers?](#o-que-sao-controllers)
   - [Estrutura de um Controller](#estrutura-de-um-controller)
   - [Namespaces](#namespaces)
   - [Imports (use statements)](#imports-use-statements)
3. [Rotas](#rotas)
   - [O que são Rotas?](#o-que-sao-rotas)
   - [Atributos de Rota](#atributos-de-rota)
   - [Parâmetros da Rota](#parametros-da-rota)
4. [Actions (Ações)](#actions-acoes)
   - [O que são Actions?](#o-que-sao-actions)
   - [Response Objects](#response-objects)
5. [Fluxo Completo](#fluxo-completo)
6. [Exemplo Prático](#exemplo-pratico)

---

## Introdução

No Symfony, **Controllers** e **Rotas** trabalham em conjunto para processar requisições HTTP. Pense nisso como um sistema de correios: as rotas são como os endereços nas cartas, e os controllers são os funcionários que processam essas cartas quando chegam no destino correto.

## Controllers

### O que são Controllers?

Um **Controller** é como um "gerente" que recebe uma requisição do usuário e decide o que fazer com ela. É uma classe PHP que contém métodos (chamados de **actions**) responsáveis por processar diferentes tipos de requisições.

**Analogia**: Imagine um restaurante onde o controller é o garçom. Quando você faz um pedido (requisição), o garçom (controller) processa seu pedido e retorna sua comida (response).

### Estrutura de um Controller

```php
<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController
{
    #[Route('/', name: 'hello_world')]
    public function index(): Response
    {
        return new Response('Olá mundo!');
    }
}
```

### Namespaces

```php
namespace App\Controller;
```

O **namespace** é como o "endereço" da sua classe no projeto. No Symfony:
- Todo conteúdo dentro da pasta `src/` usa o namespace `App`
- Esta configuração está definida no arquivo `composer.json`
- É como organizar arquivos em pastas no computador

**Por baixo dos panos**: O Symfony usa o **PSR-4** (padrão de autoload) para encontrar automaticamente suas classes baseado no namespace e estrutura de pastas.

### Imports (use statements)

```php
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
```

Os **imports** são como "importar ferramentas" que você precisa usar no seu controller:

- `Response`: Classe que representa a resposta HTTP que será enviada ao usuário
- `Route`: Atributo usado para definir rotas diretamente no código

**Analogia**: É como pegar ferramentas específicas da caixa de ferramentas antes de começar um trabalho.

## Rotas [rotas]

### O que são Rotas?

Uma **Rota** é o "caminho" que conecta uma URL específica a um método do controller. É como um mapa que diz: "quando alguém acessar esta URL, execute esta ação".

**Exemplo**: Quando o usuário digita `exemplo.com/contato`, a rota direciona para o método que exibe a página de contato.

### Atributos de Rota

```php
#[Route('/', name: 'hello_world')]
```

Os **atributos** são as "etiquetas" que você coloca nos métodos para configurar a rota:

- `'/'`: O **path** (caminho da URL). `/` significa a página inicial
- `name: 'hello_world'`: Nome único da rota, usado para referenciá-la em outros lugares

**Por baixo dos panos**: O Symfony "escaneia" todos os controllers em busca desses atributos e constrói uma tabela de rotas automaticamente.

### Parâmetros da Rota

As rotas podem ter parâmetros dinâmicos:

```php
#[Route('/usuario/{id}', name: 'mostrar_usuario')]
public function mostrarUsuario(int $id): Response
{
    return new Response("Usuário ID: " . $id);
}
```

**Analogia**: É como um formulário com campos em branco que são preenchidos dinamicamente.

## Actions (Ações)

### O que são Actions?

**Actions** são os métodos públicos dentro dos controllers que processam as requisições. Cada action representa uma "ação" específica que sua aplicação pode realizar.

```php
public function index(): Response // Esta é uma ACTION
{
    return new Response('Olá mundo!');
}
```

**Características das Actions**:
- Sempre são métodos públicos
- Sempre retornam um objeto `Response`
- Podem receber parâmetros da rota
- Têm nomes descritivos (index, show, create, etc.)

### Response Objects

```php
return new Response('Olá mundo!');
```

O **Response** é o "pacote de resposta" que sua aplicação envia de volta ao usuário:

- Contém o conteúdo HTML, JSON, ou outro formato
- Inclui headers HTTP (status code, content-type, etc.)
- É obrigatório em toda action

**Por baixo dos panos**: O Symfony converte este objeto Response em uma resposta HTTP válida que o navegador consegue interpretar.

## Fluxo Completo

Aqui está o que acontece quando um usuário acessa sua aplicação:

1. **Usuário** acessa uma URL (ex: `exemplo.com/`)
2. **Symfony** consulta a tabela de rotas para encontrar uma correspondência
3. **Rota** encontrada aponta para um controller e action específicos
4. **Controller** executa a action correspondente
5. **Action** processa a lógica necessária e cria um Response
6. **Response** é enviado de volta ao usuário

**Analogia**: É como um sistema de entrega:
1. Cliente faz pedido (URL)
2. Central de distribuição verifica o endereço (rota)
3. Funcionário processa o pedido (controller/action)
4. Produto é embalado (response)
5. Entrega ao cliente (navegador exibe o resultado)

## Exemplo Prático

Vamos expandir nosso exemplo com mais funcionalidades:

```php
<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController
{
    // Página inicial
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        return new Response('<h1>Bem-vindo ao meu site!</h1>');
    }

    // Página de saudação personalizada
    #[Route('/ola/{nome}', name: 'saudacao_personalizada')]
    public function saudar(string $nome): Response
    {
        return new Response("<h1>Olá, {$nome}!</h1>");
    }

    // Página sobre
    #[Route('/sobre', name: 'sobre')]
    public function sobre(): Response
    {
        return new Response('<h1>Sobre nós</h1><p>Esta é a página sobre nossa empresa.</p>');
    }
}
```

**Rotas resultantes**:
- `exemplo.com/` → Exibe "Bem-vindo ao meu site!"
- `exemplo.com/ola/João` → Exibe "Olá, João!"
- `exemplo.com/sobre` → Exibe página sobre

**Observações importantes**:
- O nome da classe (`HelloController`) deve ser igual ao nome do arquivo (`HelloController.php`)
- Todas as actions devem retornar um objeto `Response`
- Os controllers ficam organizados na pasta `src/Controller/`
- O Symfony automaticamente detecta e registra as rotas baseadas nos atributos

---

*Esta documentação serve como guia básico para entender Controllers e Rotas no Symfony. Para funcionalidades mais avançadas, consulte a documentação oficial do framework.*
