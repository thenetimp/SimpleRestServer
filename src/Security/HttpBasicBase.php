<?php

namespace Security;

/**
 *
 */
class HttpBasicBase extends AbstractSecurity
{
    protected $realm;
    protected $authParams = array();
    protected $headers = array();


    public function __construct($securityConfig= array())
    {
        if(isset($securityConfig['realm'])) $this->realm = $securityConfig['realm'];
        if($this->realm == "") $this->realm="Secure Area";
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
            // $this->headers[] = "HTTP/1.0 401 Unauthorized";
            $this->headers[] = "HTTP/1.0 401 Unauthorized";
            $this->headers['WWW-Authenticate'] = 'Basic realm="' . $this->realm . '"';
            return false;
        }
    }
    
    /**
     *
     */
    public function getHeaders()
    {
        return $this->headers;
    }
    
    // /**
    //  *
    //  */
    // protected function validationPassed()
    // {
    //     return true;
    // }
}