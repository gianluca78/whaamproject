<?php
namespace WHAAM\PrivateApplication\Bundle\BreadcrumbBundle\Tests\Model;

use WHAAM\PrivateApplication\Bundle\BreadcrumbsBundle\Util\YamlLoader;

class YamlLoaderTest extends \PHPUnit_Framework_TestCase {

    public function testConstruct() {
        $yamlLoader = new YamlLoader(__DIR__ . '/../../Resources/config/breadcrumbs.yml');

        $this->assertFalse(empty($yamlLoader));
    }

    /**
     * @expectedException Symfony\Component\Filesystem\Exception\FileNotFoundException
     * @expectedExceptionMessage Breadcrumbs configuration file not found
     */
    public function testConstructException() {
        $yamlLoader = new YamlLoader(__DIR__ . '/../../Resources/config/non-existent-file.yml');
    }

    /**
     * @expectedException Symfony\Component\Yaml\Exception\ParseException
     */
    public function testGetCollectionForBundleControllerAction()
    {
        $yamlLoader = new YamlLoader(__DIR__ . '/../../Resources/config/breadcrumbs.yml');

        $yamlLoader->getCollectionForBundleControllerAction('User:User:NonExistentAction');

        $validAction = $yamlLoader->getCollectionForBundleControllerAction('User:User:index');

        $this->assertFalse(empty($validAction));
    }
} 