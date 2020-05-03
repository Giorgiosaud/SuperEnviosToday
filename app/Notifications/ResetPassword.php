<?php

namespace App\Notifications;

use Illuminate\Auth\Notifications\ResetPassword as ResetPasswordLaravel;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Lang;

class ResetPassword extends ResetPasswordLaravel
{
    /**
     * Build the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return MailMessage
     */
    public function toMail($notifiable)
    {
        if (static::$toMailCallback) {
            return call_user_func(static::$toMailCallback, $notifiable, $this->token);
        }

        if (static::$createUrlCallback) {
            $url = call_user_func(static::$createUrlCallback, $notifiable, $this->token);
        } else {
            $url = url(config('app.url').route('password.reset', [
                    'token' => $this->token,
                    'email' => $notifiable->getEmailForPasswordReset(),
                ], false));
        }

        return (new MailMessage)
            ->subject(Lang::get('auth.RESET:SUBJECT'))
            ->line(Lang::get('auth.RESET:LINE:ONE'))
            ->action(Lang::get('auth.RESET:EMAIL:BUTTON'), $url)
            ->line(Lang::get('auth.RESET:VALIDATION', ['count' => config('auth.passwords.'.config('auth.defaults.passwords').'.expire')]))
            ->line(Lang::get('auth.RESET:EMAIL:LAST'));
    }
}
