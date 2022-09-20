<?php

namespace App\Services;

use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;

class MailerService
{
    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @Route("/mailer", name="app_mailer")
     */
    public function sendMail($email, $subject, $html, $context)
    {
        $email = (new TemplatedEmail())
                ->from('formationoc@christophedumas1.fr')
                ->to(new Address($email))
                ->subject($subject)

            // path of the Twig template to render
                ->htmlTemplate($html)

            // pass variables (name => value) to the template
                ->context($context)
        ;

        $this->mailer->send($email);
    }
}
