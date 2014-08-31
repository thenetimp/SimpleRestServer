<?php

namespace Component;

abstract class AbstractDatabase { 

    protected $link = null;

    // Construct function takes in an array called databaseConfig
    // It is in this function that the database connection should be constructed
    // and assigned to the protected link object variable.
    abstract public function __construct($databaseConfig= array());

    // Function that returns the database resource handler.
    public function getResourceHandler()
    {
        return $this->link;
    }
} 