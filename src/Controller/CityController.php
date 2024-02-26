<?php

namespace App\Controller;

use App\Entity\Site;
use App\Form\SiteType;
use App\Repository\SiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CityController extends AbstractController
{
    #[Route('/new/city', name: 'app_new_city', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $site = new Site();
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($site);
            $entityManager->flush();
            return $this->redirectToRoute('app_list_site');
        }
        return $this->render('city/newCity.html.twig', [
            'site' => $site,
            'siteForm' => $form,
        ]);
    }
    #[Route('/site', name: 'app_list_site', methods: ['GET', 'POST'])]
    public function listSite(SiteRepository $siteRepository, Request $request): Response
    {
        $form = $this->createForm(SiteType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

//            $site = $siteRepository->findUserByFilters($data);
        } else {
            $site = $siteRepository->findAll();
        }

        return $this->render('city/listCity.html.twig', [
            'site' => $site,
            'siteForm' => $form
        ]);
    }
}