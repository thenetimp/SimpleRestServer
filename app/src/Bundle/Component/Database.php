<?php
    
namespace Bundle\Component;

use \Exception as Exception;
use Component\AbstractDatabase as AbstractDatabase;

class Database extends AbstractDatabase
{
    public function __construct($dbCfg= array())
    {
        
        //connection to the database
        $mysqli = new \mysqli($dbCfg['hostname'], $dbCfg['username'], $dbCfg['password'], $dbCfg['database'], $dbCfg['port']);
        
        if ($mysqli->connect_errno) {
            throw new Exception("Unable to connect to the database with given database name.");
        }

        $this->dbResource = $mysqli;
        
    }
}