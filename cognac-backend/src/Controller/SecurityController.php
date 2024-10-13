<?php

namespace App\Controller;

use App\Repository\UserRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;
use Symfony\Contracts\Translation\TranslatorInterface;

class SecurityController extends AbstractController
{
    #[Route('/login', name: 'login', methods: ['POST'])]
    public function requestLoginLink(
        EntityManagerInterface $entityManager,
        LoginLinkHandlerInterface $loginLinkHandler,
        NotifierInterface $notifier,
        Request $request,
        TranslatorInterface $translator,
        UserRepository $userRepository,
    ): Response {
        $email = $request->getPayload()->get('email');
        $user = $userRepository->findOneBy(['email' => $email]);

        if ($user == null) {
            return new JsonResponse([
                'error' => [
                    'code' => 'USER_NOT_FOUND',
                    'message' => $translator->trans('auth.login.user_not_found'),
                ]
            ], Response::HTTP_NOT_FOUND);
        }

        // invalidate previous login links (persist to ensure consistency)
        $user->setLastLinkRequestedAt(new DateTimeImmutable());
        $entityManager->persist($user);
        $entityManager->flush();

        $loginLinkDetails = $loginLinkHandler->createLoginLink($user);

        $notification = new LoginLinkNotification(
            $loginLinkDetails,
            $translator->trans('auth.login.email_subject')
        );

        $recipient = new Recipient($user->getEmail());
        $notifier->send($notification, $recipient);

        return new JsonResponse([
            'message' => $translator->trans('auth.login.email_sent'),
        ]);
    }
}
