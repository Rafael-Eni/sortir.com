<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ProfilType;
use App\Form\RegistrationFormType;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProfilController extends AbstractController
{
    #[Route('/profil/{id}', name: 'app_profil')]
    public function index(ParticipantRepository $participantRepository, int $id): Response
    {
        $user = $participantRepository->find($id);

        return $this->render('profil/profil.html.twig', [
            'user' => $user
        ]);
    }

    #[Route('/update/profil/{id}', name: 'app_update_profil')]
    public function updateProfil(Participant $user, ParticipantRepository $participantRepository, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {

        if($user !== $this->getUser()) {
            return $this->redirectToRoute('app_profil', ['id' => $user->getId()]);
        }

        $form = $this->createForm(ProfilType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('poster_file')->getData() instanceof UploadedFile) {
                $dir = $this->getParameter('profil_pic_dir');
                $posterFile = $form->get('poster_file')->getData();
                $fileName = $slugger->slug($user->getPseudo()) . '-' . uniqid() . '.' . $posterFile->guessExtension();
                $posterFile->move($this->getParameter('profil_pic_dir'), $fileName);
                if ($user->getPosterFile() && \file_exists($dir . '/' . $user->getPosterFile())) {
                    unlink($dir . '/' . $user->getPosterFile());
                }
                $user->setPosterFile($fileName);
            }
            $entityManager->flush();
            return $this->redirectToRoute('app_profil', ['id' => $user->getId()]);
        }
        return $this->render('profil/updateProfil.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/delete/profil/{id}', name: 'app_delete_profil', requirements: ['id' => '\d+'])]
    public function deleteUser(Participant $participant, EntityManagerInterface $em): Response
    {
        if($participant->getEmail() !== $this->getUser()->getUserIdentifier()) {
            return $this->redirectToRoute('app_profil', ['id' => $participant->getId()]);
        }
        $userRole = $this->getUser()->getRoles();

        if (in_array('ROLE_ADMIN', $userRole)){
            return $this->redirectToRoute('app_profil', ['id' => $participant->getId()]);
        }

        $em->remove($participant);
        $em->flush();

        return $this->redirectToRoute('app_logout');
    }
}
