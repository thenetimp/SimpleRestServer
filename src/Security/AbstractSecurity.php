<?php

namespace Security;

abstract class AbstractSecurity { 

    // Construct function takes in an array called securityConfig
    public function __construct($securityConfig= array(), $dbr = null)
    {
        $this->dbr = $dbr;
    }

    // Function returns true if authorized false if not authorized
    abstract public function authorized();
} 