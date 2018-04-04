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
 */
namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\BehaviorFunction;

class LoadBehaviorFunction extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $handle = fopen(__DIR__.'/../data/behavior-function.csv', 'r');

        $row = 0;

        if($handle !== false) {
            while ($data = fgetcsv($handle, 0, '|')) {
                $behaviorFunction = new BehaviorFunction();
                $behaviorFunction->setTranslatableLocale('en_GB');
                $behaviorFunction->setBehaviorFunction($data[0]);

                echo '[BEHAVIOR FUNCTION][EN] preparing row ' . $row . PHP_EOL;

                $manager->persist($behaviorFunction);
                $manager->flush();

                $this->addReference('behavior-function-'.$row, $behaviorFunction);

                if(isset($data[1])) {
                    echo '[BEHAVIOR FUNCTION][GR] preparing row ' . $row . PHP_EOL;

                    $behaviorFunction->setBehaviorFunction($data[1]);
                    $behaviorFunction->setTranslatableLocale('el_GR');
                    $manager->persist($behaviorFunction);
                    $manager->flush();
                }

                if(isset($data[2])) {
                    echo '[BEHAVIOR FUNCTION][IT] preparing row ' . $row . PHP_EOL;

                    $behaviorFunction->setBehaviorFunction($data[2]);
                    $behaviorFunction->setTranslatableLocale('it_IT');
                    $manager->persist($behaviorFunction);
                    $manager->flush();
                }

                if(isset($data[3])) {
                    echo '[BEHAVIOR FUNCTION][PT] preparing row ' . $row . PHP_EOL;

                    $behaviorFunction->setBehaviorFunction($data[3]);
                    $behaviorFunction->setTranslatableLocale('pt_PT');
                    $manager->persist($behaviorFunction);
                    $manager->flush();
                }

                $row++;
            }
        }

        fclose($handle);
    }

    public function getOrder()
    {
        return 1;
    }
}