<?php

namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Controller;

class ChildSchoolInformationControllerTest extends \PHPUnit_Extensions_SeleniumTestCase
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

        $this->click("xpath=(//a[contains(text(),'New')])[4]");
        $this->waitForPageToLoad(30000);

        $this->type("id=childSchoolInformation_year", "2014-15");
        $this->type("id=childSchoolInformation_schoolName", "Michelangelo");
        $this->type("id=childSchoolInformation_grade", "4");

        $this->click("id=childSchoolInformation_submit");
        $this->waitForPageToLoad(30000);
        $this->assertEquals("Success! The school information section has been created successfully", $this->getText("xpath=(//div[@class='Message Information'])[1]"));

    }

    public function testView()
    {
        $this->login();

        $this->open("app/children");
        $this->waitForPageToLoad(30000);

        $this->click("link=Case data");
        $this->waitForPageToLoad(30000);

        $this->click("xpath=(//div[4]/div[2]/table/tbody/tr[2]/td[4]/a)");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("xpath=(//div[@class='Box-table-row' and //text()='2014-15'])");
        $this->assertElementPresent("xpath=(//div[@class='Box-table-row' and //text()='Michelangelo'])");
        $this->assertElementPresent("xpath=(//div[@class='Box-table-row' and //text()='4'])");

    }



        public function testEdit()
        {
            $this->login();

            $this->open("app/children");
            $this->waitForPageToLoad(30000);

            $this->click("link=Case data");
            $this->waitForPageToLoad(30000);

            $this->click("xpath=(//div[4]/div[2]/table/tbody/tr[2]/td[4]/a[2])");
            $this->waitForPageToLoad(30000);

            $this->type("id=childSchoolInformation_schoolName", "");
            $this->click("id=childSchoolInformation_submit");
            $this->waitForPageToLoad(30000);
            $this->assertElementPresent("xpath=(//div[@class='MessageRequired' and //text()='Required field'])");

            $this->type("id=childSchoolInformation_schoolName", "Michelangelo Buonarroti");
            $this->click("id=childSchoolInformation_submit");
            $this->waitForPageToLoad(30000);
            $this->assertElementPresent("xpath=(//div[@class='Message Information' and //text()='Success! The scholastic information has been updated successfully'])[1]");

        }

            public function testDelete()
            {
                $this->login();

                $this->open("app/children");
                $this->waitForPageToLoad(30000);

                $this->click("link=Case data");
                $this->waitForPageToLoad(30000);

                $this->assertElementPresent("xpath=(//div[4]/div[2]/table/tbody/tr[2]/td[4]/a[3])");
                $this->click("xpath=(//div[4]/div[2]/table/tbody/tr[2]/td[4]/a[3])");

                $this->assertElementPresent("dialog-confirm");
                $this->click("xpath=(//button[@type='button'])[2]");
                $this->waitForPageToLoad(30000);
                $this->assertElementPresent("xpath=(//div[@class='Message Information' and //text()='The scholastic information has been deleted successfully'])[1]");

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