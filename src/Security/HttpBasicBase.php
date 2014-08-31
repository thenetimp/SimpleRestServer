<?php

namespace Security;

/**
 *
 */
class HttpBasicBase
{
    protected $realm;
    protected $authParams = array();
    protected $headers = array();


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