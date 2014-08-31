Simple Rest Server
================

A simple rest server written in PHP.  After frustrations with other php frameworks to do things that should be relatively simple I have created Simple Rest Server framework.  It is not tied to any specific ORM.  Forces very little upon the programmer.  It provides methods of authentication via http_basic and http_digest authentication built in, and provides a way to extend security so a user could tie it into another authentication method.

The Core of the framework is stored in the src/ directory and consists of 5 files over 2 directories.  The Kernel which manages everything.  The ControllerBase which controllers extend to provide functionality.

Anatomy of a Request.
=======================
Say your API service is located at https://api.foo.bar/ and someone wants to call the resource /user/profile/1.  The request URL contains the information needed to route the request to the right controller and action.

  * user = Controller  = app/src/Bundle/Controler/UserController.php
  * profile = action = profileGetAction
  * 1 = parameter 1 of the action method
  * additional segments will become additional parameters in the method.

The request URI is broken up like this. method, ction and them parameters therefore to use a GET method on /user/profile/1 the method name in UserController is profileGetAction()
    
    <?php
    
        class UserController 
        {
            public function profileGetAction($userId = 0)
            {
                $data = array();
                
                //  Code here to get the data that is needed to get the profile.

                // $data is returned at the end and json encoded by 
                //the kernel $data can be an array or a valid object.
                return $data;
            }
        }
        
The request URI is broken up like this. amethod, ction and them parameters therefore to use a GET method on /user/profile/1/test/34 the method name in UserController is profileGetAction() and the 3 following parameters are

  * $userId = 1
  * $service = test
  * $roleId = 34
  
    <?php
    
        class UserController 
        {
            public function profileGetAction($userId = 0, $service = null, $roleId)
            {
                $data = array();
                
                //  Code here to get the data that is needed to get the profile.
                // $data is returned at the end and json encoded by 
                //the kernel $data can be an array or a valid object.
                return $data;
            }
        }        
        


Database
========

Database connectivity is designed so you can use whatever database connectivity you want.  Doctrine? Propel? adodb? basic mysql function calls.  All supported as it is up to you to create the code to connect to the resource.  In the database.ini file 2 parameters are required the others are optional based on your chosen database.  if "enabled" is set to "true" then SRS will try to create the database resourse using the class specified in "handler_class".  In the example below it is set to "Bundle\Component\Database".

    [database]
    enabled=true
    handler_class=Bundle\Component\Database
    username=root
    password=
    hostname=localhost
    database=srs_db
    portnumb=3306

The class file "Bundle\Component\Database" might look something like this to connect to a mysql database.

    <?php
    
    namespace Bundle\Component;

    use Component\AbstractDatabase as AbstractDatabase;

    class Database extends AbstractDatabase
    {
        public function __construct($dbCfg= array())
        {
            //connection to the database
            $mysqli = new \mysqli($dbCfg['hostname'], $dbCfg['username'], $dbCfg['password'], $dbCfg['database']);
            if ($mysqli->connect_errno) {
                throw new Exception("Unable to connect to the database with given credentials.");
            }

            $this->dbResource = $mysqli            
        }
    }

From this point the $mysqli resource will be available as $this->dbr in the Controller file.


Security
========

By default security is disabled.  To enable it you must change the settings in app/conf/security.ini

    [security]
    enabled=false   // change to true to enable it.
    realm=Secure Area // security realm
    security_class=Bundle\Security\HttpDigest // the class to manage security
    
The securit_class must extend AbstractSecurity abstract class.  The controller calls the "authorized" method on the security class and returns tru if authorized and false if not authorzed.  All security work must be done within that file.  By default SRS supports 2 authentication methods and provides 2 template classes to over-ride.

http_basic authentication
-------------------------

SRS comes with a template file for Http basic authentication.  In order to use it you must provide logic for validatePasses method.  In php http basic username and password are stored in the $_SERVER global variable with keys 'PHP_AUTH_USER' and 'PHP_AUTH_PW'.

    <?php
        namespace Bundle\Security;

        use Security\HttpBasicBase as HttpBasicBase;

        class HttpBasic extends HttpBasicBase
        {
            protected function validationPassed()
            {
                $username = $_SERVER['PHP_AUTH_USER'];
                $password = $_SERVER['PHP_AUTH_PW'];
                
                // not a real life example of querying the database....
                $row = (perform your database query here and get the first row);
                
                if($row['password'] == hash('md5',$password))
                {
                    return true;
                }

                return false;
            }
        }
        

http_digest authentication
--------------------------

SRS also comes with a template for basic http_digest authentication.

    <?php

    namespace Bundle\Security;

    use Security\HttpDigestBase as HttpDigestBase;

    /**
     *
     */
    class HttpDigest extends HttpDigestBase
    {
        /**
         *  User defined function to allow getting the password from the database.
         */
        public function getPasswordHashForUsername($username)
        {
            // Query a query to get the password hash from the database for the user and 
            // return it.  No other validation is required here as the base class does the validation.
            // the password stored in your database MUST adhere to the below algorithm
            // hash('md5', $username . ':' . $digestRealm . ':' . $password);
            // $digestRealm should always be the same as the "realm" parameter in the security.ini file.
            // It is important that this does not change as it will break all previously generated passwords.
            // You should however set the realm to something different than the default to help secure your API

            return $passwordHash;
        }
    }
    
Extending Security
--------------------------

Creating a custom class that extends security should extend Security\AbstractSecurity class.  The new Security class will need to have a public function authorized() which returns true if passed authorization process or false if it failed authorization process.  The 







    