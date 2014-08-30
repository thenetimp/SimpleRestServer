<?php
    
namespace Bundle\Controller;
use Component\ControllerBase as ControllerBase;

class Testing extends ControllerBase
{
    public function crashingGetAction($param="", $param2="", $param3="")
    {
        echo '<pre>';
        print_r($this);
    }
}