<?php

namespace Backstage\TwoFactorAuth\Listeners;

use Backstage\TwoFactorAuth\Notifications\SendOTP;
use Laravel\Fortify\Events\TwoFactorAuthenticationChallenged;
use Laravel\Fortify\Events\TwoFactorAuthenticationEnabled;

class SendTwoFactorCodeListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TwoFactorAuthenticationChallenged | TwoFactorAuthenticationEnabled $event): void
    {
        /** @var mixed $user */
        $user = $event->user;
        $user->notify(app(config('filament-2fa.send_otp_class') ?? SendOTP::class));
    }
}
