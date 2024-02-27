<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Helper\MailSender;
use App\Repository\ParticipantRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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
    public function mentionsLegales(ParticipantRepository $participantRepository): Response
    {
        $admin = $participantRepository->findByRole('ROLE_ADMIN');

        return $this->render('footer/mentions.html.twig',[
            'admin'=>$admin[0]
        ]);
    }
    #[IsGranted('ROLE_USER')]
    #[Route('/contact', name: 'app_contact')]
    public function contact(Request $request, MailSender $mailSender): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $sujet = $data['sujet'];
            $message = $data['message'];
            $mailSender->sendEmail($sujet, $message, 'admin@sortir.com');
            $this->addFlash("success", "Votre email a bien été envoyé !");
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('footer/contactUs.html.twig',[
            'form' => $form,
        ]);
    }

}
