<?php

namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Controller;

class ChildADHDDiagnosisControllerTest extends \PHPUnit_Extensions_SeleniumTestCase
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

        $this->click("xpath=(//a[contains(@href, '/app/child-diagnosis')])");
        $this->waitForPageToLoad(30000);

        $this->select("id=childADHDDiagnosis_diagnosisDate_day", "index=2");
        $this->select("id=childADHDDiagnosis_diagnosisDate_month", "index=5");
        $this->select("id=childADHDDiagnosis_diagnosisDate_year", "label=2014");
        $this->type("id=childADHDDiagnosis_onsetAge", "4");
        $this->select("id=childADHDDiagnosis_subtype", "index=2");

        $this->click("id=childADHDDiagnosis_submit");
        $this->waitForPageToLoad(30000);
        $this->assertEquals("Success! The diagnosis section has been created successfully", $this->getText("xpath=(//div[@class='Message Information'])[1]"));

    }

    public function testView()
    {
        $this->login();

        $this->open("app/children");
        $this->waitForPageToLoad(30000);

        $this->click("link=Case data");
        $this->waitForPageToLoad(30000);

        $this->click("xpath=(//a[contains(@class, 'icon-tool icon-tool-search')])");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("xpath=(//div[@class='Box-table-row' and //text()='2 May 2014'])");
        $this->assertElementPresent("xpath=(//div[@class='Box-table-row' and //text()='Predominantly hyperactive-impulsive'])");
        $this->assertElementPresent("xpath=(//div[@class='Box-table-row' and //text()='4'])");
    }

    public function testEdit()
    {
        $this->login();

        $this->open("app/children");
        $this->waitForPageToLoad(30000);

        $this->click("link=Case data");
        $this->waitForPageToLoad(30000);

        $this->click("xpath=(//a[contains(@class, 'icon-tool icon-tool-edit')])");
        $this->waitForPageToLoad(30000);

        $this->type("id=childADHDDiagnosis_onsetAge", "wrongvalue");
        $this->click("id=childADHDDiagnosis_submit");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//div[@class='MessageRequired' and //text()='This value is not valid.'])");

        $this->type("id=childADHDDiagnosis_onsetAge", "5");
        $this->click("id=childADHDDiagnosis_submit");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//div[@class='Message Information' and //text()='Success! The diagnosis has been updated successfully'])[1]");

    }

    public function testDelete()
    {
        $this->login();

        $this->open("app/children");
        $this->waitForPageToLoad(30000);

        $this->click("link=Case data");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("xpath=(//a[contains(@class, 'icon-tool icon-tool-delete')])");
        $this->click("xpath=(//a[contains(@class, 'icon-tool icon-tool-delete')])");

        $this->assertElementPresent("dialog-confirm");
        $this->click("xpath=(//button[@type='button'])[2]");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//div[@class='Message Information' and //text()='The diagnosis section has been deleted successfully'])[1]");

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