<?php declare(strict_types=1);

namespace Neusta\JmsSerializerExtensionBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class NeustaJmsSerializerExtensionBundle extends Bundle
{
    public function getPath(): string
    {
        return \dirname(__DIR__);
    }
}
