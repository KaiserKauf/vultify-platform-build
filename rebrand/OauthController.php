<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpKernel\Exception\HttpException;

class OauthController extends Controller
{
    public function redirect(string $provider)
    {
        $socialite_provider = get_socialite_provider($provider);

        return $socialite_provider->redirect();
    }

    public function callback(string $provider)
    {
        try {
            $oauthUser = get_socialite_provider($provider)->user();
            $email = trim((string) $oauthUser->email);
            if ($email === '') {
                abort(403, 'OAuth provider did not return an email address');
            }
            $email = strtolower($email);
            $user = User::whereEmail($email)->first();
            if (! $user) {
                $settings = instanceSettings();
                if (! $settings->is_registration_enabled) {
                    abort(403, 'Registration is disabled');
                }

                $user = User::create([
                    'name' => $oauthUser->name,
                    'email' => $email,
                ]);
            }
            Auth::login($user);

            // Vultify addition: the 'ares' provider's OAuth access token IS a
            // real Ares session token (Ares' OIDC token endpoint issues one
            // directly via SessionManager.issue() instead of a throwaway
            // opaque token). Stash it so the embedded Ares panel (/ares) can
            // hand it to the chat iframe and skip a second, redundant login.
            if ($provider === 'ares' && ! empty($oauthUser->token)) {
                session(['ares_token' => $oauthUser->token]);
            }

            return redirect('/');
        } catch (\Exception $e) {
            $errorCode = $e instanceof HttpException ? 'auth.failed' : 'auth.failed.callback';

            return redirect()->route('login')->withErrors([__($errorCode)]);
        }
    }
}
