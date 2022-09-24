<?php

namespace App\Controller;

use App\Form\ResetPassType;
use App\Services\MailerService;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Csrf\TokenGenerator\TokenGeneratorInterface;

class SecurityController extends AbstractController
{
    public $em;

    public function __construct(EntityManagerInterface $em, UserRepository $userRepository)
    {
        $this->em = $em;
        $this->userRepository = $userRepository;
    }

    /**
     * @Route("/login", name="app_login")
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error]);
    }

    /**
     * @Route("/logout", name="app_logout")
     */
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    /**
     * @Route("/reset-pass", name="forget_password")
     */
    public function forgetPass(Request $request, TokenGeneratorInterface $tokenGenerator, MailerService $mailer)
    {
        $form = $this->createForm(ResetPassType::class);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user =  $this->userRepository->findOneByEmail($data['email']);

            if ($user === null) {
                $this->addFlash('danger', 'Cette adresse e-mail est inconnue');
                //dd('test', $user);
                return $this->redirectToRoute('forget_password');
            }
            $token = $tokenGenerator->generateToken();
            try {

                $user->setResetToken($token);

                $this->em->persist($user);
                $this->em->flush();
                //dd($user, $token);
            } catch (\Exception $e) {
                $this->addFlash('warning', $e->getMessage());
                return $this->redirectToRoute('app_login');
            }
            $this->addFlash('danger', 'un mail vient de vous être envoyé à l\'adresse saisie');
            $url = $this->generateUrl('app_reset_password', array('token' => $token), UrlGeneratorInterface::ABSOLUTE_URL);
            $mailer->sendMail($user->getEmail(), 'Reset !', 'emails/reset_pass.html.twig', [
                'token' => $token,
                'url' => $url
            ]);
            /*$email = (new TemplatedEmail())
                ->from('formationoc@christophedumas1.fr')
                ->to($user->getEmail())
                ->subject('Reset !')
            // path of the Twig template to render
                ->htmlTemplate('emails/reset_pass.html.twig')
            // pass variables (name => value) to the template
                ->context([
                    'token' => $token,
                    'url' => $url
                ])
            ;
            $mailer->send($email);*/
        }

        return $this->render('security/forget_password.html.twig', [
            'emailForm' => $form->createView(),
        ]);
    }

    /**
     * @Route("/reset_pass/{token}", name="app_reset_password")
     */
    public function resetPassword(Request $request, $token, UserPasswordHasherInterface $userPasswordHasher)
    {
        $user =  $this->userRepository->findOneBy(['reset_token' => $token]);

        if ($user === null) {
            $this->addFlash('danger', 'Token Inconnu');
            return $this->redirectToRoute('forget_password');
        }
        if ($request->isMethod('POST')) {
            $user->setResetToken(null);
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $request->request->get('password')
                )
            );
            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('message', 'Mot de passe mis à jour');

            return $this->redirectToRoute('app_login');
        } else {
            // Si on n'a pas reçu les données, on affiche le formulaire
            return $this->render('security/reset_password.html.twig', ['token' => $token]);
        }
    }
}
