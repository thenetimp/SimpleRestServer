<?php
    
namespace Component;
    
/**
 *
 */
class ControllerBase
{

    protected $responseHeaders = array();

    protected $config = array();
    
    /**
     *
     */
    public function __construct($config = array(), $secureActions = array())
    {
        global $_HEADER;
        $this->config = $config;
    }
    
    /**
     *
     */
    public static function generateControllerName($controllerName = "")
    {
        $controllerName = ucwords(strtolower($controllerName));
        return $controllerName;
    }

    /**
     *
     */
    public static function generateControllerAction($actionName="", $requestMethod = "GET")
    {
        // Format the action and request method
        $actionName = strtolower($actionName);
        $requestMethod = ucwords(strtolower($requestMethod));

        // Assemble and return the action name
        return $actionName . $requestMethod . 'Action';
    }
    
    public function getHeaders()
    {
        return $this->responseHeaders;
    }
    
    public function authorized()
    {
        // Security should be first in the config so we can send headers.
        if($this->config['security']['enabled_globally'])
        {
            $securityClass = $this->config['security']['security_class'];
            $security = new $securityClass();
            
            if(!$security->authorized())
            {
                $this->responseHeaders = $security->getHeaders();
                return false;
            }
        }
        return true;
    }
}