<?php

namespace App\Controller;

use App\Entity\Participant;
use App\Form\RegistrationFormType;
use App\Helper\MailSender;
use App\Repository\ParticipantRepository;
use App\Security\EmailVerifier;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mime\Address;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

class RegistrationController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }

    #[Route('/register', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, EntityManagerInterface $entityManager, ParticipantRepository $participantRepository, MailSender $mailSender, EmailVerifier $emailVerifier): Response
    {
        $user = new Participant();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        $existingMailUser = $participantRepository->findOneBy(['email' => $user->getEmail()]);


        if ($form->isSubmitted() && $form->isValid()) {
            if ($existingMailUser) {
                return $this->redirectToRoute('app_register');
            }
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $userAdmin = $this->getUser();
            if ($userAdmin !== null && $userAdmin->getRoles() !== null) {
                $role = $userAdmin->getRoles();
                if (in_array('ROLE_ADMIN', $role)) {
                    $this->addFlash("success", "L'utilisateur a reçu un email de vérification");
                    $user->setActif(true);
                }
            } else {
                $this->addFlash("success", "Ton compte doit être validé par un admin. Nous t'enverrons un email de confirmation sous 24h");
                $user->setActif(false);
            }

            $user->setRoles(['ROLE_USER']);
            $user->setIsVerified(false);

            $entityManager->persist($user);
            $entityManager->flush();
            if ($userAdmin !== null && $userAdmin->getRoles() !== null) {
                if (in_array('ROLE_ADMIN', $role)) {
                    $emailVerifier->sendEmailConfirmation('app_verify_email', $user,
                        (new TemplatedEmail())
                            ->from(new Address('noreply@sortir.com', 'admin'))
                            ->to($user->getEmail())
                            ->subject('Please Confirm your Email')
                            ->htmlTemplate('registration/confirmation_email.html.twig')
                    );
                }
            }


            $subject = 'Nouvelle inscription';
            $text = 'Un nouvelle utilisateur vient de s\'inscrire : ' . $user->getNom() . ' ' . $user->getPrenom() . ' ' . $user->getEmail();
            $mailSender->sendEmail($subject, $text, 'admin@sortir.com');

            return $this->redirectToRoute('app_main');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form,
        ]);
    }

    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(Request $request, TranslatorInterface $translator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');

        // validate email confirmation link, sets User::isVerified=true and persists
        try {
            $this->emailVerifier->handleEmailConfirmation($request, $this->getUser());
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));
            return $this->redirectToRoute('app_register');
        }

        // @TODO Change the redirect on success and handle or remove the flash message in your templates
        $this->addFlash('success', 'Ton adresse email a bien été validée');

        return $this->redirectToRoute('app_main');
    }
}
