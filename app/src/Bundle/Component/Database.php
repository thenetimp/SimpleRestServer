<?php
    
namespace Bundle\Component;

use Component\AbstractDatabase as AbstractDatabase;

class Database extends AbstractDatabase
{
    public function __construct($databaseConfig= array())
    {
        // construct your database configuration here and put 
        // and link it's resource to $this->resourse
    }
}