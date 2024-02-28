<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\FileFormType;
use App\Form\RegistrationFormType;
use App\Helper\MailSender;
use App\Repository\ParticipantRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class RegistrationAdminController extends AbstractController

{

    #[Route('/admin/index', name: 'app_register_index')]
    public function registerIndex(): Response
    {

        return $this->render('admin/creer-utilisateur.html.twig', [
        ]);

    }



    #[Route('/admin/fichier-csv', name: 'app_registration_admin')]
    public function registrationAdmin(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository, MailSender $mailSender, SluggerInterface $slugger): Response
    {
        $user = new Participant();
        $form = $this->createForm(FileFormType::class, $user);
        $form->handleRequest($request);
        $existingMailUser = $participantRepository->findOneBy(['email' => $user->getEmail()]);

        if ($form->isSubmitted() && $form->isValid()) {

            $fileCSV = $form->get('fichierCSV')->getData();

            if ($existingMailUser) {
                return $this->redirectToRoute('app_register');
            }



            if (!empty($fileCSV) && $fileCSV instanceof UploadedFile) {

                $originalFilename = pathinfo($fileCSV->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $fileCSV->guessExtension();

                // Déplacez le fichier CSV vers le répertoire spécifié
                try {
                    $fileCSV->move(
                        $this->getParameter('fichiersCSV_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    $this->addFlash('error', 'Une erreur a eu lieu lors de l\'import du fichier. Merci de réessayer.');
                    return $this->redirectToRoute('app_register');
                }


                // Récupérez le chemin complet du fichier CSV
                $filePath = $this->getParameter('fichiersCSV_directory') . '/' . $newFilename;

                // Ouvrez le fichier CSV en lecture
                if (($handle = fopen($filePath, 'r')) !== false) {
                    // Parcourez chaque ligne du fichier CSV

                    while (($data = fgetcsv($handle, null, ';')) !== false) {
                        // Créez une nouvelle entité Participant

                        $participant = new Participant();

                        // Remplissez les propriétés de l'entité avec les valeurs du fichier CSV
                        $participant->setEmail($data[0]); // Assurez-vous d'ajuster l'indice en fonction de votre fichier CSV
                        $participant->setPassword($data[1]);
                        $participant->setNom($data[2]);
                        $participant->setPrenom($data[3]);
                        $participant->setTelephone($data[4]);
                        $participant->setPseudo($data[5]);
                        $participant->setSexe($data[6]);
                        $participant->setRoles(['ROLE_USER']);
                        $participant->setActif(false);
                        $participant->setIsVerified(false);
                        // Persistez l'entité dans la base de données
                        $entityManager->persist($participant);
                    }

                    // Fermez le fichier CSV
                    fclose($handle);

                    // Flush des changements dans la base de données
                    $entityManager->flush();

                    // Ajoutez un message flash pour indiquer que l'opération a réussi
                    $this->addFlash('success', 'Les données du fichier CSV ont été importées avec succès.');
                } else {
                    $this->addFlash('error', 'Impossible d\'ouvrir le fichier CSV.');
                }

                return $this->redirectToRoute('app_register');
            }

            $user->setRoles(['ROLE_USER']);
            $user->setActif(false);
            $user->setIsVerified(false);

            $entityManager->persist($user);
            $entityManager->flush();

            $subject = 'Nouvelle inscription';
            $text = 'Un nouvelle utilisateur vient de s\'inscrire : ' . $user->getNom() . ' ' . $user->getPrenom() . ' ' . $user->getEmail();
            $mailSender->sendEmail($subject, $text, 'admin@sortir.com');

            $this->addFlash("success", "Ton compte doit être validé par un admin. Nous t'enverrons un email de confirmation sous 24h");

            return $this->redirectToRoute('app_register_index');
        }

        return $this->render('admin/fichier-csv.html.twig', [
            'CSVForm' => $form,
        ]);
    }
}
