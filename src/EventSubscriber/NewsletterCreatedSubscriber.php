<?php

namespace App\EventSubscriber;

use App\Event\NewsletterSubscribedEvent;
use App\Mail\Newsletter\SubscribedConfirmation;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Notifier\Bridge\Discord\DiscordOptions;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordEmbed;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordFieldEmbedObject;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordFooterEmbedObject;
use Symfony\Component\Notifier\Bridge\Discord\Embeds\DiscordMediaEmbedObject;
use Symfony\Component\Notifier\ChatterInterface;
use Symfony\Component\Notifier\Message\ChatMessage;
use Symfony\Component\String\ByteString;

class NewsletterCreatedSubscriber implements EventSubscriberInterface
{
    public function __construct(private EntityManagerInterface $entityManager, private SubscribedConfirmation $subscribedConfirmation, private ChatterInterface $chatter)
    {
    }

    public function sendConfirmationEmail(NewsletterSubscribedEvent $event): void
    {
        $newsletter = $event->getNewsletter();

        $newsletter->setAuthToken(ByteString::fromRandom(32)->toString());

        $this->entityManager->flush();

        $this->subscribedConfirmation->sendTo($newsletter);
    }

    public function sendDiscordNotification(NewsletterSubscribedEvent $event): void
    {
        $newsletter = $event->getNewsletter();
        $chatMessage = new ChatMessage('');

        $discordOptions = (new DiscordOptions())
            ->username('Human Botster')
            ->addEmbed((new DiscordEmbed())
                    ->color(2021216)
                    ->title('Nouvel email dans la newsletter !')
                    ->thumbnail((new DiscordMediaEmbedObject())
                        ->url('https://humanbooster.com/wp-content/uploads/2021/02/intro-image.png'))
                    ->addField((new DiscordFieldEmbedObject())
                            ->name('Email')
                            ->value($newsletter->getEmail())
                            ->inline(true)
                    )
                    ->footer((new DiscordFooterEmbedObject())
                            ->text('HB Newsletter...')
                            ->iconUrl('https://humanbooster.com/wp-content/uploads/2021/04/cropped-Sans-titre-32x32.png')
                    )
            );

        $chatMessage->options($discordOptions);

        $this->chatter->send($chatMessage);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            NewsletterSubscribedEvent::NAME => [
                ['sendConfirmationEmail', 10],
                ['sendDiscordNotification', 5]
            ]

        ];
    }
}
