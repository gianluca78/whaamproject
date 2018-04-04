<?php
/*
 * This file is part of the WHAAM project funded with support from the European Commission.
 *
 * Reference project number: 531244-LLP-2012-IT-KA3MP
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @author Gianluca Merlo
 * @author Giuseppe Chiazzese
 */
namespace WHAAM\PrivateApplication\Bundle\ChildBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child;
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildADHDDiagnosis;
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildDisciplineReferral;
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildGeneralEvent;
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildMedication;
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildSchoolInformation;
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildSibling;
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUser;
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildBehavior;

class LoadChildData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $languages = array("en_EN","el_GR","it_IT","pt_BR");
        $row = 1;
        $userNumbers = range(1,100);
        $childUserRole = range(1,12);
        shuffle($userNumbers);

        for($i=1; $i<=5; $i++) {
            foreach($languages as $language) {

                $faker = Factory::create($language);

                $child = new Child();
                $child->setNickname($faker->unique()->userName);
                $child->setName($faker->name);
                $child->setSurname($faker->lastName);
                $child->setSex($this->getReference('sex-' . array_rand(array(0, 1))));
                $child->setYearOfBirth($faker->year);
                $child->setChildCreatorUser($this->getReference('user-' . array_pop($userNumbers)));
                $manager->persist($child);
                $manager->flush();

                $childGeneralEvent = new ChildGeneralEvent();
                $childGeneralEvent->setDate($faker->dateTimeThisCentury);
                $childGeneralEvent->setDescription($faker->text());
                $childGeneralEvent->setChild($child);

                $childMedication = new ChildMedication();
                $childMedication->setStartDate($faker->dateTimeThisCentury);
                $childMedication->setEndDate($faker->dateTimeThisCentury);
                $childMedication->setName($faker->word);
                $childMedication->setFrequency($faker->word);
                $childMedication->setDosage($faker->randomDigit);
                $childMedication->setChild($child);

                $schoolInformation = new ChildSchoolInformation();
                $schoolInformation->setGrade($faker->randomDigit . ' grade');
                $schoolInformation->setHasIndividualEducationPlan(array_rand(array(false, true)));
                $schoolInformation->setHasSpecialNeedSupportTeacher(array_rand(array(false, true)));
                $schoolInformation->setSchoolName($faker->company);
                $schoolInformation->setYear($faker->year);
                $schoolInformation->setChild($child);

                $childSibling = new ChildSibling();
                $childSibling->setNickname($faker->unique()->userName);
                $childSibling->setName($faker->name);
                $childSibling->setSex($this->getReference('sex-' . array_rand(array(0, 1))));
                $childSibling->setYearOfBirth($faker->year);
                $childSibling->setChild($child);

                $childADHDDiagnosis = new ChildADHDDiagnosis();
                $childADHDDiagnosis->setDiagnosisDate($faker->dateTimeThisCentury);
                $childADHDDiagnosis->setIsSecondaryDisorder(array_rand(array(false, true)));
                $childADHDDiagnosis->setOnsetAge($faker->randomDigitNotNull);
                $childADHDDiagnosis->addComorbidity($this->getReference('adhd-comorbidity-' . rand(0, 12)));
                $childADHDDiagnosis->setSubtype($this->getReference('adhd-subtype-' . rand(0, 2)));
                $childADHDDiagnosis->setChild($child);

                $childDisciplineReferral = new ChildDisciplineReferral();
                $childDisciplineReferral->setDate($faker->dateTimeThisCentury);
                $childDisciplineReferral->setDisciplineReferralType($this->getReference('discipline-referral-' . rand(0, 7)));
                $childDisciplineReferral->setMotivation($faker->sentence());
                $childDisciplineReferral->setChild($child);

                $child->addGeneralEvent($childGeneralEvent);
                $child->addMedication($childMedication);
                $child->addSchoolsInformation($schoolInformation);
                $child->addSibling($childSibling);
                $child->addDisciplineReferral($childDisciplineReferral);
                $child->addDiagnosis($childADHDDiagnosis);

                $manager->persist($child);
                $manager->flush();

                $childUser = new ChildUser();
                $childUser->setChild($child);
                $childUser->setUser($this->getReference('test'));
                $childUser->setRole($this->getReference('child-user-role-7'));
                $childUser->setIsApprovedByParent(1);

                $child->addChildUser($childUser);

                $childUser = new ChildUser();
                $childUser->setChild($child);
                $childUser->setUser($this->getReference('apple-user'));
                $childUser->setRole($this->getReference('child-user-role-7'));
                $childUser->setIsApprovedByParent(1);

                $child->addChildUser($childUser);

                $childUser = new ChildUser();
                $childUser->setChild($child);
                $childUser->setUser($this->getReference('google-user'));
                $childUser->setRole($this->getReference('child-user-role-7'));
                $childUser->setIsApprovedByParent(1);

                $child->addChildUser($childUser);

                $childUser = new ChildUser();
                $childUser->setChild($child);
                $childUser->setUser($this->getReference('user-'. array_pop($userNumbers)));
                $childUser->setRole($this->getReference('child-user-role-' . array_rand($childUserRole)));
                $childUser->setIsApprovedByParent(array_rand(array(true, false)));

                $child->addChildUser($childUser);

                $childUser = new ChildUser();
                $childUser->setChild($child);
                $childUser->setUser($this->getReference('user-'. array_pop($userNumbers)));
                $childUser->setRole($this->getReference('child-user-role-' . array_rand($childUserRole)));
                $childUser->setIsApprovedByParent(array_rand(array(true, false)));

                $child->addChildUser($childUser);

                $childBehavior = new ChildBehavior();
                $childBehavior->setChild($child);
                $childBehavior->setBehavior($this->getReference('behavior-' . rand(0, 40)));
                $childBehavior->setDescription($faker->sentence());
                $childBehavior->setSetting($faker->sentence());
                $childBehavior->setPlace($faker->word);
                $childBehavior->setChildBehaviorCreatorUser($this->getReference('test'));
                $this->addReference('child-behaviour-'. $row, $childBehavior);

                $child->addBehavior($childBehavior);

                $childBehavior = new ChildBehavior();
                $childBehavior->setChild($child);
                $childBehavior->setBehavior($this->getReference('behavior-' . rand(0, 40)));
                $childBehavior->setDescription($faker->sentence());
                $childBehavior->setSetting($faker->sentence());
                $childBehavior->setPlace($faker->word);
                $childBehavior->setChildBehaviorCreatorUser($this->getReference('test'));

                $child->addBehavior($childBehavior);

                $childBehavior = new ChildBehavior();
                $childBehavior->setChild($child);
                $childBehavior->setBehavior($this->getReference('behavior-' . rand(0, 40)));
                $childBehavior->setDescription($faker->sentence());
                $childBehavior->setSetting($faker->sentence());
                $childBehavior->setPlace($faker->word);
                $childBehavior->setChildBehaviorCreatorUser($this->getReference('apple-user'));

                $child->addBehavior($childBehavior);

                $childBehavior = new ChildBehavior();
                $childBehavior->setChild($child);
                $childBehavior->setBehavior($this->getReference('behavior-' . rand(0, 40)));
                $childBehavior->setDescription($faker->sentence());
                $childBehavior->setSetting($faker->sentence());
                $childBehavior->setPlace($faker->word);
                $childBehavior->setChildBehaviorCreatorUser($this->getReference('google-user'));

                $child->addBehavior($childBehavior);

                echo '[CHILD]['. $language[3] . $language[4] . '] preparing row ' . $i . PHP_EOL;

                $manager->persist($child);
                $manager->flush();
                $row++;

            }
        }
    }

    public function getOrder()
    {
        return 3;
    }
}