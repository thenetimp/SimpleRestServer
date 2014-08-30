<?php

namespace Component;
use \Exception as Exception;
use Component\Config as Config;


class Kernel {
    
    protected $config = array();
    
    public function __construct($baseDir = "", $debug = false)
    {
        if(!file_exists($baseDir) && !is_readable($baseDir))
            throw new Exception("Base directory is not defined, does not exist, or is un-readable");
        
        $this->baseDir = $baseDir;
        $this->configure();
        
        
    }
    
    protected function configure()
    {
        if(!file_exists($this->baseDir . '/app/conf') && !is_readable($this->baseDir . 'app/conf'))
            throw new Exception("Config directory is not defined, does not exist, or is un-readable");

        $this->config = Config::appendConfiguration($this->config, $this->baseDir . '/app/conf/database.ini');
    }
    
}