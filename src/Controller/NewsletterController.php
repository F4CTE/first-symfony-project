<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Form\NewsletterType;
use App\Repository\NewsletterRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class NewsletterController extends AbstractController
{
    #[Route('/newsletter/subscribe', name: 'newsletter_subscribe')]
    public function subscribe(Request $request, NewsletterRepository $newsletterRepository, MailerInterface $mailer): Response
    {
        $newsletter = new Newsletter();
        $form = $this->createForm(NewsletterType::class, $newsletter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newsletter->generateToken();
            $email = (new Email())
                ->from('no-reply@symfony.com')
                ->to($newsletter->getEmail())
                ->subject('please confirm your newsletter subscription')
                ->text($this->generateUrl('newsletter_confirmer', [
                    'email' => $newsletter->getEmail(),
                    'token' => $newsletter->getAuthToken(),
                ], UrlGeneratorInterface::ABSOLUTE_URL));

            $mailer->send($email);

            $newsletterRepository->save($newsletter, true);
        }

        return $this->renderForm('newsletter/subscribe.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/newsletter/confirmer', name: 'newsletter_confirmer')]
    public function confirmer(Request $request, NewsletterRepository $newsletterRepository): Response
    {
        $email = $request->query->get('email');
        $token = $request->query->get('token');
        $newsletter = $newsletterRepository->findOneBy(['email' => $email]);
        if (!$newsletter || $newsletter->getAuthToken() !== $token) {
            throw $this->createNotFoundException();
        }
        $newsletter->setIsActif(true);
        $newsletter->setAuthToken(null);
        $newsletterRepository->save($newsletter, true);
        return $this->render('newsletter/confirmed.html.twig', [
            'newsletter' => $newsletter,
        ]);
    }
}
