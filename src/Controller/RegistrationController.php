<?php

namespace App\Controller;

use App\Entity\User;
use DateTimeImmutable;
use App\Services\MailerService;
use App\Form\RegistrationFormType;
use App\Repository\UserRepository;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;

class RegistrationController extends AbstractController
{
    /**
     * @Route("/register", name="app_register")
     */
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager, MailerService $mailer): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setActivationToken(md5(uniqid()));
            $user->setCreatedAt(new DateTimeImmutable());
            $entityManager->persist($user);
            $entityManager->flush();
            $mailer->sendMail($user->getEmail(), $user->getActivationToken());
            // do anything else you need here, like send an email
            $this->addFlash('success', 'Merci d\'activer votre nouveau compte par mail !!');
            return $this->redirectToRoute('app_login');
            /*return $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request,
            );*/
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/activation/{token}", name="activation")
     */
    public function activation($token, UserRepository $user)
    {
        $user = $user->findOneBy(['activation_token' => $token]);

        if (!$user) {
            dd($token, $user);
        }

        $user->setActivationToken(null);
        $user->setIsVerified(true);
        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->persist($user);
        $entityManager->flush();

        $this->addFlash('success', 'Votre compte est activée !!');
        return $this->redirectToRoute('app_login');
    }
}
