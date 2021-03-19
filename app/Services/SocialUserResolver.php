<?php
/**
 * Created by PhpStorm.
 * User: athasamid
 * Date: 07/01/19
 * Time: 09.15
 */

namespace App\Services;


use Hivokas\LaravelPassportSocialGrant\Resolvers\SocialUserResolverInterface;
use Illuminate\Contracts\Auth\Authenticatable;
use Laravel\Socialite\Facades\Socialite;

class SocialUserResolver implements SocialUserResolverInterface
{

    /**
     * Resolve user by provider credentials.
     *
     * @param string $provider
     * @param string $accessToken
     *
     * @return Authenticatable|null
     */
    public function resolveUserByProviderCredentials(string $provider, string $accessToken): ?Authenticatable
    {
        $providerUser = null;

        try{
            $providerUser = Socialite::driver($provider)->userFromToken($accessToken);
        }catch(\Exception $e){

        }

        if ($providerUser)
            return (new SocialAccountService())->findOrCreate($providerUser, $provider);

        return null;
    }
}