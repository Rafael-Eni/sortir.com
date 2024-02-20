<?php

namespace App\Controller;

use App\Entity\Etat;
use App\Entity\Sortie;
use App\Form\SortieType;
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
    #[Route('/', name: 'app_sortie_index', methods: ['GET'])]
    public function index(SortieRepository $sortieRepository): Response
    {
        return $this->render('sortie/index.html.twig', [
            'sorties' => $sortieRepository->findAll(),
        ]);
    }
    #[Route('/new', name: 'app_sortie_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, EtatRepository $etatRepository): Response
    {
        $sortie = new Sortie();
        $form = $this->createForm(SortieType::class, $sortie);
        $form->handleRequest($request);

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
        if ($isUserOrganisateur && !$isSortiePublished){
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
        }
        else{
            return $this->redirectToRoute("app_sortie_index");
        }

    }

    #[Route('/{id}', name: 'app_sortie_delete', methods: ['POST'])]
    public function delete(Request $request, Sortie $sortie, EntityManagerInterface $entityManager): Response
    {
        if ($sortie->getOrganisateur()->getEmail() === $this->getUser()->getUserIdentifier()){
            if ($this->isCsrfTokenValid('delete'.$sortie->getId(), $request->request->get('_token'))) {
                $entityManager->remove($sortie);
                $entityManager->flush();
            }

            return $this->redirectToRoute('app_sortie_index', [], Response::HTTP_SEE_OTHER);
        }
        else{
            return $this->redirectToRoute("app_sortie_show", [
                "id" => $sortie->getId(),
            ]);
        }

    }
}
