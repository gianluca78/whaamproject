<?php

namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Controller;

class ChildMedicationControllerTest extends \PHPUnit_Extensions_SeleniumTestCase
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

        $this->click("xpath=(//a[contains(text(),'New')])[2]");
        $this->waitForPageToLoad(30000);

        $this->type("id=childMedication_name", "aspirina");
        $this->type("id=childMedication_dosage", "1 cp");
        $this->type("id=childMedication_frequency", "2 volte al giorno");
        $this->select("id=childMedication_startDate_day", "label=4");
        $this->select("id=childMedication_startDate_month", "index=12");
        $this->select("id=childMedication_startDate_year", "index=20");

        $this->select("id=childMedication_endDate_day", "label=4");
        $this->select("id=childMedication_endDate_month", "index=12");
        $this->select("id=childMedication_endDate_year", "index=21");

        $this->click("id=childMedication_submit");
        $this->waitForPageToLoad(30000);
        $this->assertEquals("Success! The medication section has been created successfully", $this->getText("xpath=(//div[@class='Message Information'])[1]"));

    }

    public function testView()
    {
        $this->login();

        $this->open("app/children");
        $this->waitForPageToLoad(30000);

        $this->click("link=Case data");
        $this->waitForPageToLoad(30000);

        $this->click("xpath=(//td[6]/a)");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("xpath=(//div[@class='Box-table-row' and //text()='aspirina'])");
        $this->assertElementPresent("xpath=(//div[@class='Box-table-row' and //text()='1 cp'])");
        $this->assertElementPresent("xpath=(//div[@class='Box-table-row' and //text()='2 volte al giorno'])");
        $this->assertElementPresent("xpath=(//div[@class='Box-table-row' and //text()='4 Dec 2014'])");

    }



            public function testEdit()
            {
                $this->login();

                $this->open("app/children");
                $this->waitForPageToLoad(30000);

                $this->click("link=Case data");
                $this->waitForPageToLoad(30000);

                $this->click("xpath=(//td[6]/a[2])");
                $this->waitForPageToLoad(30000);

                $this->type("id=childMedication_name", "");
                $this->click("id=childMedication_submit");
                $this->waitForPageToLoad(30000);
                $this->assertElementPresent("xpath=(//div[@class='MessageRequired' and //text()='Required field'])");

                $this->type("id=childMedication_name", "Betotal");
                $this->click("id=childMedication_submit");
                $this->waitForPageToLoad(30000);
                $this->assertElementPresent("xpath=(//div[@class='Message Information' and //text()='Success! The medication section has been updated successfully'])[1]");

            }

                 public function testDelete()
                 {
                     $this->login();

                     $this->open("app/children");
                     $this->waitForPageToLoad(30000);

                     $this->click("link=Case data");
                     $this->waitForPageToLoad(30000);

                     $this->assertElementPresent("xpath=(//td[6]/a[3])");
                     $this->click("xpath=(//td[6]/a[3])");

                     $this->assertElementPresent("dialog-confirm");
                     $this->click("xpath=(//button[@type='button'])[2]");
                     $this->waitForPageToLoad(30000);
                     $this->assertElementPresent("xpath=(//div[@class='Message Information' and //text()='The medication has been deleted'])[1]");

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