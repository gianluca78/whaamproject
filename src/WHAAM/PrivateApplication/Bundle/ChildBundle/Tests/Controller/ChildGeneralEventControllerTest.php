<?php

namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Controller;

class ChildGeneralEventControllerTest extends \PHPUnit_Extensions_SeleniumTestCase
{
    protected $captureScreenshotOnFailure = TRUE;
    protected $screenshotPath = '/var/www/test-funzionali/whaam/screenshots';
    protected $screenshotUrl = 'http://localhost/test-funzionali/whaam/screenshots';

    protected function setUp()
    {
        $this->setBrowser("*chrome");
        $this->setBrowserUrl("https://app.whaamproject.eu");
    }



    public function testNew()
    {
        $this->login();
        $this->open("app/children");
        $this->waitForPageToLoad(30000);

        $this->click("link=Case data");
        $this->waitForPageToLoad(30000);

        $this->click("xpath=(//a[contains(text(),'New')])[3]");
        $this->waitForPageToLoad(30000);

        $this->select("id=childGeneralEvent_date_day", "label=6");
        $this->select("id=childGeneralEvent_date_month", "label=May");
        $this->select("id=childGeneralEvent_date_year", "label=2014");

        $this->type("id=childGeneralEvent_description", "test");

        $this->click("id=childGeneralEvent_submit");
        $this->waitForPageToLoad(30000);
        $this->assertEquals("Success! The event has been created successfully", $this->getText("xpath=(//div[@class='Message Information'])[1]"));

    }

    public function testView()
    {
        $this->login();

        $this->open("app/children");
        $this->waitForPageToLoad(30000);

        $this->click("link=Case data");
        $this->waitForPageToLoad(30000);

        $this->click("xpath=(//td[3]/a)");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("xpath=(//div[@class='Box-table-row' and //text()='6 May 2014'])");
        $this->assertElementPresent("xpath=(//div[@class='Box-table-row' and //text()='test'])");
    }



        public function testEdit()
        {
            $this->login();

            $this->open("app/children");
            $this->waitForPageToLoad(30000);

            $this->click("link=Case data");
            $this->waitForPageToLoad(30000);

            $this->click("xpath=(//td[3]/a[2])");
            $this->waitForPageToLoad(30000);

            $this->type("id=childGeneralEvent_description", "");
            $this->click("id=childGeneralEvent_submit");
            $this->waitForPageToLoad(30000);
            $this->assertElementPresent("xpath=(//div[@class='MessageRequired' and //text()='Required field'])");

            $this->type("id=childGeneralEvent_description", "test test");
            $this->click("id=childGeneralEvent_submit");
            $this->waitForPageToLoad(30000);
            $this->assertElementPresent("xpath=(//div[@class='Message Information' and //text()='Success! The event has been updated'])[1]");

        }

        public function testDelete()
        {
            $this->login();

            $this->open("app/children");
            $this->waitForPageToLoad(30000);

            $this->click("link=Case data");
            $this->waitForPageToLoad(30000);

            $this->assertElementPresent("xpath=(//div[@id='MainBody']/div/div[2]/div[3]/div[2]/table/tbody/tr[2]/td[3]/a[3])");
            $this->click("xpath=(//div[@id='MainBody']/div/div[2]/div[3]/div[2]/table/tbody/tr[2]/td[3]/a[3])");

            $this->assertElementPresent("dialog-confirm");
            $this->click("xpath=(//button[@type='button'])[2]");
            $this->waitForPageToLoad(30000);
            $this->assertElementPresent("xpath=(//div[@class='Message Information' and //text()='The event has been deleted'])[1]");

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