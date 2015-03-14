<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2015 Michał Kopacz.
 * @author Michał Kopacz <michalkopacz.mk@gmail.com>
 */

namespace MostSignificantBit\OAuth2\Client\Persister\User;

use MostSignificantBit\OAuth2\Client\Parameter\AccessToken;

class SimpleIdentityProvider implements IdentityProviderInterface
{
    /**
     * @var Identity
     */
    protected $userIdentity;

    public function __construct(Identity $userIdentity)
    {
        $this->userIdentity = $userIdentity;
    }


    public function getIdentity(AccessToken $accessToken)
    {
        return $this->userIdentity;
    }
}