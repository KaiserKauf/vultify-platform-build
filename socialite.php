<?php

use App\Models\OauthSetting;
use Laravel\Socialite\Facades\Socialite;

function get_socialite_provider(string $provider)
{
    $oauth_setting = OauthSetting::firstWhere('provider', $provider);

    if (! filled($oauth_setting->redirect_uri)) {
        $oauth_setting->update(['redirect_uri' => route('auth.callback', $provider)]);
    }

    if ($provider === 'azure') {
        $azure_config = new \SocialiteProviders\Manager\Config(
            $oauth_setting->client_id,
            $oauth_setting->client_secret,
            $oauth_setting->redirect_uri,
            ['tenant' => $oauth_setting->tenant],
        );

        return Socialite::driver('azure')->setConfig($azure_config);
    }

    if ($provider == 'authentik' || $provider == 'clerk') {
        $authentik_clerk_config = new \SocialiteProviders\Manager\Config(
            $oauth_setting->client_id,
            $oauth_setting->client_secret,
            $oauth_setting->redirect_uri,
            ['base_url' => $oauth_setting->base_url],
        );

        return Socialite::driver($provider)->setConfig($authentik_clerk_config);
    }

    if ($provider == 'zitadel') {
        $zitadel_config = new \SocialiteProviders\Manager\Config(
            $oauth_setting->client_id,
            $oauth_setting->client_secret,
            $oauth_setting->redirect_uri,
            ['base_url' => $oauth_setting->base_url],
        );

        return Socialite::driver('zitadel')->setConfig($zitadel_config);
    }

    if ($provider == 'google') {
        $google_config = new \SocialiteProviders\Manager\Config(
            $oauth_setting->client_id,
            $oauth_setting->client_secret,
            $oauth_setting->redirect_uri
        );

        return Socialite::driver('google')
            ->setConfig($google_config)
            ->with(['hd' => $oauth_setting->tenant]);
    }

    if ($provider === 'ares') {
        // Ares' minimal OIDC provider — see ares/auth/oidc.py in KaiserKauf/ares.
        // Fixed endpoint URLs (not host-configurable via base_url) since this
        // integration targets a single known Ares instance, not a generic
        // multi-tenant OIDC issuer.
        return new class(
            app('request'),
            $oauth_setting->client_id,
            $oauth_setting->client_secret,
            $oauth_setting->redirect_uri
        ) extends \Laravel\Socialite\Two\AbstractProvider {
            protected $scopeSeparator = ' ';

            protected $scopes = ['openid', 'email', 'profile'];

            protected function getAuthUrl($state)
            {
                return $this->buildAuthUrlFromBase('https://ares.vultify.io/oauth/authorize', $state);
            }

            protected function getTokenUrl()
            {
                return 'https://ares.vultify.io/oauth/token';
            }

            protected function getUserByToken($token)
            {
                $response = $this->getHttpClient()->get('https://ares.vultify.io/oauth/userinfo', [
                    \GuzzleHttp\RequestOptions::HEADERS => [
                        'Authorization' => 'Bearer '.$token,
                    ],
                ]);

                return json_decode((string) $response->getBody(), true);
            }

            protected function mapUserToObject(array $user)
            {
                return (new \Laravel\Socialite\Two\User)->setRaw($user)->map([
                    'id' => $user['sub'] ?? null,
                    'email' => $user['email'] ?? null,
                    'name' => $user['name'] ?? ($user['email'] ?? null),
                ]);
            }
        };
    }

    $config = [
        'client_id' => $oauth_setting->client_id,
        'client_secret' => $oauth_setting->client_secret,
        'redirect' => $oauth_setting->redirect_uri,
    ];

    $provider_class_map = [
        'bitbucket' => \Laravel\Socialite\Two\BitbucketProvider::class,
        'discord' => \SocialiteProviders\Discord\Provider::class,
        'github' => \Laravel\Socialite\Two\GithubProvider::class,
        'gitlab' => \Laravel\Socialite\Two\GitlabProvider::class,
        'infomaniak' => \SocialiteProviders\Infomaniak\Provider::class,
    ];

    $socialite = Socialite::buildProvider(
        $provider_class_map[$provider],
        $config
    );

    if ($provider == 'gitlab' && ! empty($oauth_setting->base_url)) {
        $socialite->setHost($oauth_setting->base_url);
    }

    return $socialite;
}
