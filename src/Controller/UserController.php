<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\ContactType;
use App\Form\UserType;
use App\Helper\MailSender;
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
    #[Route('/user/delete/{id}', name: 'app_delete_user', requirements: ['id' => '\d+'])]
    public function deleteUser(Participant $participant, EntityManagerInterface $em, MailSender $mailSender): Response
    {
        $userRole = $participant->getRoles();

        if (in_array('ROLE_ADMIN', $userRole)){
            return $this->redirectToRoute('app_list_user');
        }

        $sorties = $participant->getSorties();
        if ($sorties->count()>0){
            foreach ($sorties as $sortie){
                $em->remove($sortie);
                $subjectParticipant = 'Sortie annulée';
                $textParticipant = "La sortie '" . $sortie->getNom() . "' prévue le " . $sortie->getDateHeureDebut()->format('Y-m-d H:i:s') . " a été annulée pour les raisons suivantes : \n" ."l'utilisateur a supprimé son compte";
                foreach ($sortie->getInscrits() as $inscrit) {
                    $mailSender->sendEmail($subjectParticipant, $textParticipant, $inscrit->getEmail());
                }
            }
        }
        $subjectOrganisateur = 'Compte supprimé';
        $textOrganisateur = "Ton compte a été supprimé, contacte un admin pour avoir plus de renseignement.";
        $mailSender->sendEmail($subjectOrganisateur, $textOrganisateur, $participant->getEmail());

        $em->remove($participant);
        $em->flush();

        return $this->redirectToRoute('app_list_user');
    }
    #[Route('/user/message/{id}', name: 'app_message_user')]
    public function contact(Request $request, MailSender $mailSender, Participant $participant): Response
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $destinataire = $participant->getEmail();
            $sujet = $data['sujet'];
            $message = $data['message'];
            $mailSender->sendEmail($sujet, $message, $destinataire);
            $this->addFlash("success", "Votre email a bien été envoyé !");
            return $this->redirectToRoute('app_message_user', ['id' => $participant->getId()]);
        }

        return $this->render('user/MessageUser.html.twig',[
            'form' => $form,
            'user' => $participant,
        ]);
    }
}
