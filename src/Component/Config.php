<?php

namespace Component;

/**
 *
 */
class Config
{
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
}