Simple Rest Server
================

A simple rest server written in PHP.  After frustrations with other php frameworks to do things that should be relatively simple I have created Simple Rest Server framework.  It is not tied to any specific ORM.  Forces very little upon the programmer.  It provides methods of authentication via http_basic and http_digest authentication built in, and provides a way to extend security so a user could tie it into another authentication method.

The Core of the framework is stored in the src/ directory and consists of 5 files over 2 directories.  The Kernel which manages everything.  The ControllerBase which controllers extend to provide functionality.

Anatomy of a Request.
=======================
Say your API service is located at https://api.foo.bar/ and someone wants to call the resource /user/profile/1.  The request URL contains the information needed to route the request to the right controller and action.

    user = Controller  = app/src/Bundle/Controler/UserController.php
    profile = action = profileGetAction
    1 = parameter 1 of the action method
    
The method is 3 parts.  The action + method + Action therefore to use a GET method on /user/profile/1 the method name in UserController is profileGetAction()
    
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


Security
========

By default security is disabled.  To enable it you must change the settings in app/conf/security.ini

    [security]
    enabled=false   // change to true to enable it.
    security_class=Bundle\Security\HttpDigest // the class to manage security
    
The securit_class must extend AbstractSecurity abstract class (too be defined and created soon).  The controller calls the "authorized" method on the security class and returns tru if authorized and false if not authorzed.  All security work must be done within that file.  By default SRS supports 2 authentication methods and provides 2 template classes to over-ride.

http_basic authentication
=========================

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
                
                // Make sure to cleans your input.
                $sql = "select passwordhash from users where username='" . $username . "'";
                
                // not a real life example of querying the database....
                $row = (perform your query here and get the first row);
                
                if($row['password'] == hash('md5',$password))
                {
                    return true;
                }

                return false;
            }
        }
        

http_digest authentication
=========================
(more information soon)


