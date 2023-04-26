<?php

namespace App\Controller;

use App\Entity\Newsletter;
use App\Form\NewsletterType;
use App\Repository\NewsletterRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\ByteString;

class NewsletterController extends AbstractController
{
    #[Route('/newsletter/subscribe', name: 'newsletter_subscribe')]
    public function subscribe(Request $request, NewsletterRepository $newsletterRepository, MailerInterface $mailer): Response
    {
        $newsletter = new Newsletter();
        $form = $this->createForm(NewsletterType::class, $newsletter);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $newsletter->setAuthToken(ByteString::fromRandom(32)->toString());
            $email = (new Email())
                ->from('no-reply@symfony.com')
                ->to($newsletter->getEmail())
                ->subject('please confirm your newsletter subscription')
                ->text($this->generateUrl('newsletter_confirm', [
                    'token' => $newsletter->getAuthToken(),
                ], UrlGeneratorInterface::ABSOLUTE_URL));
            $newsletterRepository->save($newsletter, true);
            $mailer->send($email);
            $this->addFlash('success', 'Votre demande \'inscription a bien été prise en compte. veuillez la confirmer en cliquant sur le lien reçu.');
            return $this->redirectToRoute('app_index');
        }

        return $this->renderForm('newsletter/subscribe.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route('/newsletter/confirm/{authToken}', name: 'newsletter_confirm')]
    public function confirmer(Newsletter $newsletter, EntityManagerInterface $entityManager): Response
    {
        $newsletter
            ->setIsActif(true)
            ->setAuthToken(null);
        $entityManager->flush();
        $this->addFlash('success', 'Votre inscription à la newsletter a bien été confirmée.');
        return $this->redirectToRoute('app_index');
    }
}
