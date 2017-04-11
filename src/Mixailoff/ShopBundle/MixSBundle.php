<?php

namespace Mixailoff\ShopBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MixSBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
