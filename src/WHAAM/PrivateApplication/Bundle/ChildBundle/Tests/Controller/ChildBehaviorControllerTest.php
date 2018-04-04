<?php

namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Controller;

class ChildBehaviorControllerTest extends \PHPUnit_Extensions_SeleniumTestCase
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
        //$this->select("id=childBehavior_behavior", "index=1");
        $this->waitForSelectedLabel("id=childBehavior_behavior", "Arguing with adults, peers, parents, siblings, etc.");


       // $this->select("id=id=childBehavior_behavior", "index=4");

        $this->click("id=childBehavior_submit");
        $this->waitForPageToLoad(30000);
        $this->assertEquals("Success! The behaviour section has been created successfully", $this->getText("xpath=(//div[@class='Message Information'])[1]"));

    }

    public function testIndex()
    {
        $this->login();

        $this->open("app/children");
        $this->waitForPageToLoad(30000);

        $this->click("link=Behaviours");
        $this->waitForPageToLoad(30000);

        $this->assertEquals("test", $this->getText("css=div.Tool a"));
        $this->assertEquals("My children", $this->getText("css=div#MenuBar div.breadcrumbs div:nth-of-type(1) a"));
        $this->assertEquals("Behaviours", $this->getText("css=div#MenuBar div.breadcrumbs div:nth-of-type(3) a"));
        $this->assertEquals("List", $this->getText("css=div#MenuBar div.title h1"));

        $this->click("link=New");
        $this->waitForPageToLoad(30000);
        $this->goBack();
        $this->waitForPageToLoad(30000);

        $this->click("link=Assessments");
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
        $this->assertEquals("The child's behaviour and the related assessments will be permanently deleted and cannot be recovered. Are you sure you want to proceed?", $this->getText("dialog-confirm"));


    }


    public function testView()
    {
        $this->login();

        $this->open("app/children");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("xpath=(//a[contains(@href, '/app/child-behaviors/sim15test')])");
        $this->click("xpath=(//a[contains(@href, '/app/child-behaviors/sim15test')])");
        $this->waitForPageToLoad(30000);

        $this->click("xpath=(//td[2]/div/div/a)");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("xpath=(//div[@class='Box-table-row' and //text()='Quando la maestra spiega alla lavagna'])");
        $this->assertElementPresent("xpath=(//div[@class='Box-table-row' and //text()='ora di matematica'])");
        $this->assertElementPresent("xpath=(//div[@class='Box-table-row' and //text()='la maestra alla lavagna'])");

    }

    public function testEdit()
    {
        $this->login();

        $this->open("app/children");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("xpath=(//a[contains(@href, '/app/child-behaviors/sim15test')])");
        $this->click("xpath=(//a[contains(@href, '/app/child-behaviors/sim15test')])");
        $this->waitForPageToLoad(30000);

        $this->click("xpath=(//td[2]/div/div/a[2])");
        $this->waitForPageToLoad(30000);

        $this->type("id=childBehavior_description", "Quando la maestra spiega alla lavagna e Luigi è alla lavagna");
        $this->click("id=childBehavior_submit");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//div[@class='Message Information' and //text()='Success! The behaviour section has been modified successfully'])[1]");

    }

    public function testDelete()
    {
        $this->login();

        $this->open("app/children");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("xpath=(//a[contains(@href, '/app/child-behaviors/sim15test')])");
        $this->click("xpath=(//a[contains(@href, '/app/child-behaviors/sim15test')])");
        $this->waitForPageToLoad(30000);

        $this->assertElementPresent("xpath=(//td[2]/div/div/a[3])");
        $this->click("xpath=(//td[2]/div/div/a[3])");

        $this->assertElementPresent("dialog-confirm");
        $this->click("xpath=(//button[@type='button'])[2]");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//div[@class='Message Information' and //text()='This behaviour has been deleted successfully'])[1]");
        $this->deleteChild();
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