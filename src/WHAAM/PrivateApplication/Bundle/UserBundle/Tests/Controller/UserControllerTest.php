<?php

namespace WHAAM\PrivateApplication\Bundle\UserBundle\Controller;

class UserControllerTest extends \PHPUnit_Extensions_SeleniumTestCase
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

        $this->click("link=Sign Up");
        $this->waitForPageToLoad(30000);
        $this->select("id=user_base_user_sex", "index=0");
        $this->select("id=user_base_user_dateOfBirth_day", "index=9");
        $this->select("id=user_base_user_dateOfBirth_month", "index=11");
        $this->select("id=user_base_user_dateOfBirth_year", "index=18");
        $this->select("id=user_base_user_nation", "index=6");
        $this->assertElementPresent("id=user_base_user_otherNation");
        $this->type("id=user_base_user_otherNation", "France");
        $this->click("id=user_base_user_isHealthProfessional");
        $this->click("id=user_base_user_healthProfessionalClientsAgeRange_4");
        $this->click("id=user_base_user_healthProfessionalTreatmentModalities_2");
        $this->click("id=user_base_user_healthProfessionalTreatmentModalities_3");
        $this->click("id=user_base_user_healthProfessionalSpecialties_5");
        $this->click("id=user_base_user_healthProfessionalTreatmentApproaches_12");
        $this->type("id=user_username", "samantha");
        $this->type("id=user_password_first", "samanthafox");
        $this->type("id=user_password_second", "samanthafox");
        $this->type("id=user_email", "heidenreich.zoe@haley.com");
        $this->click("id=user_terms_acceptance");
        $this->click("id=user_submit");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//div[@class='MessageRequired' and //text()='Required field'])");

       }

    public function testProfileIndex()
    {
        $this->login();

        $this->type("name=_username", "test");
        $this->type("name=_password", "test");
        $this->click("//button[@type='submit']");
        $this->waitForPageToLoad(30000);

        $this->click("xpath=(//a[contains(@href, '/app/users/account')])[2]");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("css=a.account-icon.view-profile");
        $this->assertElementPresent("css=a.account-icon.edit-password");
        $this->assertElementPresent("css=a.account-icon.edit-profile");

    }

    public function testViewProfile()
    {
        $this->login();

        $this->type("name=_username", "test");
        $this->type("name=_password", "test");
        $this->click("//button[@type='submit']");
        $this->waitForPageToLoad(30000);

        $this->click("xpath=(//a[contains(@href, '/app/users/account')])[2]");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("css=a.account-icon.view-profile");
        $this->click("css=a.account-icon.view-profile");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("//div[@class='CellName' and //text()='Surname:']");
        $this->assertElementPresent("//div[@class='CellName' and //text()='First name:']");
        $this->assertElementPresent("//div[@class='CellName' and //text()='Username:']");
        $this->assertElementPresent("//div[@class='CellName' and //text()='Email:']");
        $this->assertElementPresent("//div[@class='CellName' and //text()='Sex:']");
        $this->assertElementPresent("//div[@class='CellName' and //text()='Date of birth:']");
        $this->assertElementPresent("//div[@class='CellName' and //text()='Nationality:']");
    }

    public function testEditWrongPwd()
    {
        $this->login();

        $this->type("name=_username", "test");
        $this->type("name=_password", "test");
        $this->click("//button[@type='submit']");
        $this->waitForPageToLoad(30000);

        $this->click("xpath=(//a[contains(@href, '/app/users/account')])[2]");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("css=a.account-icon.edit-password");
        $this->click("css=a.account-icon.edit-password");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("id=user_edit_password_oldPassword");
        $this->assertElementPresent("id=user_edit_password_password_first");
        $this->assertElementPresent("id=user_edit_password_password_second");
        $this->assertElementPresent("id=user_edit_password_save");
        $this->type("id=user_edit_password_oldPassword", "Pippo");
        $this->type("id=user_edit_password_password_first", "Pippo");
        $this->type("id=user_edit_password_password_second", "Pippo");
        $this->click("id=user_edit_password_save");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//div[@class='MessageRequired' and //text()='This value should be the user current password'])");
    }

    public function EditUser()
    {
        $this->login();

        $this->type("name=_username", "test");
        $this->type("name=_password", "test");
        $this->click("//button[@type='submit']");
        $this->waitForPageToLoad(30000);

        $this->click("xpath=(//a[contains(@href, '/app/users/account')])[2]");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("css=a.account-icon.edit-profile");
        $this->click("css=a.account-icon.edit-profile");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//label[@for='user_edit_base_user_surname'])");
        $this->assertElementPresent("xpath=(//label[@for='user_edit_base_user_name'])");
        $this->assertElementPresent("xpath=(//label[@for='user_edit_base_user_sex'])");
        $this->assertSomethingNotSelected("id=user_edit_base_user_isHealthProfessional");
        $this->assertElementNotPresent("id=user_edit_base_user_healthProfessionalClientsAgeRange_1");


    }

    public function testEditWrongRepeatPwd()
    {
        $this->login();

        $this->type("name=_username", "test");
        $this->type("name=_password", "test");
        $this->click("//button[@type='submit']");
        $this->waitForPageToLoad(30000);

        $this->click("xpath=(//a[contains(@href, '/app/users/account')])[2]");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("css=a.account-icon.edit-password");
        $this->click("css=a.account-icon.edit-password");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("id=user_edit_password_oldPassword");
        $this->assertElementPresent("id=user_edit_password_password_first");
        $this->assertElementPresent("id=user_edit_password_password_second");
        $this->assertElementPresent("id=user_edit_password_save");
        $this->type("id=user_edit_password_oldPassword", "test");
        $this->type("id=user_edit_password_password_first", "Pippo");
        $this->type("id=user_edit_password_password_second", "Test");
        $this->click("id=user_edit_password_save");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//div[@class='MessageRequired' and //text()='Password and repeat password fields are not the same'])");
    }

   private function login()
   {
       $this->open("app/login");
       $this->waitForPageToLoad(30000);
   }
}