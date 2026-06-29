<?php

namespace App\Providers;

use App\Actions\Fortify\CreateNewUser;
use App\Actions\Fortify\ResetUserPassword;
use App\Actions\Fortify\UpdateUserPassword;
use App\Actions\Fortify\UpdateUserProfileInformation;
use App\Models\User;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\ValidationException;
use Laravel\Fortify\Fortify;
use Hash;

class FortifyServiceProvider extends ServiceProvider
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
        Fortify::createUsersUsing(CreateNewUser::class);
        Fortify::updateUserProfileInformationUsing(UpdateUserProfileInformation::class);
        Fortify::updateUserPasswordsUsing(UpdateUserPassword::class);
        Fortify::resetUserPasswordsUsing(ResetUserPassword::class);

        RateLimiter::for('login', function (Request $request) {
            $email = (string) $request->email;

            return Limit::perMinute(5)->by($email.$request->ip());
        });

        RateLimiter::for('two-factor', function (Request $request) {
            return Limit::perMinute(5)->by($request->session()->get('login.id'));
        });

        Fortify::authenticateUsing(function (Request $request) {
            $username = filter_var($request->email, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
            $user = User::where($username, $request->email)
                ->first();
            if (
                $user &&
                Hash::check(($request->password), $user->password)
            ) {
                // 2FA ist erforderlich - prüfen ob aktiviert
                if (empty($user->two_factor_secret)) {
                    // 2FA nicht aktiviert - Login verweigern mit Fehlermeldung
                    throw ValidationException::withMessages([
                        'email' => ['Die Zwei-Faktor-Authentifizierung ist für dieses Konto erforderlich. Bitte aktivieren Sie 2FA in Ihren Einstellungen.'],
                    ]);
                }
                
                // 2FA ist aktiviert - Fortify prüft automatisch den 2FA-Code
                return $user;
            }

        });

        // Two Factor Authentication Views
        Fortify::twoFactorChallengeView(function () {
            return view('auth.two-factor-challenge');
        });

        // Password Confirmation View
        Fortify::confirmPasswordView(function () {
            return view('auth.confirm-password');
        });
    }
}
