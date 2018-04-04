<?php
namespace WHAAM\PrivateApplication\Bundle\BreadcrumbBundle\Tests\Model;

use WHAAM\PrivateApplication\Bundle\BreadcrumbsBundle\Model\Breadcrumbs;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class BreadcrumbsTest extends WebTestCase {

    protected static $breadcrumbs;
    protected static $container;

    public static function setUpBeforeClass()
    {
        $kernel = static::createKernel();
        $kernel->boot();

        self::$container = $kernel->getContainer();

        self::$breadcrumbs = self::$container->get('whaam_breadcrumbs');
    }

    /*
    public function testProcessUrls()
    {
        $company = self::$container->get('doctrine.orm.entity_manager')
            ->createQueryBuilder()
            ->select('c')
            ->from('GestionaleCivitaCompanyBundle:Company', 'c')
            ->setMaxResults(1)
            ->getQuery()
            ->getSingleResult()
        ;

        self::$breadcrumbs->load('Company:BeneficiaryCompany:edit');
        self::$breadcrumbs->processUrls(
            array('%beneficiary_company_name%' => array('slug' => $company->getSlug())),
            array('%beneficiary_company_name%' => 'test')
        );

        $breadcrumbs = self::$breadcrumbs->getBreadcrumbs();

        $this->assertTrue(array_key_exists('test', $breadcrumbs));
        $this->assertRegExp('/visualizza/', $breadcrumbs['test']);
    }
    */
}