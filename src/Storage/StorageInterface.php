<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2015 Michał Kopacz.
 * @author Michał Kopacz <michalkopacz.mk@gmail.com>
 */

namespace MostSignificantBit\OAuth2\Client\Persister\Storage;

use MostSignificantBit\OAuth2\Client\Persister\User\Identity;

interface StorageInterface
{
    /**
     * @param Identity $userIdentity
     * @param AccessTokenData $accessTokenData
     * @return bool
     */
    public function saveAccessTokenData(Identity $userIdentity, AccessTokenData $accessTokenData);

    /**
     * @param Identity $userIdentity
     * @return AccessTokenData
     */
    public function getAccessTokenData(Identity $userIdentity);
} 