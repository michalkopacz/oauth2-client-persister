<?php
/**
 * @license   http://opensource.org/licenses/BSD-3-Clause BSD-3-Clause
 * @copyright Copyright (c) 2015 Michał Kopacz.
 * @author Michał Kopacz <michalkopacz.mk@gmail.com>
 */

namespace MostSignificantBit\OAuth2\Client\Persister\User;


class Identity
{
    protected $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function getUserId()
    {
        return $this->id;
    }
} 