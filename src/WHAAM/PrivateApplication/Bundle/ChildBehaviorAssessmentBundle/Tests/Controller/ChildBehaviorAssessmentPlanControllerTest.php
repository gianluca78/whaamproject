<?php

namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAsssessmentBundle\Controller;

use Symfony\Component\Validator\Constraints\Date;
use DateTime;

class ChildBehaviorAssessmentPlanControllerTest extends \PHPUnit_Extensions_SeleniumTestCase
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

        $this->click("//tr[2]/td[4]/a");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("link=Assessments");

        $this->click("link=Assessments");
        $this->waitForPageToLoad(30000);
        $rows = $this->getXpathCount("//table[@class='tcontent']/tbody/tr");
        $this->assertElementPresent("//tr[$rows]/td[8]/a");
    }

    public function testNewEditBaselineStartDateEqualEndDate()
    {
        $this->login();
        $this->accessBaseline();
        //startDate=EndDate
        $startDate = $this->getSelectedIndex("id=child_behavior_assessment_baseline_base_phase_startDate_day");
        $startMonth = $this->getSelectedIndex("id=child_behavior_assessment_baseline_base_phase_startDate_month");
        $startYear = $this->getSelectedIndex("id=child_behavior_assessment_baseline_base_phase_startDate_year");

        $this->select("child_behavior_assessment_baseline_base_phase_endDate_day", "index=" . $startDate);
        $this->select("child_behavior_assessment_baseline_base_phase_endDate_month", "index=" . $startMonth);
        $this->select("child_behavior_assessment_baseline_base_phase_endDate_year", "index=" . $startYear);

        $this->select("id=child_behavior_assessment_baseline_observer", "index=1");
        $this->type("id=child_behavior_assessment_baseline_minimumNumberOfObservations", "5");
        $this->type("id=child_behavior_assessment_baseline_observationLength", "20");
        $this->select("id=child_behavior_assessment_baseline_observationType", "index=1");
        if (!($this->isChecked("//li/input"))){
            $this->click("//li/input");
        }
        $this->click("id=child_behavior_assessment_baseline_submit");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//div[@class='MessageRequired' and //text()='The end date must be after the start date'])");
    }

    public function testNewBaselineStartDateGreaterEndDate()
    {
        $this->login();
        $this->accessBaseline();
        //startDate > EndDate
        $startDate = $this->getSelectedIndex("id=child_behavior_assessment_baseline_base_phase_startDate_day");
        $startMonth = $this->getSelectedIndex("id=child_behavior_assessment_baseline_base_phase_startDate_month");
        $startYear = $this->getSelectedIndex("id=child_behavior_assessment_baseline_base_phase_startDate_year");
        $endDate = $startDate-1;
        $this->select("child_behavior_assessment_baseline_base_phase_endDate_day", "index=" . $endDate);
        $this->select("child_behavior_assessment_baseline_base_phase_endDate_month", "index=" . $startMonth);
        $this->select("child_behavior_assessment_baseline_base_phase_endDate_year", "index=" . $startYear);

        $this->select("id=child_behavior_assessment_baseline_observer", "index=1");
        $this->type("id=child_behavior_assessment_baseline_minimumNumberOfObservations", "5");
        $this->type("id=child_behavior_assessment_baseline_observationLength", "20");
        $this->select("id=child_behavior_assessment_baseline_observationType", "index=1");
        if (!($this->isChecked("//li/input"))){
            $this->click("//li/input");
        }
        $this->click("id=child_behavior_assessment_baseline_submit");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//div[@class='MessageRequired' and //text()='The end date must be after the start date'])");

    }

    public function testNewEditBaselineStartLowerEnd()
    {
        $this->login();
        $this->accessBaseline();
        //startDate < EndDate
        $startDate = $this->getSelectedIndex("id=child_behavior_assessment_baseline_base_phase_startDate_day");
        $startMonth = $this->getSelectedIndex("id=child_behavior_assessment_baseline_base_phase_startDate_month");
        $startYear = $this->getSelectedIndex("id=child_behavior_assessment_baseline_base_phase_startDate_year");
        $endDate = $startDate+1;
        $this->select("child_behavior_assessment_baseline_base_phase_endDate_day", "index=" . $endDate);
        $this->select("child_behavior_assessment_baseline_base_phase_endDate_month", "index=" . $startMonth);
        $this->select("child_behavior_assessment_baseline_base_phase_endDate_year", "index=" . $startYear);

        $this->select("id=child_behavior_assessment_baseline_observer", "index=1");
        $this->type("id=child_behavior_assessment_baseline_minimumNumberOfObservations", "5");
        $this->type("id=child_behavior_assessment_baseline_observationLength", "20");
        $this->select("id=child_behavior_assessment_baseline_observationType", "index=1");
        if (!($this->isChecked("//li/input"))){
            echo("True");
            $this->click("//li/input");
        }

        $this->click("id=child_behavior_assessment_baseline_submit");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//div[@class='Message Information'])[1]");

    }

    public function testEditBaselineModifyStartDateEndDate()
    {

        $this->login();
        $this->accessBaseline();
        $this->assertElementPresent("id=child_behavior_assessment_baseline_base_phase_startDate_day");
        $this->assertElementPresent("id=child_behavior_assessment_baseline_base_phase_startDate_month");
        $this->assertElementPresent("id=child_behavior_assessment_baseline_base_phase_startDate_year");
        // case startDate > endDate
        $startDate = $this->getSelectedIndex("id=child_behavior_assessment_baseline_base_phase_startDate_day");
        $endDate = $startDate -1;
        $this->select("child_behavior_assessment_baseline_base_phase_endDate_day", "index=" . $endDate);
        $this->click("id=child_behavior_assessment_baseline_submit");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//div[@class='MessageRequired' and //text()='The end date must be after the start date'])");
        // case endDate extension
        $endDate = $this->getSelectedIndex("id=child_behavior_assessment_baseline_base_phase_endDate_day");
        $endDate = $endDate +2;
        $this->select("child_behavior_assessment_baseline_base_phase_endDate_day", "index=" . $endDate);
        $this->click("id=child_behavior_assessment_baseline_submit");
        $this->waitForPageToLoad(30000);
        $this->assertEquals("Success! The baseline has been modified successfully", $this->getText("xpath=(//div[@class='Message Information'])[1]"));

    }




    public function testNewFunction()
    {
        $this->login();
        $this->accessFunction();
        $this->assertElementPresent("id=childBehaviorFunction_behaviorWhen");
        $this->assertElementPresent("id=childBehaviorFunction_behaviorFunction");
        $this->assertElementPresent("id=childBehaviorFunction_note");
        $this->type("id=childBehaviorFunction_behaviorWhen", "When the mom have a toy in her hand the child hits mom");
        $this->select("childBehaviorFunction_behaviorFunction", "index=3");
        $this->type("id=childBehaviorFunction_note", "No note");

        $this->click("id=childBehaviorFunction_submit");
        $this->waitForPageToLoad(30000);
        //$this->assertElementPresent("xpath=(//div[@class='MessageRequired' and //text()='The end date must be after the start date'])");
        $this->assertEquals("Success! The function has been created successfully", $this->getText("xpath=(//div[@class='Message Information'])[1]"));

    }

    public function testNewEditInterventionStartDateEqualEndDate()
    {
        $this->login();
        $this->accessIntervention();
        $this->assertElementPresent("id=child_behavior_assessment_intervention_base_phase_startDate_day");
        $this->assertElementPresent("id=child_behavior_assessment_intervention_base_phase_startDate_month");
        $this->assertElementPresent("id=child_behavior_assessment_intervention_base_phase_startDate_year");

        $startDate = $this->getSelectedIndex("id=child_behavior_assessment_intervention_base_phase_startDate_day");
        $startMonth = $this->getSelectedIndex("id=child_behavior_assessment_intervention_base_phase_startDate_month");
        $startYear = $this->getSelectedIndex("id=child_behavior_assessment_intervention_base_phase_startDate_year");

        $this->select("child_behavior_assessment_intervention_base_phase_endDate_day", "index=" . $startDate);
        $this->select("child_behavior_assessment_intervention_base_phase_endDate_month", "index=" . $startMonth);
        $this->select("child_behavior_assessment_intervention_base_phase_endDate_year", "index=" . $startYear);

        $this->click("id=add-strategy");
        $this->type("id=child_behavior_assessment_intervention_strategies_0_name", "ciccio");
        $this->type("id=child_behavior_assessment_intervention_strategies_0_description", "description");
        if (!($this->isChecked("//li/input"))){
            $this->click("//li/input");
        }

        $this->click("id=child_behavior_assessment_intervention_submit");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//div[@class='MessageRequired' and //text()='The end date must be after the start date'])");
        $this->assertElementPresent("xpath=(//div[@class='MessageRequired' and //text()='An assessment phase is overlapped with the dates interval'])");
    }

    public function testNewInterventionStartDateGreaterEndDate()
    {
        $this->login();
        $this->accessIntervention();
        //startDate > EndDate
        $this->assertElementPresent("id=child_behavior_assessment_intervention_base_phase_startDate_day");
        $this->assertElementPresent("id=child_behavior_assessment_intervention_base_phase_startDate_month");
        $this->assertElementPresent("id=child_behavior_assessment_intervention_base_phase_startDate_year");

        $startDate = $this->getSelectedIndex("id=child_behavior_assessment_intervention_base_phase_startDate_day");
        $startMonth = $this->getSelectedIndex("id=child_behavior_assessment_intervention_base_phase_startDate_month");
        $startYear = $this->getSelectedIndex("id=child_behavior_assessment_intervention_base_phase_startDate_year");

        $endDate = $startDate-1;
        $this->select("child_behavior_assessment_intervention_base_phase_endDate_day", "index=" . $endDate);
        $this->select("child_behavior_assessment_intervention_base_phase_endDate_month", "index=" . $startMonth);
        $this->select("child_behavior_assessment_intervention_base_phase_endDate_year", "index=" . $startYear);

        $this->click("id=add-strategy");
        $this->type("id=child_behavior_assessment_intervention_strategies_0_name", "ciccio");
        $this->type("id=child_behavior_assessment_intervention_strategies_0_description", "description");
        if (!($this->isChecked("//li/input"))){
            $this->click("//li/input");
        }

        $this->click("id=child_behavior_assessment_intervention_submit");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//div[@class='MessageRequired' and //text()='The end date must be after the start date'])");

    }

    public function testNewInterventionAfterBaselineDate()
    {
        $this->login();
        $this->accessIntervention();
        //startDate > EndDate
        $this->assertElementPresent("id=child_behavior_assessment_intervention_base_phase_startDate_day");
        $this->assertElementPresent("id=child_behavior_assessment_intervention_base_phase_startDate_month");
        $this->assertElementPresent("id=child_behavior_assessment_intervention_base_phase_startDate_year");

        $startDate = $this->getSelectedIndex("id=child_behavior_assessment_intervention_base_phase_startDate_day");

        $startDate = $startDate+2;

        $this->select("child_behavior_assessment_intervention_base_phase_startDate_day", "index=" . $startDate);

        $startMonth = $this->getSelectedIndex("id=child_behavior_assessment_intervention_base_phase_startDate_month");
        $startYear = $this->getSelectedIndex("id=child_behavior_assessment_intervention_base_phase_startDate_year");

        $endDate = $startDate+1;

        $this->select("child_behavior_assessment_intervention_base_phase_endDate_day", "index=" . $endDate);
        $this->select("child_behavior_assessment_intervention_base_phase_endDate_month", "index=" . $startMonth);
        $this->select("child_behavior_assessment_intervention_base_phase_endDate_year", "index=" . $startYear);

        $this->click("id=add-strategy");
        $this->type("id=child_behavior_assessment_intervention_strategies_0_name", "ciccio");
        $this->type("id=child_behavior_assessment_intervention_strategies_0_description", "description");
        if (!($this->isChecked("//li/input"))){
            $this->click("//li/input");
        }

        $this->click("id=child_behavior_assessment_intervention_submit");
        $this->waitForPageToLoad(30000);
        $this->assertEquals("Success! The intervention has been created successfully", $this->getText("xpath=(//div[@class='Message Information'])[1]"));

    }

    public function testDeleteIntervention()
    {

        $this->login();
        $this->open("app/children");
        $this->waitForPageToLoad(30000);

        $this->assertEquals("test", $this->getText("css=div.Tool a"));
        $this->assertEquals("My children", $this->getText("css=div#MenuBar div.breadcrumbs div a"));
        $this->assertEquals("List", $this->getText("css=div#MenuBar div.title h1"));

        $this->click("//tr[2]/td[4]/a");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("link=Assessments");

        $this->click("link=Assessments");
        $this->waitForPageToLoad(30000);

        $rows = $this->getXpathCount("//table[@class='tcontent']/tbody/tr");
        $this->click("//tr[$rows]/td[8]/a");
        $this->waitForPageToLoad(30000);
        $this->click("//tr[3]/td[2]/div/div[2]/a[4]");
        $this->assertElementPresent("intervention-dialog-confirm");
        $this->click("xpath=(//button[@type='button'])[2]");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("xpath=(//div[@class='Message Information' and //text()='The intervention has been deleted successfully'])[1]");

    }

    public function testDeleteFunction()
    {

        $this->login();
        $this->open("app/children");
        $this->waitForPageToLoad(30000);

        $this->assertEquals("test", $this->getText("css=div.Tool a"));
        $this->assertEquals("My children", $this->getText("css=div#MenuBar div.breadcrumbs div a"));
        $this->assertEquals("List", $this->getText("css=div#MenuBar div.title h1"));

        $this->click("//tr[2]/td[4]/a");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("link=Assessments");

        $this->click("link=Assessments");
        $this->waitForPageToLoad(30000);

        $rows = $this->getXpathCount("//table[@class='tcontent']/tbody/tr");
        $this->click("//tr[$rows]/td[8]/a");
        $this->waitForPageToLoad(30000);
        $this->click("//tr[2]/td[2]/div/div[2]/a[3]");
        $this->assertElementPresent("function-dialog-confirm");
        $this->click("xpath=(//button[@type='button'])[2]");
        $this->waitForPageToLoad(30000);
        $this->assertEquals("The function has been deleted successfully", $this->getText("xpath=(//div[@class='Message Information'])[1]"));

    }



        public function testDeleteBaseline()
        {

            $this->login();
            $this->open("app/children");
            $this->waitForPageToLoad(30000);

            $this->assertEquals("test", $this->getText("css=div.Tool a"));
            $this->assertEquals("My children", $this->getText("css=div#MenuBar div.breadcrumbs div a"));
            $this->assertEquals("List", $this->getText("css=div#MenuBar div.title h1"));

            $this->click("//tr[2]/td[4]/a");
            $this->waitForPageToLoad(30000);
            $this->assertElementPresent("link=Assessments");

            $this->click("link=Assessments");
            $this->waitForPageToLoad(30000);

            $rows = $this->getXpathCount("//table[@class='tcontent']/tbody/tr");
            $this->click("//tr[$rows]/td[8]/a");
            $this->waitForPageToLoad(30000);
            $title = $this->getAttribute("//a[contains(@title, 'Baseline')]@title");
            $this->assertEquals("New Baseline disable", $this->getAttribute("//a[contains(@title, 'Baseline')]@title"));
            $this->click("//td[2]/div/div[2]/a[4]");
            $this->assertElementPresent("baseline-dialog-confirm");
            $this->click("xpath=(//button[@type='button'])[2]");
            $this->waitForPageToLoad(30000);
           $this->assertElementPresent("xpath=(//div[@class='Message Information' and //text()='The baseline has been deleted successfully'])[1]");

        }




    private function accessFunction()
    {
        $this->open("app/children");
        $this->waitForPageToLoad(30000);

        $this->assertEquals("test", $this->getText("css=div.Tool a"));
        $this->assertEquals("My children", $this->getText("css=div#MenuBar div.breadcrumbs div a"));
        $this->assertEquals("List", $this->getText("css=div#MenuBar div.title h1"));

        $this->click("//tr[2]/td[4]/a");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("link=Assessments");

        $this->click("link=Assessments");
        $this->waitForPageToLoad(30000);

        $rows = $this->getXpathCount("//table[@class='tcontent']/tbody/tr");
        $this->click("//tr[$rows]/td[8]/a");
        $this->waitForPageToLoad(30000);
        $title = $this->getAttribute("//a[contains(@title, 'Function')]@title");
        if ((string) $title == "New Function ") {
            $this->click("link=New Function");
            $this->waitForPageToLoad(30000);

        }
    }
        private function accessIntervention()
    {
        $this->open("app/children");
        $this->waitForPageToLoad(30000);

        $this->assertEquals("test", $this->getText("css=div.Tool a"));
        $this->assertEquals("My children", $this->getText("css=div#MenuBar div.breadcrumbs div a"));
        $this->assertEquals("List", $this->getText("css=div#MenuBar div.title h1"));

        $this->click("//tr[2]/td[4]/a");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("link=Assessments");

        $this->click("link=Assessments");
        $this->waitForPageToLoad(30000);

        $rows = $this->getXpathCount("//table[@class='tcontent']/tbody/tr");
        $this->click("//tr[$rows]/td[8]/a");
        $this->waitForPageToLoad(30000);
        $title = $this->getAttribute("//a[contains(@title, 'New Intervention')]@title");
        if ((string) $title == "New Intervention") {
            $this->click("link=New Intervention");
            $this->waitForPageToLoad(30000);

        }

        if ((string) $title == "New intervention disable") {
            $this->click("//td[2]/div/div[2]/a[3]");
            $this->waitForPageToLoad(30000);

        }
    }



        private function accessBaseline()
    {
        $this->open("app/children");
        $this->waitForPageToLoad(30000);

        $this->assertEquals("test", $this->getText("css=div.Tool a"));
        $this->assertEquals("My children", $this->getText("css=div#MenuBar div.breadcrumbs div a"));
        $this->assertEquals("List", $this->getText("css=div#MenuBar div.title h1"));

        $this->click("//tr[2]/td[4]/a");
        $this->waitForPageToLoad(30000);
        $this->assertElementPresent("link=Assessments");

        $this->click("link=Assessments");
        $this->waitForPageToLoad(30000);

        $rows = $this->getXpathCount("//table[@class='tcontent']/tbody/tr");
        $this->click("//tr[$rows]/td[8]/a");
        $this->waitForPageToLoad(30000);
        $title = $this->getAttribute("//a[contains(@title, 'Baseline')]@title");

        if ((string) $title == "New Baseline") {
            $this->click("link=New Baseline");
            $this->waitForPageToLoad(30000);

        }

        if ((string) $title == "New Baseline disable") {
            $this->click("//td[2]/div/div[2]/a[3]");
            $this->waitForPageToLoad(30000);

        }


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