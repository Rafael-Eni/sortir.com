<?php

namespace App\Controller;

use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('/', name: 'app_main')]
    public function index(): Response
    {
        return $this->render('main/index.html.twig',[
            'controller_name' => 'MainController',
        ]);
    }


    #[Route('/politique-de-confidentialité', name: 'app_politique')]
    public function politiqueDeConfidentialité(): Response
    {
        return $this->render('footer/politique.html.twig',[
            'controller_name' => 'MainController',
        ]);
    }

    #[Route('/mentions-legales', name: 'app_mentions')]
    public function mentionsLegales(): Response
    {
        return $this->render('footer/mentions.html.twig',[
            'controller_name' => 'MainController',
        ]);
    }

}
