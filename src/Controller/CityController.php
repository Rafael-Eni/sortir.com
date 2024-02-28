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

    #[Route('/site', name: 'app_list_site', methods: ['GET', 'POST'])]
    public function listSite(SiteRepository $siteRepository, Request $request,  EntityManagerInterface $entityManager): Response
    {
        $site = new Site();
        $form = $this->createForm(SiteType::class, $site);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($site);
            $entityManager->flush();
            return $this->redirectToRoute('app_list_site');
        } else {
            $site = $siteRepository->findAll();
        }

        return $this->render('city/listCity.html.twig', [
            'site' => $site,
            'siteForm' => $form
        ]);
    }

    #[Route('/city/delete/{id}', name: 'app_delete_city', requirements: ['id' => '\d+'])]
    public function deleteCity(Site $site, EntityManagerInterface $em): Response
    {
        $city = $site->getSorties();
        if ($city->count() > 0) {
            $this->addFlash("warning", "Une ou plusieurs sorties sont affectées a ce site !");
            return $this->redirectToRoute('app_list_site');
        }

        $em->remove($site);
        $em->flush();

        return $this->redirectToRoute('app_list_site');
    }
}
