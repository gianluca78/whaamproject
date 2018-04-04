<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new FOS\RestBundle\FOSRestBundle(),
            new JMS\SerializerBundle\JMSSerializerBundle(),
            new Nelmio\ApiDocBundle\NelmioApiDocBundle(),
            new Nelmio\CorsBundle\NelmioCorsBundle(),
            new Ob\HighchartsBundle\ObHighchartsBundle(),
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            new BCC\ExtraToolsBundle\BCCExtraToolsBundle(),
            new WHAAM\PrivateApplication\Bundle\SecurityBundle\WHAAMPrivateApplicationSecurityBundle(),
            new WHAAM\PrivateApplication\Bundle\UserBundle\WHAAMPrivateApplicationUserBundle(),
            new WHAAM\WebService\Bundle\UserBundle\WHAAMWebServiceUserBundle(),
            new WHAAM\PrivateApplication\Bundle\BreadcrumbsBundle\WHAAMPrivateApplicationBreadcrumbsBundle(),
            new WHAAM\PrivateApplication\Bundle\ChildBundle\WHAAMPrivateApplicationChildBundle(),
            new WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\WHAAMPrivateApplicationChildBehaviorAssessmentBundle(),
            new WHAAM\WebService\Bundle\ChildBehaviorAssessmentBundle\WHAAMWebServiceChildBehaviorAssessmentBundle(),
            new WHAAM\PrivateApplication\Bundle\NotificationBundle\WHAAMPrivateApplicationNotificationBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');

        $localFile = (__DIR__.'/config/local_'.$this->getEnvironment().'.yml');

        if(is_file($localFile)) {
            $loader->load($localFile);
        }
    }
}
