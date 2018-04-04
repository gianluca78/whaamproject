<?php
namespace WHAAM\PrivateApplication\Bundle\BreadcrumbsBundle\Template;

use Symfony\Component\Templating\Helper\Helper;
use Symfony\Component\Templating\EngineInterface;
use WHAAM\PrivateApplication\Bundle\BreadcrumbsBundle\Model\Breadcrumbs;

class BreadcrumbsHelper extends Helper {

    private $templating;
    private $breadcrumbs;

    public function __construct(EngineInterface $templating, Breadcrumbs $breadcrumbs)
    {
        $this->templating = $templating;
        $this->breadcrumbs = $breadcrumbs;
    }

    public function getName() {
        return 'breadcrumbs';
    }

    public function render() {
        return $this->templating->render(
            'WHAAMPrivateApplicationBreadcrumbsBundle::breadcrumbs.html.twig',
            array(
                'breadcrumbs' => $this->breadcrumbs->getBreadcrumbs()
            )
        );
    }
} 