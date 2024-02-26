<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Participant;
use App\Entity\Sortie;
use App\Form\CancelType;
use App\Form\SearchFormType;
use App\Form\SortieType;
use App\Helper\MailSender;
use App\Repository\ParticipantRepository;
use App\Repository\SiteRepository;
use App\Repository\EtatRepository;
use App\Repository\SortieRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/sortie')]
#[IsGranted('ROLE_USER')]
class SortieController extends AbstractController
{
    #[Route('/', name: 'app_sortie_index', methods: ['GET', 'POST'])]
    public function index(SortieRepository $sortieRepository, SiteRepository $siteRepository, Request $request): Response
    {
        $form = $this->createForm(SearchFormType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();

            $data['organisateur'] = $data['organisateur'] ? $this->getUser()->getId() : false;
            $data['participant'] = $data['participant'] ? $this->getUser()->getId() : false;
            $data['nonParticipant'] = $data['nonParticipant'] ? $this->getUser()->getId() : false;
            $data['finished'] = $data['finished'] ? 5 : false;

            $sorties = $sortieRepository->findByFilters($data);
        } else {
            $sorties = $sortieRepository->findAllStartingWithinMonth();
        }

        return $this->render('sortie/index.html.twig', [
            'searchForm' => $form,
            'sorties' => $sorties,
        ]);
    }

    #[Route('/new', name: 'app_sortie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);
        $userRole = $this->getUser()->getRoles();

        if(in_array('ROLE_ADMIN', $userRole)){
            $this->addFlash('warning', 'Tu es l\'admin');
            return $this->redirectToRoute('app_sortie_index');
        }

        if ($form->isSubmitted() && $form->isValid()) {
            // Create Ville
            $ville = $sortie->getLieu()->getVille();
            $entityManager->persist($ville);

            // Create lieu
            $lieu = $sortie->getLieu();
            $entityManager->persist($lieu);

            // Set oraganisateur
            $sortie->setOrganisateur($this->getUser());

            // Set creation state
            $isPublished = $sortie->isIsPublished();
            $state = $isPublished ? 2 : 1;
            $creationState = $etatRepository->find($state);
            $sortie->setEtat($creationState);

            // Persist entity
            $entityManager->persist($sortie);
            $entityManager->flush();

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('sortie/new.html.twig', [
            'sortie' => $sortie,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_sortie_show', methods: ['GET'])]
    public function show(Sortie $sortie): Response
    {
        return $this->render('sortie/show.html.twig', [
            'sortie' => $sortie,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_sortie_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Sortie $sortie, EntityManagerInterface $entityManager, EtatRepository $etatRepository): Response
    {
        $isUserOrganisateur = $sortie->getOrganisateur()->getEmail() === $this->getUser()->getUserIdentifier();
        $isSortiePublished = $sortie->isIsPublished();
        if ($isUserOrganisateur && !$isSortiePublished) {
            $form = $this->createForm(SortieType::class, $sortie);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                // Set creation state
                $isPublished = $sortie->isIsPublished();
                $state = $isPublished ? 2 : 1;
                $creationState = $etatRepository->find($state);
                $sortie->setEtat($creationState);
                $entityManager->flush();

                return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
            }

            return $this->render('sortie/edit.html.twig', [
                'sortie' => $sortie,
                'form' => $form,
            ]);
        } else {
            return $this->redirectToRoute("app_sortie_index");
        }

    }

    #[Route('/{id}', name: 'app_sortie_delete', methods: ['POST'])]
    public function delete(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        if ($sortie->getOrganisateur()->getEmail() === $this->getUser()->getUserIdentifier() && !$sortie->isIsPublished()) {
            if ($this->isCsrfTokenValid('delete' . $sortie->getId(), $request->request->get('_token'))) {
                $entityManager->remove($sortie);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        } else {
            return $this->redirectToRoute("app_sortie_show", [
                "id" => $sortie->getId(),
            ]);
        }

    }

    #[Route('/{id}/cancel', name: 'app_sortie_cancel', methods: ['GET', 'POST'])]
    public function cancel(Request $request, Sortie $sortie, EntityManagerInterface $entityManager, EtatRepository $etatRepository, MailSender $mailSender): Response
    {
        $isUserOrganisateur = $sortie->getOrganisateur()->getEmail() === $this->getUser()->getUserIdentifier();
        $isBeforeStartDate = new \DateTime() < $sortie->getDateHeureDebut();
        $isSortiePublished = $sortie->isIsPublished();

        $form = $this->createForm(CancelType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            if ($isUserOrganisateur && $isBeforeStartDate && $isSortiePublished) {
                $canceledState = $etatRepository->findOneBy(['libelle' => 'Annulée']);
                $sortie->setEtat($canceledState);
                $entityManager->flush();

                $this->addFlash('success', 'Sortie annulée');

                $subject = 'Sortie annulée';
                $text = "La sortie " . $sortie->getNom() . " prévue le " . $sortie->getDateHeureDebut()->format('Y-m-d H:i:s') . " a été annulée pour les raisons suivantes : \n" . $form->get('motif')->getData();
                foreach ($sortie->getInscrits() as $inscrit) {
                    $mailSender->sendEmail($subject, $text, $inscrit->getEmail());
                }

                return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
            } else {
                return $this->redirectToRoute("app_sortie_show", [
                    "id" => $sortie->getId(),
                ]);
            }
        }

        return $this->render('sortie/cancel.html.twig', [
            'form' => $form,
            'sortie' => $sortie
        ]);

    }

    #[Route('/{id}/suscribe', name: 'app_sortie_suscribe', methods: ['GET'])]
    public function suscribe(Sortie $sortie, EntityManagerInterface $entityManager, EtatRepository $etatRepository): Response
    {

        $participants = $sortie->getInscrits();
        $currentUser = $this->getUser();

        $isSuscribed = $participants->contains($currentUser);

        $maxParticipant = $sortie->getNbInscriptionMax();
        $hasTickets = $participants->count() < $maxParticipant;

        $isBeforeLimitDate = date('now') < $sortie->getDateLimiteInscription();

        $isOrganisateur = $currentUser->getId() == $sortie->getOrganisateur()->getId();

        if (!$isSuscribed && $hasTickets && $isBeforeLimitDate && $sortie->isIsPublished() && !$isOrganisateur) {
            $sortie->getInscrits()->add($currentUser);
            if ($participants->count() == $maxParticipant) {
                $creationState = $etatRepository->find(3);
                $sortie->setEtat($creationState);
            }
            $entityManager->flush();
            $this->addFlash('success', 'Vous avez été inscrit à l\'évènement');
        } else {
            $errorMsg = [];
            if ($isSuscribed) array_push($errorMsg, 'Vous êtes déjà inscrit');
            if (!$hasTickets) array_push($errorMsg, 'Evenement complet');
            if (!$isBeforeLimitDate) array_push($errorMsg, 'Date inscription dépassée');
            if (!$sortie->isIsPublished()) array_push($errorMsg, 'Evenement pas encore ouvert');
            if ($isOrganisateur) array_push($errorMsg, 'C\'est toi l\'organisateur');

            foreach ($errorMsg as $msg) {
                $this->addFlash('warning', $msg);
            }
        }

        return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
    }

    #[Route('/{id}/unsubscribe', name: 'app_sortie_unsubscribe', methods: ['GET'])]
    public function unsubscribe(Sortie $sortie, EntityManagerInterface $entityManager, EtatRepository $etatRepository): Response
    {
        $participants = $sortie->getInscrits();
        $currentUser = $this->getUser();
        $maxParticipant = $sortie->getNbInscriptionMax();

        $isOrganisateur = $currentUser->getId() == $sortie->getOrganisateur()->getId();
        $isSuscribed = $participants->contains($currentUser);
        $isBeforeStartDate = new \DateTime() < $sortie->getDateHeureDebut();

        if (!$isOrganisateur && $isSuscribed && $isBeforeStartDate) {
            $participants->removeElement($currentUser);
            if ($participants->count() < $maxParticipant) {
                $creationState = $etatRepository->find(2);
                $sortie->setEtat($creationState);
            }
            $entityManager->flush();
            $this->addFlash('success', 'Vous avez été désinscrit de l\'évènement');
        } else {
            $errorMsg = [];
            if (!$isSuscribed) array_push($errorMsg, 'Vous n\'êtes pas inscrit');
            if (!$isBeforeStartDate) array_push($errorMsg, 'Evènement déjà commencé');
            if ($isOrganisateur) array_push($errorMsg, 'C\'est toi l\'organisateur');

            foreach ($errorMsg as $msg) {
                $this->addFlash('warning', $msg);
            }
        }

        return $this->redirectToRoute('app_sortie_show', ['id' => $sortie->getId()]);
    }

}
