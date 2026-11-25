<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordGermanNotification extends Notification
{
    use Queueable;

    public function __construct(
        public string $token,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ]);

        $expireMinutes = config('auth.passwords.'.config('auth.defaults.passwords').'.expire', 60);

        return (new MailMessage)
            ->subject('Passwort zurücksetzen — fail4free.de')
            ->greeting('Hallo!')
            ->line('Du erhältst diese E-Mail, weil wir eine Anfrage zum Zurücksetzen deines Passworts erhalten haben.')
            ->action('Passwort zurücksetzen', $url)
            ->line("Dieser Link ist {$expireMinutes} Minuten lang gültig.")
            ->line('Falls du kein Zurücksetzen angefordert hast, kannst du diese E-Mail einfach ignorieren.')
            ->salutation('Viele Grüße, fail4free.de');
    }
}
