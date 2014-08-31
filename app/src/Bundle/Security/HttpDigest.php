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
        
        // Delete this once and write your own code.  This is for testing purposes
        // any username with the password testing will validate, but your should
        // access your database and retieve and return the Ha1 hash from there.
        return $this->generatePasswordHashForUsernamePassword($username, "testing");
    }
}