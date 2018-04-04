<?php

namespace WHAAM\PrivateApplication\Bundle\NotificationBundle\Controller;



class NotificationControllerTest extends \PHPUnit_Extensions_SeleniumTestCase
{
    protected $captureScreenshotOnFailure = TRUE;
    protected $screenshotPath = '/var/www/test-funzionali/whaam/screenshots';
    protected $screenshotUrl = 'http://localhost/test-funzionali/whaam/screenshots';

    protected function setUp()
    {
        $this->setBrowser("*chrome");
        $this->setBrowserUrl("https://app.whaamproject.eu");
    }

    public function testIndexAndMarkAsRead()
    {

        $this->login();
        $this->click("//a[5]");
        $this->waitForPageToLoad(30000);

        $msgNotRead = $this->getXpathCount("//tr[@class='msg-not-read']");
        $this->goBack();
        $this->waitForPageToLoad(30000);

        $this->click("link=New");
        $this->waitForPageToLoad(30000);

        $this->type("id=child_nickname", "Sim15test");
        $this->type("id=child_base_child_surname", "Caruso");
        $this->type("id=child_base_child_name", "Pippo");
        $this->select("id=child_base_child_sex", "index=1");
        $this->select("id=child_base_child_yearOfBirth", "label=1995");
        $this->click("id=child_submit");
        $this->waitForPageToLoad(30000);

        $this->select("id=childUser_role", "index=7");
        $this->click("id=childUser_submit");
        $this->waitForPageToLoad(30000);

        $this->assertEquals("Success! The role has been updated successfully", $this->getText("xpath=(//div[@class='Message Information'])[2]"));

        $this->click("//a[5]");
        $this->waitForPageToLoad(30000);

        $msgAfterChildCreation = $this->getXpathCount("//tr[@class='msg-not-read']");

        $this->assertEquals($msgNotRead+1, $msgAfterChildCreation);

        $this->clickAndWait("//td[3]/a");
        $newRows1 = $this->getXpathCount("//tr[@class='msg-not-read']");

        $noticesToReadAfterNewChild = $msgAfterChildCreation-1;

        $this->assertEquals($newRows1, $noticesToReadAfterNewChild);

        $this->click("css=img[alt='WHAAM logo']");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("xpath=(//a[contains(@href, '/app/children/sim15test/delete')])");
        $this->click("xpath=(//a[contains(@href, '/app/children/sim15test/delete')])");
        $this->assertElementPresent("dialog-confirm");
        $this->click("xpath=(//button[@type='button'])[2]");
        $this->waitForPageToLoad(30000);
        $this->assertEquals("Success! The child profile has been deleted successfully", $this->getText("xpath=(//div[@class='Message Information'])[1]"));
    }


    public function testDeleteChildBehaviorNotificationError404()
    {

        $this->login();

        $this->createChild();

        $this->open("app/children");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//a[contains(@href, '/app/child-behaviors/sim15test')])");
        $this->click("xpath=(//a[contains(@href, '/app/child-behaviors/sim15test')])");
        $this->waitForPageToLoad(30000);

        $this->click("link=New");
        $this->waitForPageToLoad(30000);

        $this->select("id=childBehavior_behaviorCategory", "index=1");
        $this->type("id=childBehavior_description", "Quando la maestra spiega alla lavagna");
        $this->type("id=childBehavior_place", "ora di matematica");
        $this->type("id=childBehavior_setting", "la maestra alla lavagna");

        $this->click("id=childBehavior_behavior");

        $this->waitForSelectedLabel("id=childBehavior_behavior", "Arguing with adults, peers, parents, siblings, etc.");

        $this->click("id=childBehavior_submit");
        $this->waitForPageToLoad(30000);

        $this->open("app/child-behaviors/sim15test");

        $this->assertElementPresent("xpath=(//td[2]/div/div/a[3])");
        $this->click("xpath=(//td[2]/div/div/a[3])");

        $this->assertElementPresent("dialog-confirm");
        $this->click("xpath=(//button[@type='button'])[2]");
        $this->waitForPageToLoad(30000);

        $this->click("//a[5]");
        $this->waitForPageToLoad(30000);

        $this->click("//tr[3]/td/a");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("xpath=(//div[@class='ErrorTxt' and //text()='Error 404'])");

        $this->open("app/children");
        $this->waitForPageToLoad(30000);

        $this->deleteChild();

    }

    public function testDeleteDiagnosisNotificationError404()
    {

        $this->login();

        $this->createChild();

        $this->open("app/children");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//a[contains(@href, '/app/case-data/sim15test')])");
        $this->click("xpath=(//a[contains(@href, '/app/case-data/sim15test')])");
        $this->waitForPageToLoad(30000);

        $this->open("/app/child-diagnosis/sim15test/new");
        $this->waitForPageToLoad(30000);

        $this->select("id=childADHDDiagnosis_diagnosisDate_day", "index=2");
        $this->select("id=childADHDDiagnosis_diagnosisDate_month", "index=5");
        $this->select("id=childADHDDiagnosis_diagnosisDate_year", "label=2014");
        $this->type("id=childADHDDiagnosis_onsetAge", "4");
        $this->select("id=childADHDDiagnosis_subtype", "index=2");

        $this->click("id=childADHDDiagnosis_submit");
        $this->waitForPageToLoad(30000);

        $this->open("/app/case-data/sim15test");

        $this->assertElementPresent("xpath=(//td[5]/a[3])");
        $this->click("xpath=(//td[5]/a[3])");

        $this->assertElementPresent("dialog-confirm");
        $this->click("xpath=(//button[@type='button'])[2]");
        $this->waitForPageToLoad(30000);

        $this->click("//a[5]");
        $this->waitForPageToLoad(30000);

        $this->click("//tr[3]/td/a");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("xpath=(//div[@class='ErrorTxt' and //text()='Error 404'])");

        $this->deleteChild();
    }

    public function testDeleteDisciplineReferralNotificationError404()
    {

        $this->login();

        $this->createChild();

        $this->open("app/children");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//a[contains(@href, '/app/case-data/sim15test')])");
        $this->click("xpath=(//a[contains(@href, '/app/case-data/sim15test')])");
        $this->waitForPageToLoad(30000);

        $this->open("/app/child-discipline-referral/sim15test/new");
        $this->waitForPageToLoad(30000);

        $this->select("id=childDisciplineReferral_date_day", "label=6");
        $this->select("id=childDisciplineReferral_date_month", "label=May");
        $this->select("id=childDisciplineReferral_date_year", "label=2014");
        $this->select("id=childDisciplineReferral_disciplineReferralType", "index=6");
        $this->type("id=childDisciplineReferral_motivation", "test");

        $this->click("id=childDisciplineReferral_submit");
        $this->waitForPageToLoad(60000);

        $this->open("/app/case-data/sim15test");

        $this->assertElementPresent("xpath=(//td[4]/a[3])");
        $this->click("xpath=(//td[4]/a[3])");

        $this->assertElementPresent("dialog-confirm");
        $this->click("xpath=(//button[@type='button'])[2]");
        $this->waitForPageToLoad(30000);

        $this->click("//a[5]");
        $this->waitForPageToLoad(30000);

        $this->click("//tr[3]/td/a");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("xpath=(//div[@class='ErrorTxt' and //text()='Error 404'])");

        $this->deleteChild();
    }

    public function testDeleteGeneralEventNotificationError404()
    {

        $this->login();

        $this->createChild();

        $this->open("app/children");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//a[contains(@href, '/app/case-data/sim15test')])");
        $this->click("xpath=(//a[contains(@href, '/app/case-data/sim15test')])");
        $this->waitForPageToLoad(30000);

        $this->open("/app/child-event/sim15test/new");
        $this->waitForPageToLoad(30000);


        $this->select("id=childGeneralEvent_date_day", "label=6");
        $this->select("id=childGeneralEvent_date_month", "label=May");
        $this->select("id=childGeneralEvent_date_year", "label=2014");

        $this->type("id=childGeneralEvent_description", "test");

        $this->click("id=childGeneralEvent_submit");
        $this->waitForPageToLoad(30000);

        $this->open("/app/case-data/sim15test");

        $this->assertElementPresent("xpath=(//td[3]/a[3])");
        $this->click("xpath=(//td[3]/a[3])");

        $this->assertElementPresent("dialog-confirm");
        $this->click("xpath=(//button[@type='button'])[2]");
        $this->waitForPageToLoad(30000);

        $this->click("//a[5]");
        $this->waitForPageToLoad(30000);

        $this->click("//tr[3]/td/a");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("xpath=(//div[@class='ErrorTxt' and //text()='Error 404'])");

        $this->deleteChild();
    }

    public function testDeleteMedicationNotificationError404()
    {

        $this->login();

        $this->createChild();

        $this->open("app/children");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//a[contains(@href, '/app/case-data/sim15test')])");
        $this->click("xpath=(//a[contains(@href, '/app/case-data/sim15test')])");
        $this->waitForPageToLoad(30000);

        $this->open("/app/child-medication/sim15test/new");
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

        $this->open("/app/case-data/sim15test");

        $this->assertElementPresent("xpath=(//td[6]/a[3])");
        $this->click("xpath=(//td[6]/a[3])");

        $this->assertElementPresent("dialog-confirm");
        $this->click("xpath=(//button[@type='button'])[2]");
        $this->waitForPageToLoad(30000);

        $this->click("//a[5]");
        $this->waitForPageToLoad(30000);

        $this->click("//tr[3]/td/a");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("xpath=(//div[@class='ErrorTxt' and //text()='Error 404'])");

        $this->deleteChild();
    }

    public function testDeleteSchoolInfoNotificationError404()
    {

        $this->login();

        $this->createChild();

        $this->open("app/children");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//a[contains(@href, '/app/case-data/sim15test')])");
        $this->click("xpath=(//a[contains(@href, '/app/case-data/sim15test')])");
        $this->waitForPageToLoad(30000);

        $this->open("/app/child-school-information/sim15test/new");
        $this->waitForPageToLoad(30000);

        $this->type("id=childSchoolInformation_year", "2014-15");
        $this->type("id=childSchoolInformation_schoolName", "Michelangelo");
        $this->type("id=childSchoolInformation_grade", "4");

        $this->click("id=childSchoolInformation_submit");
        $this->waitForPageToLoad(30000);

        $this->open("/app/case-data/sim15test");

        $this->assertElementPresent("xpath=(//td[4]/a[3])");
        $this->click("xpath=(//td[4]/a[3])");

        $this->assertElementPresent("dialog-confirm");
        $this->click("xpath=(//button[@type='button'])[2]");
        $this->waitForPageToLoad(30000);

        $this->click("//a[5]");
        $this->waitForPageToLoad(30000);

        $this->click("//tr[3]/td/a");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("xpath=(//div[@class='ErrorTxt' and //text()='Error 404'])");

        $this->deleteChild();
    }



    public function testDeleteChildNotificationError404()
    {

        $this->login();

        $this->createChild();

        $this->deleteChild();

        $this->click("//a[5]");
        $this->waitForPageToLoad(30000);

        $this->click("//tr[3]/td/a");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("xpath=(//div[@class='ErrorTxt' and //text()='Error 404'])");


    }


    private function createChild()
    {
        $this->open("/app/children/new");
        $this->waitForPageToLoad(30000);

        $this->type("id=child_nickname", "Sim15test");
        $this->type("id=child_base_child_surname", "Caruso");
        $this->type("id=child_base_child_name", "Pippo");
        $this->select("id=child_base_child_sex", "index=1");
        $this->select("id=child_base_child_yearOfBirth", "label=1995");
        $this->click("id=child_submit");
        $this->waitForPageToLoad(30000);
        $this->select("id=childUser_role", "index=7");
        $this->click("id=childUser_submit");
        $this->waitForPageToLoad(30000);

    }

    private function deleteChild()
    {
        $this->open("app/children");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//a[contains(@href, '/app/children/sim15test/delete')])");
        $this->click("xpath=(//a[contains(@href, '/app/children/sim15test/delete')])");
        $this->assertElementPresent("dialog-confirm");
        $this->click("xpath=(//button[@type='button'])[2]");
        $this->waitForPageToLoad(30000);

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