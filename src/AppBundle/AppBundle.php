<?php

namespace AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Codeages\PluginBundle\DependencyInjection\CodeagesPluginExtension;

class AppBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new CodeagesPluginExtension();
    }
}
