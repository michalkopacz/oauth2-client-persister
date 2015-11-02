<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2015 Michał Kopacz.
 * @author Michał Kopacz <michalkopacz.mk@gmail.com>
 */

namespace MostSignificantBit\OAuth2\Client\Persister\Storage;

use Carbon\Carbon as DateTime;
use MostSignificantBit\OAuth2\Client\Parameter\AccessToken;
use MostSignificantBit\OAuth2\Client\Parameter\RefreshToken;
use MostSignificantBit\OAuth2\Client\Persister\User\Identity;

class MemoryStorage implements StorageInterface
{
    protected $data;

    /**
     * @param Identity $userIdentity
     * @param AccessTokenData $accessTokenData
     * @return bool
     */
    public function saveAccessTokenData(Identity $userIdentity, AccessTokenData $accessTokenData)
    {
        $this->data[$userIdentity->getUserId()] =  array(
            'access_token'         => $accessTokenData->getAccessToken()->getValue(),
            'refresh_token'        => $accessTokenData->getRefreshToken() !== null ? $accessTokenData->getRefreshToken()->getValue() : null,
            'expiration_timestamp' => $accessTokenData->getExpirationDate()->getTimestamp(),
        );
    }

    /**
     * @param Identity $userIdentity
     * @return AccessTokenData|null
     */
    public function getAccessTokenData(Identity $userIdentity)
    {
        if (!isset($this->data[$userIdentity->getUserId()])) {
            return null;
        }

        $rawData = $this->data[$userIdentity->getUserId()];

        $accessTokenData =  new AccessTokenData();
        $accessTokenData->setAccessToken(new AccessToken($rawData['access_token']));

        if ($rawData['refresh_token'] !== null) {
            $accessTokenData->setRefreshToken(new RefreshToken($rawData['refresh_token']));
        }

        $accessTokenData->setExpirationDate(DateTime::createFromTimestamp($rawData['expiration_timestamp']));

        return $accessTokenData;
    }
}