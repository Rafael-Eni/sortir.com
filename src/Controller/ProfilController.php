<?php

namespace App\Controller;

use App\Entity\Participant;
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
    public function updateProfil(int $id, ParticipantRepository $participantRepository, Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $user = $participantRepository->find($id);
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get('poster_file')->getData() instanceof UploadedFile) {
                $dir = $this->getParameter('poster_dir');
                $posterFile = $form->get('poster_file')->getData();
                $fileName = $slugger->slug($user->getFirstName()) . '-' . uniqid() . '.' . $posterFile->guessExtension();
                $posterFile->move($this->getParameter('poster_dir'), $fileName);
                if ($user->getPosterFile() && \file_exists($dir . '/' . $user->getPosterFile())) {
                    unlink($dir . '/' . $user->getPosterFile());
                }
                $user->setPosterFile($fileName);
            }
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $entityManager->flush();
            return $this->redirectToRoute('app_profil_user', ['id' => $user->getId()]);
        }
        return $this->render('profil/updateProfil.html.twig', [
            'form' => $form->createView(),
            'user' => $user
        ]);
    }

    #[Route('/delete/profil/{id}', name: 'app_delete_profil', requirements: ['id' => '\d+'])]
    public function deleteUser(Participant $participant, EntityManagerInterface $em): Response
    {
        $em->remove($participant);
        $em->flush();

        return $this->redirectToRoute('app_login');
    }
}
