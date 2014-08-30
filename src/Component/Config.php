<?php

namespace Component;

class Config {
    
    /**
     * Append configuration file to the configuration array
     */
    public static function appendConfiguration($config = array(), $filepath = "")
    {
        // Check that the filepath and make sure the file exists.
        if(!file_exists($filepath) && !is_readable($filepath))
            throw new Exception("Config file is not defined, does not exist, or is un-readable");

        $file = parse_ini_file($filepath, true);
        
        return array_merge($file, $config);
    }
    
    /**
     * Grab the $_SERVER environment data and store in the object.
     */
    public static function processServerEnv()
    {
        if(isset($_SERVER))
            $server = $_SERVER;
            unset($_SERVER);
            return $server;
    }

    /**
     * Grab the $_GET environment data and store in the object and unset the variable.
     */
    public static function processGetRequestVars()
    {
        if(isset($_GET))
            $get = array_merge($_GET, array());
            unset($_GET);
            return $get;
    }

    /**
     * Grab the $_POST environment data and store in the object and unset the variable.
     */
    public static function processPostRequestVars()
    {
        if(isset($_POST))
            $post = array_merge($_POST, array());
            unset($_POST);
            return $post;
    }
}