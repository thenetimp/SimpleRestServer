<?php

namespace Component;
use \Exception as Exception;
use Component\Config as Config;
use Component\ControllerBase as ControllerBase;


class Kernel {
    
    protected $baseDir = null;
    protected $config = array();
    protected $env = array();
    protected $request = array();
    
    public function __construct($baseDir = "", $debug = false)
    {
        try {
            if(!file_exists($baseDir) && !is_readable($baseDir))
                throw new Exception("Base directory is not defined, does not exist, or is un-readable");
            $this->baseDir = $baseDir;
            $this->configure();
            $this->findControllerByRequestUri();
        }
        catch (Exception $e)
        {
            echo $e->getMessage();
        }
    }
    
    /**
     * Configure the application
     */
    protected function configure()
    {
        if(!file_exists($this->baseDir . '/app/conf') && !is_readable($this->baseDir . 'app/conf'))
            throw new Exception("Config directory is not defined, does not exist, or is un-readable");

        if(file_exists($this->baseDir . '/app/conf/srs.ini') && is_readable($this->baseDir . 'app/conf/srs.ini'))
        {
            $this->config = Config::appendConfiguration($this->config, $this->baseDir . '/app/conf/srs.ini');
        }
        else
        {
            $this->config = Config::appendConfiguration($this->config, $this->baseDir . '/app/conf/database.ini');
            $this->config = Config::appendConfiguration($this->config, $this->baseDir . '/app/conf/security.ini');
        }
        
        // Process the server env variables.
        $this->env['server'] = Config::processServerEnv();

        // Process the server env variables.
        $this->request['get'] = Config::processGetRequestVars();

        // Process the server env variables.
        $this->request['post'] = Config::processPostRequestVars();
        
    }
    
    /**
     * Discover name of the controller to call and the action.
     */
    protected function findControllerByRequestUri()
    {
        // Remove the prepending 
        list($controller,$action) = split('/', substr($this->env['server']['REQUEST_URI'],1));

        $controllerName = ControllerBase::generateControllerName($controller);
        $controllerAction = ControllerBase::generateControllerAction($action,
            $this->env['server']['REQUEST_METHOD']);

        // Instanciate the new controller
        $controllerName = 'Bundle\Controller\\' . $controllerName;
        
        if(!class_exists($controllerName)) throw new Exception("Unable to instanciate controller object dynamically.  The class \"$controllerName\" does not exist.");
        
        $controller = new $controllerName();        
    }
    
}