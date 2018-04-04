<?php
namespace WHAAM\PrivateApplication\Bundle\BreadcrumbsBundle\Util;

use Symfony\Component\Yaml\Yaml,
    Symfony\Component\Filesystem\Exception\FileNotFoundException,
    Symfony\Component\Yaml\Exception\ParseException;

class YamlLoader {

    private $yamlConfiguration;

    public function __construct($inputFile)
    {
        if(!file_exists($inputFile)) {
            throw new FileNotFoundException('Breadcrumbs configuration file not found');
        }

        $this->yamlConfiguration = Yaml::parse(file_get_contents($inputFile));
    }

    public function getCollectionForBundleControllerAction($bundleControllerAction)
    {
        list($bundle, $controller, $action) = explode(':', $bundleControllerAction);

        if(!isset($this->yamlConfiguration[$bundle][$controller][$action])) {
            throw new ParseException('It is not possible to parse the yaml file with the bundle:controller:action
                passed (' . $bundle . ':' . $controller . ':' . $action
            );
        }

        return $this->yamlConfiguration[$bundle][$controller][$action];
    }

} 