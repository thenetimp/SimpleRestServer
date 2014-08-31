<?php

namespace Security;

/**
 *
 */
class HttpDigestBase
{
    protected $realm;
    protected $authParams = array();
    protected $headers = array();

    /**
     *
     */
    public function __construct($realm="")
    {
        // Make sure the realm is 
        if($realm == "") $realm = "Secure Area";
        $this->realm = $realm;
    }

    /**
     *
     */
    public function authorized()
    {
        global $_HEADER;

        if(isset($_HEADER['Authorization']))
        {
            if($this->validationPassed())
            {
                return true;
            }

            $this->headers[] = "HTTP/1.0 401 Unauthorized";
            return false;
        }
        else
        {
            $nonce = hash('sha512', $this->randomString());
            $opaque = hash('sha512', $this->randomString());
            $this->headers[] = "HTTP/1.0 401 Unauthorized";
            $this->headers['WWW-Authenticate'] = ' Digest realm="' . $this->realm . '",
                    nonce="' . $nonce . '"';

            // $this->headers['WWW-Authenticate'] = ' Digest realm="' . $this->realm . '",
            //         qop="auth,auth-int",
            //         nonce="' . $nonce . '",
            //         opaque="' . $opaque . '"';

            return false;
        }
    }
    
    /**
     *
     */
    protected function validationPassed()
    {
        
        $this->authArray = $this->generateAuthDigestArray();
        $ha1 = $this->getPasswordHashForUsername($this->authArray['username']);
        $ha2 = hash('md5', $_SERVER['REQUEST_METHOD'] . ':' . $this->authArray['uri']);

        // If these match then we have validated the password as matching.
        if($this->authArray['response'] == hash('md5', $ha1 . ':' . $this->authArray['nonce'] . ':' . $ha2))
        {
            return true;
        }
        
        // Return false because we aren't validated.
        return false;        
    }
    
    /**
     *
     */
    public function getHeaders()
    {
        return $this->headers;
    }
    
    /**
     *
     */
    protected function randomString($bits = 256)
    {
        $bytes = ceil($bits / 8);
        $return = '';
        for ($i = 0; $i < $bytes; $i++) {
            $return .= chr(mt_rand(0, 255));
        }
        return $return;
    }
    
    /**
     *
     */
    protected function generateAuthDigestArray()
    {
        $authArray = array();
        $segments = split(', ', $_SERVER['PHP_AUTH_DIGEST']);

        echo '<pre>';

        foreach($segments as $segment)
        {
            list($key, $value) =split('=', $segment);
            $authArray[$key] = str_replace("\"", "", $value);
        }

        return $authArray;
    }

    /**
     * If using http_digest passwords must be hashed with the following function before storing
     * in the database.  Do not use an me5 hash on the password prior to this hash as it will
     * fail any authentication.  Changing the realm will in turn break any previously generated
     * passwords.
     */
    public function generatePasswordHashForUsernamePassword($username, $password)
    {
        return hash('md5', $this->authArray['username'] . ':' . $this->realm . ':' . $password);
    }
    
}