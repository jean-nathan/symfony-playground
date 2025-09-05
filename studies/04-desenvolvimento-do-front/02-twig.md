# Twig

Como renderizar uma página?

Basta extender o AbstractController na controller do método para usar o metodo herdado chamado render que recveberá 2 parametros. A página e valores da controller pra página.

É de costume criar uma pasta com nome da controller, se o nome da classe é Hello, lá também será.

```php


class HelloController extends AbstractController // O nome da class será o mesmo do arquivo
{
  #[Route('/', name: 'hello_world')]
  public function index(): Response // Todas as funcoes das controllers terao nome de acoes (actions)
  {
    return $this->render('./hello/index.html.twig', ['title'=> 'Zoologico']);
  }
}
```

Existe alguns comandos que usaremos no Twig:

{{...}} Mostrar o valor de uma variavel ou expressão <h1>{{ 5+5 }}</h1> ou <h1>{{ title }}</h1>
{{% ... %}} Faça algo como iterar sobre um laço

<ul>
  {# Este é um comentário - ele não aparece no DOM #}
  {% for usuario in usuarios %}
  <li>Nome: {{usuario.name}} - {{usuario.age}} - {{usuario.city}}</li>
  {% endfor %}
</ul>

Também podemos usar 


Usamos o dump para debugar e o exit para não executar o código abaixo do mesmo.

  $usuario = [
      ['name' => 'Nathan', 'age' => 25, 'city' => 'São Paulo'],
      ['name' => 'Ana', 'age' => 30, 'city' => 'Rio de Janeiro'],
      ['name' => 'Carlos', 'age' => 28, 'city' => 'Belo Horizonte'],
      ['name' => 'Maria', 'age' => 22, 'city' => 'Curitiba'],
  ];

    dump($usuario);
    exit;
