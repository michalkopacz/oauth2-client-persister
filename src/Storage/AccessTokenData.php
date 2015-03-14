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

class AccessTokenData
{
    /**
     * @var AccessToken
     */
    protected $accessToken;

    /**
     * @var RefreshToken
     */
    protected $refreshToken;

    /**
     * @var DateTime
     */
    protected $expirationDate;

    /**
     * @param \MostSignificantBit\OAuth2\Client\Parameter\AccessToken $accessToken
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @return \MostSignificantBit\OAuth2\Client\Parameter\AccessToken
     */
    public function getAccessToken()
    {
        return $this->accessToken;
    }

    /**
     * @param \MostSignificantBit\OAuth2\Client\Parameter\RefreshToken $refreshToken
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return \MostSignificantBit\OAuth2\Client\Parameter\RefreshToken
     */
    public function getRefreshToken()
    {
        return $this->refreshToken;
    }

    /**
     * @param \Carbon\Carbon $expirationDate
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;
    }

    /**
     * @return \Carbon\Carbon
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }
} 