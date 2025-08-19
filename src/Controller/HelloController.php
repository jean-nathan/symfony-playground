<?php

namespace App\Controller; // Tudo que esta dentro de src, será chamado de App, conforme config no composer.json

// Usamos metodos que nao estao dentro desse controller, por isso chamados os arquivos abaixo com use que contém os metodos como Response e Route
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;



class HelloController // O nome da class será o mesmo do arquivo
{
  #[Route('/', name: 'hello_world')]
  public function index(): Response // Todas as funcoes das controllers terao nome de acoes (actions)
  {
    return new Response('Olá mundo!');
  }


  #[Route('/open/{name}', name: 'nome')]
  public function openName($name): Response // Todas as funcoes das controllers terao nome de acoes (actions)
  {
    return new Response("<h1>Olá, {$name}</h1>");
  }

  #[Route('/open/{age}', name: 'age')]
  public function openAge($age): Response // Todas as funcoes das controllers terao nome de acoes (actions)
  {
    return new Response("<h1>Sua idade é, {$age}</h1>");
  }
}

// Obs: Nossos controlares sempre vao retornar um objeto reponse
// basicamente uma controller possui uma rota que quando chamada invoca uma action na controller e retorne uma response que no caso o "Olá mundo".
