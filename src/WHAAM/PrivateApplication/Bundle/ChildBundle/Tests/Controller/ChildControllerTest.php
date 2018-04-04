<?php

namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Controller;

class ChildControllerTest extends \PHPUnit_Extensions_SeleniumTestCase
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


        $this->click("link=New");
        $this->waitForPageToLoad(30000);
        $this->goBack();
        $this->waitForPageToLoad(30000);

        $this->click("link=Child network");
        $this->waitForPageToLoad(30000);
        $this->goBack();
        $this->waitForPageToLoad(30000);

        $this->click("link=Case data");
        $this->waitForPageToLoad(30000);
        $this->goBack();
        $this->waitForPageToLoad(30000);

        $this->click("link=Behaviours");
        $this->waitForPageToLoad(30000);
        $this->goBack();
        $this->waitForPageToLoad(30000);

        $this->click("link=Invitation");
        $this->waitForPageToLoad(30000);
        $this->goBack();
        $this->waitForPageToLoad(30000);

        $this->click("css=a.icon-tool.icon-tool-search");
        $this->waitForPageToLoad(30000);
        $this->goBack();
        $this->waitForPageToLoad(30000);

        $this->click("css=a.icon-tool.icon-tool-edit");
        $this->waitForPageToLoad(30000);
        $this->goBack();
        $this->waitForPageToLoad(30000);

        $this->click("css=a.icon-tool.icon-tool-delete");
        $this->assertElementPresent("dialog-confirm");
        $this->assertEquals("The child and the related data will be permanently deleted and cannot be recovered. Are you sure you want to proceed?", $this->getText("dialog-confirm"));

   }

    public function testNew()
    {
        $this->login();

        $this->open("app/children");
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

    }

    public function testView()
    {
        $this->login();
        $this->open("/app/children/Sim15test/view");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//div[@class='CellValue' and //text()='Sim15test'])");
        $this->assertElementPresent("xpath=(//div[@class='CellValue' and //text()='Caruso'])");
        $this->assertElementPresent("xpath=(//div[@class='CellValue' and //text()='Pippo'])");
        $this->assertElementPresent("xpath=(//div[@class='CellValue' and //text()='1995'])");
        $this->assertElementPresent("xpath=(//div[@class='CellValue' and //text()='Male'])");
    }

    public function testInvitation()
    {
        $email ='a.it';
        $this->login();
        $this->open("app/children/");
        $this->waitForPageToLoad(30000);
        $this->click("link=Invitation");
        $this->waitForPageToLoad(30000);
        $this->type("id=childUserInvitation_email", "a.it");
        $this->click("childUserInvitation_submit");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//*[text()='The email \"\"a.it\"\" is not a valid email'])");
    }

    public function testEdit()
    {
        $this->login();
        $this->open("app/children/Sim15test/edit");
        $this->waitForPageToLoad(30000);
        $this->type("id=child_edit_base_child_surname", "CarusoS");

        $this->click("id=add-sibling");
        $this->assertElementPresent("id=child_edit_base_child_siblings_0_name");
        $this->assertElementPresent("id=child_edit_base_child_siblings_0_nickname");
        $this->assertElementPresent("id=child_edit_base_child_siblings_0_sex");
        $this->assertElementPresent("id=child_edit_base_child_siblings_0_yearOfBirth");
        $this->click("id=remove-sibling-1");
        $this->assertElementNotPresent("id=child_edit_base_child_siblings_0_name");
        $this->assertElementNotPresent("id=child_edit_base_child_siblings_0_nickname");
        $this->assertElementNotPresent("id=child_edit_base_child_siblings_0_sex");
        $this->assertElementNotPresent("id=child_edit_base_child_siblings_0_yearOfBirth");
        $this->click("id=add-sibling");
        $this->type("id=child_edit_base_child_siblings_0_name", "Ciccio");
        $this->click("id=child_edit_submit");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//div[@class='MessageRequired' and //text()='Required field'])");
        $this->type("id=child_edit_base_child_siblings_0_nickname", "Ciccio92");
        $this->select("id=child_edit_base_child_siblings_0_sex", "index=1");
        $this->select("id=child_edit_base_child_siblings_0_yearOfBirth", "index=1");
        $this->click("id=child_edit_submit");
        $this->waitForPageToLoad(30000);
        $this->assertEquals("Success! The child profile has been modified successfully", $this->getText("xpath=(//div[@class='Message Information'])[1]"));
    }



    public function testDelete()
    {
        $this->login();

        $this->open("app/children");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//a[contains(@href, '/app/children/sim15test/delete')])");
        $this->click("xpath=(//a[contains(@href, '/app/children/sim15test/delete')])");
        $this->assertElementPresent("dialog-confirm");
        $this->click("xpath=(//button[@type='button'])[2]");
        $this->waitForPageToLoad(30000);
        $this->assertEquals("Success! The child profile has been deleted successfully", $this->getText("xpath=(//div[@class='Message Information'])[1]"));
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