<?php

declare(strict_types=1);

namespace TomoPongrac\WebshopApi;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class WebShopApiBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}