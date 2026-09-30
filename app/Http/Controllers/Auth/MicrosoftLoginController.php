<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Support\Users\FindOrCreateUserByEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Contracts\User as SocialiteUser;
use Laravel\Socialite\Facades\Socialite;

class MicrosoftLoginController extends Controller
{
    public function redirect(): RedirectResponse
    {
        return Socialite::driver('microsoft')->redirect();
    }

    public function callback(FindOrCreateUserByEmail $resolverUsuario): RedirectResponse
    {
        /** @var SocialiteUser $cuentaMicrosoft */
        $cuentaMicrosoft = Socialite::driver('microsoft')->user();

        $user = $resolverUsuario->resolver(
            $cuentaMicrosoft->getEmail(),
            $cuentaMicrosoft->getName() ?: $cuentaMicrosoft->getEmail(),
            $cuentaMicrosoft->getId(),
        );

        if (! $user->active) {
            return redirect()->route('login')->withErrors([
                'email' => trans('auth.failed'),
            ]);
        }

        Auth::login($user, remember: true);

        return redirect()->intended(route('dashboard'));
    }
}
