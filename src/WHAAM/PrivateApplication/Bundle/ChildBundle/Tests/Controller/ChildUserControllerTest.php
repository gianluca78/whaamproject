<?php

namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Controller;

class ChildUserControllerTest extends \PHPUnit_Extensions_SeleniumTestCase
{
    protected $captureScreenshotOnFailure = TRUE;
    protected $screenshotPath = '/var/www/test-funzionali/whaam/screenshots';
    protected $screenshotUrl = 'http://localhost/test-funzionali/whaam/screenshots';

    protected function setUp()
    {
        $this->setBrowser("*chrome");
        $this->setBrowserUrl("https://app.whaamproject.eu");
    }

    public function testEdit()
    {
        $this->login();

        $this->open("app/children");
        $this->waitForPageToLoad(30000);

        $this->assertEquals("test", $this->getText("css=div.Tool a"));
        $this->assertEquals("My children", $this->getText("css=div#MenuBar div.breadcrumbs div a"));
        $this->assertEquals("List", $this->getText("css=div#MenuBar div.title h1"));

        $hrefNew = $this->getAttribute("//a[contains(@href, '/view')]/@href");

        $hrefIndex = $this->getAttribute("//td[5]/a/@href");
        $hrefEdit = str_replace("index", "edit", $hrefIndex);

        $this->click("link=Child network");
        $this->waitForPageToLoad(30000);

        $this->open($hrefEdit);
        $this->waitForPageToLoad(30000);

        $this->select("id=childUser_role", "index=8");
        $this->click("id=childUser_submit");
        $this->waitForPageToLoad(30000);
        $this->assertEquals("Success! The role has been updated successfully", $this->getText("xpath=(//div[@class='Message Information'])[1]"));

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