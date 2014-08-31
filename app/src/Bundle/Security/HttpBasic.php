<?php

namespace Bundle\Security;

use Security\HttpBasicBase as HttpBasicBase;

/**
 *
 */
class HttpBasic extends HttpBasicBase
{
    protected function validationPassed()
    {

        return false;
    }
}