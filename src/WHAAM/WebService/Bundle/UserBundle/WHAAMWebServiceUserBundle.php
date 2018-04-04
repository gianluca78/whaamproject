<?php

namespace WHAAM\WebService\Bundle\UserBundle;

use WHAAM\PrivateApplication\Common\Security\Factory\WsseFactory;
use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class WHAAMWebServiceUserBundle extends Bundle
{
    /**
     * @param ContainerBuilder $container
     * @todo to be moved in a security bundle
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }
}
