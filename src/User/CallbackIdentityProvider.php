<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2015 Michał Kopacz.
 * @author Michał Kopacz <michalkopacz.mk@gmail.com>
 */

namespace MostSignificantBit\OAuth2\Client\Persister\User;


use MostSignificantBit\OAuth2\Client\Parameter\AccessToken;

class CallbackIdentityProvider implements IdentityProviderInterface
{
    /**
     * @var Identity
     */
    protected $callback;

    public function __construct(callable $callback)
    {
        $this->callback = $callback;
    }

    public function getIdentity(AccessToken $accessToken)
    {
        return call_user_func_array($this->callback, array($accessToken));
    }
}