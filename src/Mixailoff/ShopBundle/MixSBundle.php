<?php

namespace Mixailoff\ShopBundle;

use /** @noinspection PhpUndefinedClassInspection */
    Symfony\Component\HttpKernel\Bundle\Bundle;

/** @noinspection PhpUndefinedClassInspection */
class MixSBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
