<?php

namespace Component;
use \Exception as Exception;


class Kernel {
    
    public function __construct($baseDir = "", $debug = false)
    {
        if(!file_exists($baseDir) && !is_readable($baseDir))
            throw new Exception("Base directory is not defined, does not exist, or is un-readable");

        $this->configure();
        
        
    }
    
    
    protected function configure($baseDir = "")
    {

        if(!file_exists($baseDir . '/app/conf') && !is_readable($baseDir . 'app/conf'))
            throw new Exception("Base directory is not defined, does not exist, or is un-readable");



        // $this->config = new Config();
    }
    
}