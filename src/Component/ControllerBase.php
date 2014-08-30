<?php
    
namespace Component;
    
/**
 *
 */
class ControllerBase {

    protected $config = array();
    
    public function __construct($config)
    {
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
}