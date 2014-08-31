<?php

namespace Bundle\Security;

use Security\HttpDigestBase as HttpDigestBase;

/**
 *
 */
class HttpDigest extends HttpDigestBase
{
    /**
     *  User defined function to allow getting the password from the database.
     */
    public function getPasswordHashForUsername($username)
    {
        return $this->generatePasswordHashForUsernamePassword($username, "testing");
    }
}