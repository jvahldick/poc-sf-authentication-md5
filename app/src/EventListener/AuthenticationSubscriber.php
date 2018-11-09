<?php

declare(strict_types=1);

namespace App\EventListener;

use App\Entity\User;
use App\Event\UserAuthenticatedEvent;
use App\Repository\UserRepository;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AuthenticationSubscriber implements EventSubscriberInterface
{
    private $userRepository;
    private $passwordEncoder;

    public function __construct(
        UserRepository $repository,
        UserPasswordEncoderInterface $passwordEncoder

    ) {
        $this->userRepository = $repository;
        $this->passwordEncoder = $passwordEncoder;
    }

    public static function getSubscribedEvents()
    {
        return [
            UserAuthenticatedEvent::class => 'onUserAuthentication'
        ];
    }

    public function onUserAuthentication(UserAuthenticatedEvent $event)
    {
        $request = $event->getRequest();
        $user = $event->getUser();

        if (!$user instanceof User) {
            throw new \RuntimeException('Invalid user.');
        }

        if (false === $user->isLegacyUser()) {
            return;
        }

        // Change the encryption of the legacy password
        $user->setLegacyUser(false);

        $plainPassword = $request->request->get('_password');
        $encodedPassword = $this->passwordEncoder->encodePassword($user, $plainPassword);

        $user->setPassword($encodedPassword);

        $this->userRepository->update($user);
    }
}
