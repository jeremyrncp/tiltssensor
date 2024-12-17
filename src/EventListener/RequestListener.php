<?php

namespace App\EventListener;

use App\Entity\User;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

final class RequestListener implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            RequestEvent::class => ['onKernelRequest', 17],
        ];
    }

    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();

        if ($request->getSession()) {
            // Set user's locale from session
            if ($locale = $request->getSession()->get('_locale')) {
                $request->setLocale($locale);
            }
        }
    }
}
