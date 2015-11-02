<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2015 Michał Kopacz.
 * @author Michał Kopacz <michalkopacz.mk@gmail.com>
 */

namespace MostSignificantBit\OAuth2\Client\Persister;

use Carbon\Carbon as DateTime;
use MostSignificantBit\OAuth2\Client\Client;
use MostSignificantBit\OAuth2\Client\Grant\AccessTokenRequestAwareGrantInterface;
use MostSignificantBit\OAuth2\Client\Grant\RefreshToken\AccessTokenRequest;
use MostSignificantBit\OAuth2\Client\Grant\RefreshToken\RefreshTokenGrant;
use MostSignificantBit\OAuth2\Client\Parameter\AccessToken;
use MostSignificantBit\OAuth2\Client\Parameter\ExpiresIn;
use MostSignificantBit\OAuth2\Client\Parameter\RefreshToken;
use MostSignificantBit\OAuth2\Client\Persister\Storage\AccessTokenData;
use MostSignificantBit\OAuth2\Client\Persister\Storage\StorageInterface;
use MostSignificantBit\OAuth2\Client\Persister\User\Identity;
use MostSignificantBit\OAuth2\Client\Persister\User\IdentityProviderInterface;
use MostSignificantBit\OAuth2\Client\Persister\User\SimpleIdentityProvider;

class Persister
{
    protected $client;

    protected $storage;

    public function __construct(Client $client, StorageInterface $storage)
    {
        $this->client = $client;
        $this->storage = $storage;
    }

    /**
     * @param UserIdentity $userIdentity
     * @param AccessTokenRequestAwareGrantInterface $grant
     * @return AccessToken|null
     */
    public function obtainAccessToken(IdentityProviderInterface $identityProvider, AccessTokenRequestAwareGrantInterface $grant)
    {
        $accessTokenResponse = $this->client->obtainAccessToken($grant);

        $accessToken = $accessTokenResponse->getAccessToken();

        $accessTokenData = new AccessTokenData();
        $accessTokenData->setAccessToken($accessToken);
        $accessTokenData->setRefreshToken($accessTokenResponse->getRefreshToken());

        $expiresDate = $this->computeExpirationDate($accessTokenResponse->getExpiresIn());
        $accessTokenData->setExpirationDate($expiresDate);

        $this->storage->saveAccessTokenData($identityProvider->getIdentity($accessToken), $accessTokenData);

        return $accessToken;
    }

    /**
     * @param Identity $userIdentity
     * @return AccessToken|null
     */
    public function getAccessToken(Identity $userIdentity)
    {
        $accessTokenData = $this->storage->getAccessTokenData($userIdentity);

        if ($accessTokenData === null) {
            return null;
        }

        $expiresDate = $accessTokenData->getExpirationDate();

        if (!$expiresDate->isPast()) {
            return $accessTokenData->getAccessToken();
        }

        $refreshToken = $accessTokenData->getRefreshToken();

        if ($refreshToken !== null) {
            return $this->refreshAccessToken($userIdentity, $refreshToken);
        }

        return null;
    }

    /**
     * @param ExpiresIn $expiresIn
     * @return DateTime
     */
    protected function computeExpirationDate(ExpiresIn $expiresIn)
    {
        return $this->getDateTimeWithCurrentTime()->addSeconds($expiresIn->getValue());
    }

    /**
     * @return DateTime
     */
    protected function getDateTimeWithCurrentTime()
    {
        return DateTime::now();
    }

    /**
     * @param Identity $userIdentity
     * @param RefreshToken $refreshToken
     * @return AccessToken|null
     */
    protected function refreshAccessToken(Identity $userIdentity, RefreshToken $refreshToken)
    {
        $accessTokenRequest = new AccessTokenRequest($refreshToken);
        $grant = new RefreshTokenGrant($accessTokenRequest);

        $identityProvider = new SimpleIdentityProvider($userIdentity);

        return $this->obtainAccessToken($identityProvider, $grant);
    }
} 