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
    protected $dbResource = null;
    
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
            
            if(isset($this->config['database']['enabled']) && $this->config['database']['enabled'])
            {
                $this->configureDatabase();
            }
            
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
        
        
        if($response['succeed'] == "false")
        {
            unset($response['data']);
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
     * create the database object
     */
    public function configureDatabase()
    {
        // Get the database class and see if it exists.
        $dbClass = $this->config['database']['handler_class'];
        if(!isset($dbClass) || !class_exists($dbClass)) throw new Exception("REF 0005: Unable to create the database resource.");

        // Instanciate the database class
        $database = new $dbClass();

        // Get the database resource from the database object
        $this->dbResource =  $database->getResourceHandler();
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
        if($controller == "") $controller = "Default";

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
        $controllerName = 'Bundle\Controller\\' . $controllerName . 'Controller';

        // Check if the class name exists if it does not throw an exception.
        if(!class_exists($controllerName)) throw new Exception("REF #00003: Can not complete your request.  Endpoint does not exist");

        // Check that the action method exists in the controller class
        if(!in_array($controllerAction, get_class_methods($controllerName))) throw new Exception("REF #00004: Can not complete your request.  Endpoint does not exist");

        // Instanciate the new controller
        $controller = new $controllerName($this->config);
        
        $headers = array();
        
        if((isset($this->config['security']['enabled']) && $this->config['security']['enabled']) && !call_user_func_array(array($controller, 'authorized'), array($controllerAction)))
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