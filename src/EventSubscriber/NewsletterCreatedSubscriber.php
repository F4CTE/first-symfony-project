<?php

namespace App\EventSubscriber;

use App\Event\NewsletterSubscribedevent;
use App\Mail\Newsletter\SubscribedConfirmation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\String\ByteString;

class NewsletterCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $entityManager, private SubscribedConfirmation $subscribedConfirmation)
    {
        
    }

    public function onNewsletterSubscribed(NewsletterSubscribedevent $event): void
    {
        $newsletter = $event->getNewsletter();

        $newsletter->setAuthToken(ByteString::fromRandom(32)->toString());
        
        $this->entityManager->flush();

        $this->subscribedConfirmation->sendTo($newsletter);
        
    }

    public static function getSubscribedEvents(): array
    {
        return [
            'newsletter.subscribed' => 'onNewsletterSubscribed',
        ];
    }
}
