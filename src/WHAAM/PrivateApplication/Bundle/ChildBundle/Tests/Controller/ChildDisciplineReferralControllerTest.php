<?php

namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Controller;

class ChildDisciplineReferralControllerTest extends \PHPUnit_Extensions_SeleniumTestCase
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

        $this->click("xpath=(//a[contains(@href, '/app/child-discipline-referral')])");
        $this->waitForPageToLoad(30000);

        $this->select("id=childDisciplineReferral_date_day", "label=6");
        $this->select("id=childDisciplineReferral_date_month", "label=May");
        $this->select("id=childDisciplineReferral_date_year", "label=2014");
        $this->select("id=childDisciplineReferral_disciplineReferralType", "index=6");
        $this->type("id=childDisciplineReferral_motivation", "test");

        $this->click("id=childDisciplineReferral_submit");
        $this->waitForPageToLoad(60000);
        $this->assertEquals("Success! The discipline referrals section has been created with successfully", $this->getText("xpath=(//div[@class='Message Information'])[1]"));

    }

    public function testView()
    {
        $this->login();

        $this->open("app/children");
        $this->waitForPageToLoad(30000);

        $this->click("link=Case data");
        $this->waitForPageToLoad(30000);

        $this->click("xpath=(//div[5]/div[2]/table/tbody/tr[2]/td[4]/a)");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("xpath=(//div[@class='Box-table-row' and //text()='6 May 2014'])");
        $this->assertElementPresent("xpath=(//div[@class='Box-table-row' and //text()='Parent Contact'])");
        $this->assertElementPresent("xpath=(//div[@class='Box-table-row' and //text()='test'])");
    }



        public function testEdit()
        {
            $this->login();

            $this->open("app/children");
            $this->waitForPageToLoad(30000);

            $this->click("link=Case data");
            $this->waitForPageToLoad(30000);

            $this->click("xpath=(//div[5]/div[2]/table/tbody/tr[2]/td[4]/a[2])");
            $this->waitForPageToLoad(30000);

            $this->type("id=childDisciplineReferral_motivation", "");
            $this->click("id=childDisciplineReferral_submit");
            $this->waitForPageToLoad(30000);
            $this->assertElementPresent("xpath=(//div[@class='MessageRequired' and //text()='Required field'])");

            $this->type("id=childDisciplineReferral_motivation", "test test");
            $this->click("id=childDisciplineReferral_submit");
            $this->waitForPageToLoad(30000);
            $this->assertElementPresent("xpath=(//div[@class='Message Information' and //text()='Success! The discipline referral has been updated'])[1]");

        }

        public function testDelete()
        {
            $this->login();

            $this->open("app/children");
            $this->waitForPageToLoad(30000);

            $this->click("link=Case data");
            $this->waitForPageToLoad(30000);

            $this->assertElementPresent("xpath=(//div[@id='MainBody']/div/div[2]/div[5]/div[2]/table/tbody/tr[2]/td[4]/a[3])");
            $this->click("xpath=(//div[@id='MainBody']/div/div[2]/div[5]/div[2]/table/tbody/tr[2]/td[4]/a[3])");

            $this->assertElementPresent("dialog-confirm");
            $this->click("xpath=(//button[@type='button'])[2]");
            $this->waitForPageToLoad(30000);
            $this->assertElementPresent("xpath=(//div[@class='Message Information' and //text()='The discipline referral has been deleted'])[1]");

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