<?php

namespace Backstage\TwoFactorAuth\Http\Livewire\Auth;

use Backstage\TwoFactorAuth\Enums\TwoFactorType;
use Backstage\TwoFactorAuth\Notifications\SendOTP;
use DanHarrin\LivewireRateLimiting\Exceptions\TooManyRequestsException;
use DanHarrin\LivewireRateLimiting\WithRateLimiting;
use Filament\Actions\Action;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Filament\Pages\Concerns\InteractsWithFormActions;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Cache;
use Laravel\Fortify\Http\Requests\TwoFactorLoginRequest;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Reactive;

class LoginTwoFactor extends Page implements HasActions, HasForms
{
    use InteractsWithFormActions;
    use InteractsWithForms;
    use WithRateLimiting;

    protected static string $layout = 'filament-2fa::layouts.login';

    protected static string $view = 'filament-2fa::auth.login-two-factor';

    protected static bool $shouldRegisterNavigation = false;

    public mixed $challengedUser = null;

    public ?string $twoFactorType = null;

    public ?string $code = null;

    #[Reactive]
    public int $lastResendTime = 0;

    public function mount(TwoFactorLoginRequest $request): void
    {
        if ($request->challengedUser()) {
            $this->challengedUser = $request->challengedUser();
            $this->twoFactorType = $this->challengedUser->two_factor_type?->value ?? TwoFactorType::email->value;

            if (! Cache::has('resend_cooldown_' . $this->challengedUser->id)) {
                Cache::put('resend_cooldown_' . $this->challengedUser->id, true, now()->addSeconds(30));
            }
        }
    }

    public function hasLogo(): bool
    {
        return false;
    }

    #[Computed]
    public function canResend(): bool
    {
        return ! Cache::has('resend_cooldown_' . $this->challengedUser->id);
    }

    public function resend(): Action
    {
        return Action::make('resend')
            ->label(__('Resend'))
            ->color('primary')
            ->extraAttributes(['class' => 'w-full text-xs'])
            ->link()
            ->disabled(fn () => ! $this->canResend())
            ->action(fn () => $this->handleResend());
    }

    public function handleResend(): void
    {
        if (! $this->canResend()) {
            return;
        }

        if (! $this->throttle()) {
            return;
        }

        $this->challengedUser->notify(app(SendOTP::class));

        Cache::put('resend_cooldown_' . $this->challengedUser->id, true, now()->addSeconds(30));

        $this->dispatch('resent');

        Notification::make()
            ->title(__('Successfully resend the OTP code'))
            ->success()
            ->send();
    }

    private function throttle(): bool
    {
        try {
            $this->rateLimit(1);

            return true;
        } catch (TooManyRequestsException $exception) {
            $translation = __('filament-panels::pages/auth/email-verification/email-verification-prompt.notifications.notification_resend_throttled');
            $translationArray = is_array($translation) ? $translation : [];

            Notification::make()
                ->title(__('filament-panels::pages/auth/email-verification/email-verification-prompt.notifications.notification_resend_throttled.title', [
                    'seconds' => $exception->secondsUntilAvailable,
                    'minutes' => $exception->minutesUntilAvailable,
                ]))
                ->body(array_key_exists('body', $translationArray) ?
                    __('filament-panels::pages/auth/email-verification/email-verification-prompt.notifications.notification_resend_throttled.body', [
                        'seconds' => $exception->secondsUntilAvailable,
                        'minutes' => $exception->minutesUntilAvailable,
                    ]) : [])
                ->danger()
                ->send();

            return false;
        }
    }

    protected function getFormSchema(): array
    {
        return [
            TextInput::make('code')
                ->label(__('Code'))
                ->required()
                ->extraInputAttributes([
                    'name' => 'code',
                    'autocomplete' => 'one-time-code',
                    'onchange' => 'document.getElementById("recovery_code").value = this.value',
                ])
                ->validationAttribute('authentication code'),
        ];
    }
}
