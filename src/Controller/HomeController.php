<?php

namespace App\Controller; // Tudo que esta dentro de src, será chamado de App, conforme config no composer.json

// Usamos metodos que nao estao dentro desse controller, por isso chamados os arquivos abaixo com use que contém os metodos como Response e Route

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController 
{
  #[Route('/', name: 'app_home')]
  public function new(): Response 
  {

    $slug = 'test';
    dump($slug);
    return $this->render('./home/home.html.twig', ['slug' => $slug]);
  }
}

// Obs: Nossos controlares sempre vao retornar um objeto reponse
// basicamente uma controller possui uma rota que quando chamada invoca uma action na controller e retorne uma response que no caso o "Olá mundo".
