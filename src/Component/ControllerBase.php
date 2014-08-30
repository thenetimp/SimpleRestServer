<?php
    
namespace Component;
    
/**
 *
 */
class ControllerBase {
    
    public function __construct()
    {
        echo "Controller Constructed";
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