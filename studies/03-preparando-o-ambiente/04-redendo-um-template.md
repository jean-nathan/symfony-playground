## Camada de Visão (Twig): Dando Vida ao HTML

Até agora, nosso Controller poderia retornar um texto simples ou um JSON, mas para criar páginas web de verdade, precisamos de HTML. E aí surge a primeira pergunta:

*"Por que não posso simplesmente dar um `echo` no HTML dentro do meu Controller em PHP?"*

Você até pode, mas isso mistura as responsabilidades. O Controller é o **cérebro** da aplicação, ele cuida da lógica. O HTML é a **aparência**, a parte visual. Misturar os dois é como um chef de cozinha que, além de cozinhar, tenta desenhar o cardápio ao mesmo tempo. Ele pode até conseguir, mas o resultado não vai ser tão bom e a manutenção será um pesadelo.

**Por que isso é importante?** Para separar a lógica de programação (PHP) da lógica de apresentação (HTML/CSS). Isso permite que um desenvolvedor de front-end, que talvez não conheça PHP a fundo, possa trabalhar na aparência do site sem risco de quebrar a lógica de negócio.

**Analogia:** O **Controller é o Chef de Cozinha**. Ele seleciona os ingredientes (busca dados no banco), prepara o prato principal (aplica a lógica de negócio) e entrega tudo pronto em uma bandeja. O **Twig é o Food Stylist**. Ele pega a comida pronta da bandeja e a arruma no prato de forma bonita e apetitosa para o cliente. O Chef não precisa saber sobre a decoração do prato, e o Stylist não precisa saber a receita do molho.

### Instalando o Twig: Nosso Kit de Ferramentas para Templates

Como você bem anotou, o Symfony vem "pelado" por padrão. Ele é um sistema de rotas e nada mais. Se queremos renderizar HTML de forma profissional, precisamos da ferramenta certa. Essa filosofia é ótima, pois garante que nossa aplicação tenha apenas o que é realmente necessário, mantendo-a leve e rápida.

Para instalar o Twig, usamos o Composer, que é o grande gerenciador de pacotes do PHP.

```bash
composer require twig
```

O comando `require` diz ao Composer: "Ei, eu preciso deste pacote para o meu projeto. Por favor, encontre-o, baixe-o e configure-o para mim".

### O Que Aconteceu nos Bastidores? A Mágica do Symfony Flex

Quando você rodou esse comando, várias coisas aconteceram. Não foi só um download. Aqui entra em cena o **Symfony Flex**, que atua como um assistente inteligente para o Composer dentro de um projeto Symfony.

**Analogia:** Pense no **Composer** como um serviço de entrega que baixa uma caixa com um móvel desmontado na sua porta. O **Symfony Flex** é o montador especializado que pega essa caixa, lê o manual (a "receita"), monta o móvel para você e já o coloca no lugar certo da casa.

Vamos ver o que esse "montador" fez, baseado nas suas anotações:

1.  **Arquivos Modificados:**

      * `composer.json`: É a "lista de compras" do seu projeto. O Flex adicionou as dependências que o Twig precisa para funcionar. Como você observou, não foi só uma, mas várias:
          * `symfony/twig-bundle`: A "cola" oficial que integra o Twig ao Symfony. É o principal pacote de integração.
          * `twig/twig`: A biblioteca do Twig em si, o "motor" do sistema de templates.
          * `twig/extra-bundle` e `symfony/yaml`: Dependências adicionais que esses pacotes precisam para funcionar corretamente.
      * `composer.lock`: É a "nota fiscal" do seu projeto. Ele "trava" as versões exatas de cada pacote que foi instalado. Isso garante que, se outro desenvolvedor for trabalhar no projeto, ele instalará exatamente as mesmas versões que você, evitando o clássico "mas na minha máquina funciona".
      * `symfony.lock`: Um arquivo de controle para o Flex, para ele saber quais "receitas" já aplicou e não tentar aplicá-las de novo.

2.  **A Receita em Ação (`config/bundles.php`):**
    O Flex aplicou uma "receita" (recipe). A parte mais visível disso é a alteração no arquivo `config/bundles.php`. Ele adicionou estas linhas:

    ```php
    Symfony\Bundle\TwigBundle\TwigBundle::class => ['all' => true],
    Twig\Extra\TwigExtraBundle\TwigExtraBundle::class => ['all' => true],
    ```

    **Por que isso é importante?** Esse arquivo é o "quadro de disjuntores" da sua aplicação. É aqui que você "liga" ou "desliga" os pacotes (Bundles) para o Kernel do Symfony. Ao adicionar essas linhas, o Flex está dizendo: "Symfony, a partir de agora, carregue o Bundle do Twig em todos os ambientes (desenvolvimento, produção, etc.). Ele está ativo e pronto para ser usado".

3.  **Criação de Estrutura (`templates/`):**
    A receita do Twig é inteligente. Ela sabe que, se você instalou um sistema de templates, você vai precisar de um lugar para guardar seus arquivos `.html.twig`. Por isso, o Flex automaticamente criou o diretório `templates/` na raiz do seu projeto. É a "oficina" do nosso Food Stylist, onde todos os pratos (páginas) serão montados.

Em resumo, com um único comando, o Symfony Flex orquestrou o download, a configuração, a ativação e a criação da estrutura de pastas necessária para começar a trabalhar. Isso é produtividade\!

### Colocando em Prática: Renderizando sua Primeira View

Agora que o Twig está instalado e configurado, como o Controller (Chef) faz para pedir que o Twig (Stylist) monte uma página?

É bem simples. Se o seu Controller estende a classe `AbstractController` do Symfony, você ganha acesso a vários métodos de atalho, incluindo o `$this->render()`.

Veja um exemplo:

**1. No seu Controller (ex: `src/Controller/MeuController.php`):**

```php
<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class MeuController extends AbstractController
{
    #[Route('/ola', name: 'app_ola')]
    public function ola(): Response
    {
        $nomeDoUsuario = 'Fulano';

        // O Chef prepara os "ingredientes" (dados)
        // e pede para o Stylist (Twig) montar o prato (template)
        return $this->render('meu/pagina.html.twig', [
            'nome' => $nomeDoUsuario,
        ]);
    }
}
```

**2. No seu Template ( `templates/meu/pagina.html.twig`):**

```twig
{# Este é um arquivo Twig. A sintaxe é parecida com HTML, mas com superpoderes! #}
<!DOCTYPE html>
<html>
<head>
    <title>Minha Página</title>
</head>
<body>
    {# A variável 'nome' que passamos no Controller está disponível aqui #}
    <h1>Olá, {{ nome }}!</h1>
</body>
</html>
```

Quando você acessar a rota `/ola`, o Controller vai executar, passar a variável `nomeDoUsuario` para o Twig com o nome de `nome`, e o Twig vai renderizar o HTML, substituindo `{{ nome }}` por "Fulano". Simples assim\!
