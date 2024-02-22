<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\UserType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ADMIN')]
class UserController extends AbstractController
{
    #[Route('/user', name: 'app_list_user')]
    public function list(ParticipantRepository $participantRepository, Request $request): Response
    {
        $form = $this->createForm(UserType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $listUser = $participantRepository->findUserByFilters($data);
        } else {
            $listUser = $participantRepository->findAll();
        }

        return $this->render('user/userList.html.twig', [
            'user' => $listUser,
            'userForm' => $form
        ]);
    }

    #[Route('/user/detail/{id}', name: 'app_detail_user')]
    public function detail(Participant $participant): Response
    {
        return $this->render('profil/profil.html.twig', [
            'user' => $participant
        ]);
    }

    #[Route('/user/desactivate/{id}', name: 'app_desactivate_user')]
    public function desactivate(Participant $participant, EntityManagerInterface $entityManager): Response
    {
        if ($participant->isActif()) {
            $participant->setActif(false);
        } else {
            $participant->setActif(true);
        }
        $entityManager->flush();
        return $this->redirectToRoute('app_list_user');
    }
}
