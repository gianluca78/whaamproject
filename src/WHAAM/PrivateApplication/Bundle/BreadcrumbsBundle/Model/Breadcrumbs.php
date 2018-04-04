<?php
namespace WHAAM\PrivateApplication\Bundle\BreadcrumbsBundle\Model;

use WHAAM\PrivateApplication\Common\Util\LocaleValidator,
    WHAAM\PrivateApplication\Bundle\BreadcrumbsBundle\Util\YamlLoader;
use Symfony\Component\Routing\Router,
    Symfony\Bundle\FrameworkBundle\Translation\Translator;

class Breadcrumbs {
    private $breadcrumbs = array();
    private $router;
    private $translator;
    private $yamlElements = array();
    private $yamlLoader;

    public function __construct(YamlLoader $yamlLoader, Router $router, LocaleValidator $localeValidator, Translator $translator) {
        $this->localeValidator = $localeValidator;
        $this->router = $router;
        $this->translator = $translator;
        $this->yamlLoader = $yamlLoader;
    }

    public function load($bundleControllerAction) {
        $this->yamlElements = $this->yamlLoader->getCollectionForBundleControllerAction($bundleControllerAction);

        return $this;
    }

    public function processUrls(array $routeAttributes = array(), array $variableLabels = array()) {
        $this->validateParameters($routeAttributes, $variableLabels);

        foreach($this->yamlElements as $label => $routeName) {
            if($this->isVariableLabel($label)) {
                $this->getBreadcrumbsElementWithVariableLabel($label, $variableLabels, $routeName, $routeAttributes);
            } else {
                $this->getBreadcrumbsElementWithoutVariableLabel($label, $routeName, $routeAttributes);
            }
        }
    }

    private function getBreadcrumbsElementWithVariableLabel($label, $variableLabels, $routeName=null, $routeAttributes)
    {
        $translatedLabel = $this->translator->trans($label, array(), 'interface');

        if($routeName) {
            $this->breadcrumbs[$variableLabels[$translatedLabel]] = $this->generateUrlFromRouteName(
                $label, $routeName, $routeAttributes
            );

            return $this;
        }

        $this->breadcrumbs[$variableLabels[$translatedLabel]] = null;

        return $this;
    }

    private function getBreadcrumbsElementWithoutVariableLabel($label, $routeName=null, $routeAttributes)
    {
        $translatedLabel = $this->translator->trans($label, array(), 'interface');

        if($routeName) {
            $this->breadcrumbs[$translatedLabel] = $this->generateUrlFromRouteName(
                $label, $routeName, $routeAttributes
            );

            return $this;
        }

        $this->breadcrumbs[$translatedLabel] = null;

        return $this;

    }

    private function isVariableLabel($label)
    {
        if(preg_match('/^%[\s\S]*%$/', $label)) {
            return true;
        }

        return false;
    }

    private function generateUrlFromRouteName($label, $routeName, $routeAttributes) {
        if(in_array($label, array_keys($routeAttributes))) {
            return $this->router->generate($routeName, $routeAttributes[$label]);
        }

        return $this->router->generate($routeName);
    }

    private function validateParameters(array $routeAttributes, array $variableLabels) {
        if(array_keys($routeAttributes) != array_intersect(array_keys($routeAttributes), array_keys($this->yamlElements))) {
            throw new \Exception('Invalid route attributes passed to the function');
        }

        $configurationVariableLabels= array_filter(array_keys($variableLabels), function ($label) {
           return preg_match('/^%[\s\S]*%$/', $label);
        });

        $diff = array_diff($configurationVariableLabels, array_keys($variableLabels));

        if(!empty($diff)) {
            throw new \Exception('A label variable is missing');
        }
    }

    public function getBreadcrumbs() {
        return $this->breadcrumbs;
    }
} 