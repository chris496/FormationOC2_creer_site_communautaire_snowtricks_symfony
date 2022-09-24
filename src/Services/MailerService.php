<?php

namespace App\Services;

use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\DependencyInjection\ParameterBag\ContainerBagInterface;

class MailerService
{
    public function __construct(MailerInterface $mailer, ContainerBagInterface $params)
    {
        $this->mailer = $mailer;
        $this->params = $params;
    }

    /**
     * @Route("/mailer", name="app_mailer")
     */
    public function sendMail($email, $subject, $html, $context)
    {
        $email = (new TemplatedEmail())
            ->from($this->params->get('MAILER_FROM'))
            ->to(new Address($email))
            ->subject($subject)

            // path of the Twig template to render
            ->htmlTemplate($html)

            // pass variables (name => value) to the template
            ->context($context);

        $this->mailer->send($email);
    }
}
