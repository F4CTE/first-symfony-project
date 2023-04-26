<?php

namespace App\Mail\Newsletter;

use App\Entity\Newsletter;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SubscribedConfirmation
{
    public function __construct(
        private MailerInterface $mailer,
        private UrlGeneratorInterface $urlGenerator
    ) {
    }

    public function sendTo(Newsletter $newsletter)
    {
        $email = (new Email())
            ->from('admin@hb-corp.com')
            ->to($newsletter->getEmail())
            ->subject('Inscription à la newsletter')
            ->text($this->urlGenerator->generate(
                'newsletter_confirm',
                ['authToken' => $newsletter->getAuthToken()],
                UrlGeneratorInterface::ABSOLUTE_URL
            ));

        $this->mailer->send($email);
    }
}
