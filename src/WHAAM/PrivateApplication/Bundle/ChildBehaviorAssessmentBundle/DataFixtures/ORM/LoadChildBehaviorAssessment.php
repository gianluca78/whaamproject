<?php
/*
 * This file is part of the WHAAM project funded with support from the European Commission.
 *
 * Reference project number: 531244-LLP-2012-IT-KA3MP
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @author Giuseppe Chiazzese
 *
 */
namespace WHAAM\PrivateApplication\Bundle\ChildBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessment;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentABC;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentBaseline;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentObservationData;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentIntervention;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentObservationSession;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorFunction;

class LoadChildBehaviourAssessment extends AbstractFixture implements OrderedFixtureInterface
{
    private $faker;
    private $observationTimeLength = array(30, 40, 45);
    private $startStopOccurrencesNumber = array(4, 8, 12);

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->faker = Factory::create();
        for($i=1; $i<=10; $i++) {
            $childBehaviourAssessment = new ChildBehaviorAssessment();
            $childBehaviourAssessment->setChildBehavior($this->getReference('child-behaviour-' . $i));

            $childUser = $childBehaviourAssessment->getChildBehavior()->getChild()->getChildUsers()->get(0);
            $user = $childBehaviourAssessment->getChildBehavior()->getChildBehaviorCreatorUser();

            $childBehaviourAssessmentBaseline = $this->prepareAssessmentPhases(
                $childBehaviourAssessment,
                $childUser,
                $user
            );

            $childBehaviourAssessmentABC = new ChildBehaviorAssessmentABC();
            $childBehaviourAssessmentABC->setABCDate($this->faker->dateTimeBetween('-5 days', 'now'));
            $childBehaviourAssessmentABC->setAntecedentTrigger($this->faker->sentence());
            $childBehaviourAssessmentABC->setAntecedentWhat($this->faker->sentence());
            $childBehaviourAssessmentABC->setAntecedentWhere($this->faker->sentence());
            $childBehaviourAssessmentABC->setAntecedentWho($this->faker->name);
            $childBehaviourAssessmentABC->setConsequenceChildReaction($this->faker->sentence());
            $childBehaviourAssessmentABC->setConsequenceOthersReaction($this->faker->sentence());
            $childBehaviourAssessmentABC->setChildBehaviorAssessmentBaseline($childBehaviourAssessmentBaseline);
            $childBehaviourAssessmentABC->setUser($user);

            $childBehaviourAssessmentBaseline->addABC($childBehaviourAssessmentABC);

            $childBehaviourAssessment->addBaseline($childBehaviourAssessmentBaseline);

            $manager->persist($childBehaviourAssessment);
            $manager->flush();
        }
    }

    public function getOrder()
    {
        return 4;
    }
    
    private function prepareAssessmentPhases($childBehaviourAssessment, $childUser, $user, $numberOfDayIncrement = 1, $randomIntervention=false)
    {
        $baselineDaysEnd = array($numberOfDayIncrement + 2, $numberOfDayIncrement + 10);
        $interventionDaysStart = array($numberOfDayIncrement + 11, $numberOfDayIncrement + 15);
        $interventionDaysEnd = array($numberOfDayIncrement + 16, $numberOfDayIncrement + 20);

        $childBehaviourAssessmentBaseline = new ChildBehaviorAssessmentBaseline();
        $childBehaviourAssessmentBaseline->setStartDate($this->faker->dateTimeBetween('now', '+' . $numberOfDayIncrement . ' days'));
        $childBehaviourAssessmentBaseline->setEndDate($this->faker->dateTimeBetween('+' .$baselineDaysEnd[0] . ' days', '+' . $baselineDaysEnd[1] . 'days'));
        $childBehaviourAssessmentBaseline->setObservationLength($this->observationTimeLength[array_rand($this->observationTimeLength)]);
        $childBehaviourAssessmentBaseline->setMinimumNumberOfObservations(8);
        $childBehaviourAssessmentBaseline->setIsLocked(1);

        $observationTypes = array('duration', 'frequency');

        $childBehaviourAssessmentBaseline->setObservationType($observationTypes[array_rand($observationTypes)]);
        $childBehaviourAssessmentBaseline->setObserver($childUser->getUser());
        $childBehaviourAssessmentBaseline->addABCUser($childUser->getUser());
        $childBehaviourAssessmentBaseline->setBaselineCreatorUser($user);
        $childBehaviourAssessmentBaseline->setChildBehaviorAssessment($childBehaviourAssessment);

        $childBehaviorFunction = new ChildBehaviorFunction();
        $childBehaviorFunction->setBehaviorWhen($this->faker->sentence());
        $childBehaviorFunction->setBehaviorFunction($this->getReference('behavior-function-' . rand(0, 3)));
        $childBehaviorFunction->setNote($this->faker->sentence());
        $childBehaviorFunction->setChildBehaviorAssessmentBaseline($childBehaviourAssessmentBaseline);
        $childBehaviorFunction->setIsLocked(1);
        $childBehaviourAssessmentBaseline->setChildBehaviorFunction($childBehaviorFunction);

        $childBehaviorAssessmentIntervention = new ChildBehaviorAssessmentIntervention();
        $childBehaviorAssessmentIntervention->setChildBehaviorAssessmentBaseline($childBehaviourAssessmentBaseline);
        $childBehaviorAssessmentIntervention->setStartDate($this->faker->dateTimeBetween('+' . $interventionDaysStart[0] . ' days', '+' . $interventionDaysStart[1] .' days'));
        $childBehaviorAssessmentIntervention->setEndDate($this->faker->dateTimeBetween('+' . $interventionDaysEnd[0] . ' days', '+' . $interventionDaysEnd[1] . ' days'));
        $childBehaviorAssessmentIntervention->setInterventionCreatorUser($user);
        $childBehaviorAssessmentIntervention->setIsLocked(1);

        $timestampBaselineSessionStartDate = $childBehaviourAssessmentBaseline->getStartDate()->format('U');
        $timestampInterventionSessionStartDate = $childBehaviorAssessmentIntervention->getStartDate()->format('U');

        for($k=0; $k<=7; $k++) {
            $childBehaviourAssessmentObservationBaselineSession = new ChildBehaviorAssessmentObservationSession();
            $childBehaviourAssessmentObservationBaselineSession->setSessionStartTimestamp(\DateTime::createFromFormat('U', $timestampBaselineSessionStartDate));
            $childBehaviourAssessmentObservationBaselineSession->setNote($this->faker->realText());
            $childBehaviourAssessmentObservationBaselineSession->setBaseline($childBehaviourAssessmentBaseline);

            $childBehaviourAssessmentBaseline->addObservationSession($childBehaviourAssessmentObservationBaselineSession);

            $childBehaviourAssessmentObservationInterventionSession = new ChildBehaviorAssessmentObservationSession();
            $childBehaviourAssessmentObservationInterventionSession->setSessionStartTimestamp(\DateTime::createFromFormat('U', $timestampInterventionSessionStartDate));
            $childBehaviourAssessmentObservationInterventionSession->setNote($this->faker->realText());
            $childBehaviourAssessmentObservationInterventionSession->setIntervention($childBehaviorAssessmentIntervention);

            $childBehaviorAssessmentIntervention->addObservationSession($childBehaviourAssessmentObservationInterventionSession);

            $observationBaselineSessionStartTimeStamp = $timestampBaselineSessionStartDate;
            $observationInterventionSessionStartTimeStamp = $timestampInterventionSessionStartDate;

            $observationsNumbers = array(2, 6, 8, 10, 14, 16, 20);

            $observationsNumber = array_rand($observationsNumbers);

            for($j=1; $j<=$observationsNumbers[$observationsNumber]; $j++){
                echo '[CHILD BEHAVIOUR ASSESSMENT][' . $j . '][CHILD BEHAVIOUR ASSESSMENT BASELINE OBSERVATION DATA] preparing row ' . $j . PHP_EOL;

                $randomNumberSeconds = rand(0, 60);

                $childBehaviourAssessmentObservationData = new ChildBehaviorAssessmentObservationData();
                $childBehaviourAssessmentObservationData->setObservationTimestamp(\Datetime::createFromFormat('U', $observationBaselineSessionStartTimeStamp + $randomNumberSeconds));
                $childBehaviourAssessmentObservationData->setObservationSession($childBehaviourAssessmentObservationBaselineSession);
                $childBehaviourAssessmentObservationBaselineSession->addObservation($childBehaviourAssessmentObservationData);
            }

            $timestampBaselineSessionStartDate+=86400;

            $observationsNumber = array_rand($observationsNumbers);

            for($j=1; $j<=$observationsNumbers[$observationsNumber]; $j++){
                echo '[CHILD BEHAVIOUR ASSESSMENT][' . $j . '][CHILD BEHAVIOUR ASSESSMENT INTERVENTION OBSERVATION DATA] preparing row ' . $j . PHP_EOL;

                $randomNumberSeconds = rand(0, 60);

                $childBehaviourAssessmentObservationData = new ChildBehaviorAssessmentObservationData();
                $childBehaviourAssessmentObservationData->setObservationTimestamp(\Datetime::createFromFormat('U', $observationInterventionSessionStartTimeStamp + $randomNumberSeconds));
                $childBehaviourAssessmentObservationData->setObservationSession($childBehaviourAssessmentObservationInterventionSession);
                $childBehaviourAssessmentObservationInterventionSession->addObservation($childBehaviourAssessmentObservationData);
            }

            $timestampInterventionSessionStartDate+=86400;
        }

        $childBehaviourAssessmentBaseline->setIntervention($childBehaviorAssessmentIntervention);

        return $childBehaviourAssessmentBaseline;
    }
}