<?php

declare(strict_types=1);

namespace App\User;

use App\Entity\User;
use App\Mail\MailService;
use Symfony\Contracts\Translation\TranslatorInterface;

class UserMailService
{
    public function __construct(
        private readonly MailService $mailService,
        private readonly TranslatorInterface $translator,
        private readonly string $locale,
        private readonly string $frontendDomain,
        private readonly bool $useSSL,
    ) {
    }

    public function sendPasswordResetEmail(User $user): void
    {
        $params = [
            '{user_name}' => $user->getGivenName(),
            '{reset_url}' => \sprintf(
                '%s/password/reset/%s/%s',
                $this->getFrontendUrl(),
                $user->getPasswordResetToken(),
                $user->getEmail(),
            ),
        ];

        $this->mailService->sendEmail(
            $this->translator->trans('mails.password_request.subject', [], 'messages', $this->locale),
            $this->translator->trans('mails.password_request.message', $params, 'messages', $this->locale),
            $user->getEmail(),
            \sprintf(
                '%s %s',
                $user->getGivenName(),
                $user->getFamilyName(),
            ),
        );
    }

    protected function getFrontendUrl(): string
    {
        $protocol = $this->useSSL ? 'https' : 'http';

        return \sprintf(
            '%s://%s',
            $protocol,
            $this->frontendDomain,
        );
    }
}
