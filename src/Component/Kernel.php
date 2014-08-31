<?php

namespace Component;

use \ArrayObject as ArrayObject;
use \Exception as Exception;
use Component\Config as Config;
use Component\ControllerBase as ControllerBase;
use Component\Header as Header;

/**
 * Kernel Class
 */
class Kernel
{
    /**
     * Object variables
     */
    protected $baseDir = null;
    protected $config = array();
    protected $headers = array();
    
    /**
     * Construct the kernel
     */
    public function __construct($baseDir = "", $debug = false)
    {
        $response = array(
            'succeed' => false,
            'message' => ""
        );
        
        try {
            if(!file_exists($baseDir) && !is_readable($baseDir))
                throw new Exception("REF #00001: Base directory is not defined, does not exist, or is un-readable");
            $this->baseDir = $baseDir;
            $this->configure();
            $responseData = $this->findControllerByRequestUri();
            
            $response['succeed'] = "true";
            $response['message'] = 'succeed';
            
            $responseCodeHeader = null;
            $headers = array();
            
            if(count($this->headers) > 0)
            {
                foreach($this->headers as $key => $val)
                {
                    if($key == '0')
                    {
                        $responseCodeheader = $val;
                        
                        list($protocol, $code, $message) = split(" ", $responseCodeheader);

                        switch($code)
                        {
                            case '401':
                                $response['succeed'] = "false";
                                $response['message'] = $message; 
                        }
                    }
                    else
                    {
                        $headers[] = $key . ': ' . $val;
                    }
                }
            }

            header($responseCodeHeader);
            foreach($headers as $header)
            {
                header($header);
            }
                
            $response['data'] = $responseData;
        }
        catch (Exception $e)
        {
            $response['message'] = $e->getMessage();
            header('HTTP/1.1 500 Internal Server Error'); 
        }
        
        
        echo json_encode($response);
    }
    
    /**
     * Configure the application
     */
    protected function configure()
    {
        global $_HEADER;
        
        $_HEADER = new ArrayObject(getallheaders());
        
        if(!file_exists($this->baseDir . '/app/conf') && !is_readable($this->baseDir . 'app/conf'))
            throw new Exception("REF #00002: Config directory is not defined, does not exist, or is un-readable");

        if(file_exists($this->baseDir . '/app/conf/srs.ini') && is_readable($this->baseDir . 'app/conf/srs.ini'))
        {
            $this->config = Config::appendConfiguration($this->config, $this->baseDir . '/app/conf/srs.ini');
        }
        else
        {
            $this->config = Config::appendConfiguration($this->config, $this->baseDir . '/app/conf/database.ini');
            $this->config = Config::appendConfiguration($this->config, $this->baseDir . '/app/conf/security.ini');
        }
    }
    
    /**
     * Discover name of the controller to call and the action.
     */
    protected function findControllerByRequestUri()
    {

        // Get the parameters
        $parameters =split('/', substr(strtok($_SERVER['REQUEST_URI'],'?'),1));

        // Define the controller
        $controller = array_shift($parameters);
        if($controller == "") $controller = "DefaultController";

        // Define the action
        $action = "index";
        
        if(count($parameters) >= 1)
        {
            $action = array_shift($parameters);
        }

        // Genterate the controller base name and action.
        $controllerName = ControllerBase::generateControllerName($controller);
        $controllerAction = ControllerBase::generateControllerAction($action,
            $_SERVER['REQUEST_METHOD']);

        // Generate the new controller class name
        $controllerName = 'Bundle\Controller\\' . $controllerName;

        // Check if the class name exists if it does not throw an exception.
        if(!class_exists($controllerName)) throw new Exception("REF #00003: Can not complete your request.  Endpoint does not exist");

        // Check that the action method exists in the controller class
        if(!in_array($controllerAction, get_class_methods($controllerName))) throw new Exception("REF #00004: Can not complete your request.  Endpoint does not exist");

        // Instanciate the new controller
        $controller = new $controllerName($this->config);
        
        $headers = array();
        
        if(!call_user_func_array(array($controller, 'authorized'),array()))
        {
            $this->headers = call_user_func_array(array($controller, 'getHeaders'),array());
            return @call_user_func_array(array($controller, $controllerAction), $parameters);
        }
        else
        {
            // Call the controller action on the controller object.
            return @call_user_func_array(array($controller, $controllerAction), $parameters);
        }
    }
    
    public function getConfig()
    {
        return $this->config;
    }
}