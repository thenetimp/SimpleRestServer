<?php

namespace Security;

abstract class AbstractSecurity { 

    // Construct function takes in an array called securityConfig
    abstract function __construct($securityConfig= array());

    // Function returns true if authorized false if not authorized
    abstract function authorized();
} 