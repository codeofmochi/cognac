<?php

namespace App\Controller;

use App\Notifier\CustomLoginLinkNotification;
use App\Repository\UserRepository;
use App\Service\ApiResponseBuilder;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Notifier\NotifierInterface;
use Symfony\Component\Notifier\Recipient\Recipient;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\LoginLink\LoginLinkHandlerInterface;
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
        ApiResponseBuilder $apiResponse,
    ): Response {
        $email = $request->getPayload()->get('email');
        $user = $userRepository->findOneBy(['email' => $email]);

        if ($user == null) {
            return $apiResponse->error('USER_NOT_FOUND', $translator->trans("auth.login.user_not_found"), Response::HTTP_NOT_FOUND);
        }

        // invalidate previous login links (persist to ensure consistency)
        $user->setLastLinkRequestedAt(new DateTimeImmutable());
        $entityManager->persist($user);
        $entityManager->flush();

        $loginLinkDetails = $loginLinkHandler->createLoginLink($user);

        $notification = new CustomLoginLinkNotification(
            $loginLinkDetails,
            $translator->trans('auth.login.email_subject'),
            [
                "userName" => $user->getFirstName(),
                "appDomain" => $request->getHost(),
                "loginUrl" => $loginLinkDetails->getUrl(),
            ]
        );

        $recipient = new Recipient($user->getEmail());
        $notifier->send($notification, $recipient);

        return $apiResponse->create([
            'message' => $translator->trans('auth.login.email_sent'),
        ]);
    }
    
    #[Route('/login_check', name: 'login_check')]
    public function check(): never
    {
        // The login link authenticator should intercept this call, we only define the route
        // see https://symfony.com/doc/current/security/login_link.html#1-configure-the-login-link-authenticator
        throw new \LogicException('This code should never be reached');
    }
}
