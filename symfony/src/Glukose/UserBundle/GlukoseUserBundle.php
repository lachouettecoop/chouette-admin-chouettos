<?php

namespace Glukose\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class GlukoseUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }

}
