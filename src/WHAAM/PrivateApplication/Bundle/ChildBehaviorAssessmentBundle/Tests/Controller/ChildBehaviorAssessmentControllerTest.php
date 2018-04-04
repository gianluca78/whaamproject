<?php

namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAsssessmentBundle\Controller;

use Symfony\Component\Validator\Constraints\Date;
use DateTime;

class ChildBehaviorAssessmentControllerTest extends \PHPUnit_Extensions_SeleniumTestCase
{
    protected $captureScreenshotOnFailure = TRUE;
    protected $screenshotPath = '/var/www/test-funzionali/whaam/screenshots';
    protected $screenshotUrl = 'http://localhost/test-funzionali/whaam/screenshots';

    protected function setUp()
    {
        $this->setBrowser("*chrome");
        $this->setBrowserUrl("https://app.whaamproject.eu");
    }

    public function testIndex()
    {
        $this->login();

        $this->open("app/children");
        $this->waitForPageToLoad(30000);

        $this->assertEquals("test", $this->getText("css=div.Tool a"));
        $this->assertEquals("My children", $this->getText("css=div#MenuBar div.breadcrumbs div a"));
        $this->assertEquals("List", $this->getText("css=div#MenuBar div.title h1"));

        $this->click("//tr[2]/td[4]/a");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("link=Assessments");

        $this->click("link=Assessments");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("link=New assessment");
    }

    public function testNew()
    {
        $this->login();

        $this->open("app/children");
        $this->waitForPageToLoad(30000);

        $this->assertEquals("test", $this->getText("css=div.Tool a"));
        $this->assertEquals("My children", $this->getText("css=div#MenuBar div.breadcrumbs div a"));
        $this->assertEquals("List", $this->getText("css=div#MenuBar div.title h1"));


        $this->click("//tr[2]/td[4]/a");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("link=Assessments");
        $this->click("link=Assessments");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("link=New assessment");
        $rows = $this->getXpathCount("//table[@class='tcontent']/tbody/tr");
        $this->click("link=New assessment");
        $this->waitForPageToLoad(30000);
        $this->assertXpathCount("//table[@class='tcontent']/tbody/tr", $rows+1);
    }

   private function login()
   {
       $this->open("app/login");
       $this->type("name=_username", "test");
       $this->type("name=_password", "test");
       $this->click("//button[@type='submit']");
       $this->waitForPageToLoad(30000);
   }
}