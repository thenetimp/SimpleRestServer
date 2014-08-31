<?php
    
namespace Component;
    
/**
 *
 */
class ControllerBase
{

    protected $config = array();
    protected $responseHeaders = array();
    protected $disabledSecurityActions = array();
    protected $dbr = null;
    
    /**
     *
     */
    public function __construct($config = array(), $dbResource = null)
    {
        global $_HEADER;
        $this->config = $config;
        $this->dbr = $dbResource;
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
    
    public function authorized($controllerAction)
    {
        // Security should be first in the config so we can send headers.
        if($this->config['security']['enabled'] && !in_array($controllerAction, $this->disabledSecurityActions))
        {
            $securityClass = $this->config['security']['security_class'];
            $security = new $securityClass($this->config['security']);
            
            if(!$security->authorized())
            {
                $this->responseHeaders = $security->getHeaders();
                return false;
            }
        }
        return true;
    }
}