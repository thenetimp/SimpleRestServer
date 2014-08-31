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
    
    The method is 3 parts.  The action + method + Action therefore to use a GET method 
    on /user/profile/1 the method name in UserController is profileGetAction()
    
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









