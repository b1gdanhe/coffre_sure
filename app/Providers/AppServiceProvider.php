<?php

namespace App\Providers;

use Illuminate\Support\Facades\Lang;
use Illuminate\Support\ServiceProvider;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Notifications\Messages\MailMessage;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        JsonResource::withoutWrapping();

        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->subject(Lang::get('Confirmez votre adresse email'))
                ->line(Lang::get('Veuillez cliquer sur le bouton ci-dessous pour vérifier votre adresse électronique.'))
                ->action(Lang::get('Vérifier l\'adresse e-mail'), $url)
                ->line(Lang::get('Si vous n\'avez pas créé de compte, aucune autre action n\'est requise.'))
                ->markdown(
                    'mails.emails.verify',
                    [
                        'action' => $url,
                        'user' => $notifiable
                    ]
                );
        });
    }
}
