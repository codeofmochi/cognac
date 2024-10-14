<?php

namespace App\Notifier;

use Symfony\Component\Notifier\Message\EmailMessage;
use Symfony\Component\Notifier\Recipient\EmailRecipientInterface;
use Symfony\Component\Security\Http\LoginLink\LoginLinkDetails;
use Symfony\Component\Security\Http\LoginLink\LoginLinkNotification;

class CustomLoginLinkNotification extends LoginLinkNotification
{
    // Twig context variables to be displayed in email
    private array $context;

    public function __construct(LoginLinkDetails $loginLinkDetails, string $subject, array $context)
    {
        parent::__construct($loginLinkDetails, $subject);
        $this->context = $context;
    }

    public function asEmailMessage(EmailRecipientInterface $recipient, ?string $transport = null): ?EmailMessage
    {
        $emailMessage = parent::asEmailMessage($recipient, $transport);

        // get the NotificationEmail object and override the template template
        $email = $emailMessage->getMessage();
        /** @var NotificationEmail $email */
        $email->htmlTemplate('emails/login_link.html.twig');
        $email->textTemplate('emails/login_link.txt.twig');
        $email->context($this->context);

        return $emailMessage;
    }
}