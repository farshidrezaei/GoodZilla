<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;
use Illuminate\Auth\Notifications\VerifyEmail as VerifyEmailBase;
use Lang;

class VerifyApiEmail extends VerifyEmailBase
{

    /**
     * Get the notification's channels.
     *
     * @param mixed $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Build the mail representation of the notification.
     *
     * @param mixed $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {


        return (new MailMessage)
            ->subject('[' . env('APP_NAME') . ']' . Lang::get('Please verify your device'))
            ->line(Lang::get('Hi, To complete the registration, enter the verification code on the registration wizard form.'))
            ->line(Lang::get('Verification code: ') . $notifiable->verification_code)
            ->line(Lang::get('Thanks.'));
    }

    /**
     * Get the verification URL for the given notifiable.
     *
     * @param mixed $notifiable
     * @return string
     */
    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'auth.email.verification.verify', Carbon::now()->addMinutes(60), [
            'id' => $notifiable->getKey()
        ]); // this will basically mimic the email endpoint with get request
    }


}
