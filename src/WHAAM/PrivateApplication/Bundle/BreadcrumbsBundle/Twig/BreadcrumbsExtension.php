<?php
namespace WHAAM\PrivateApplication\Bundle\BreadcrumbsBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class BreadcrumbsExtension extends \Twig_Extension
{

    private $breadcrumbs;
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->breadcrumbs = $this->container->get('whaam_breadcrumbs');
    }

    public function getBreadcrumbs()
    {
        return $this->breadcrumbs;
    }

    public function getFunctions()
    {
        return array(
            'get_breadcrumbs' => new \Twig_Function_Method($this, 'getBreadcrumbs', array("is_safe" => array("html"))),
            'render_breadcrumbs' => new \Twig_Function_Method($this, 'renderBreadcrumbs', array("is_safe" => array("html")))
        );
    }

    public function getName()
    {
        return 'breadcrumbs';
    }

    public function renderBreadcrumbs()
    {
        return $this->container->get('whaam_breadcrumbs.helper')->render();
    }

} 