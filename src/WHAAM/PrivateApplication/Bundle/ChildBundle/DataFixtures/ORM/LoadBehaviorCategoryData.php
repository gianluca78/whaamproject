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
 */
namespace WHAAM\PrivateApplication\Bundle\ChildBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\BehaviorCategory;

class LoadBehaviorCategoryData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $handle = fopen(__DIR__.'/../data/behavior-categories.csv', 'r');

        $row = 0;

        if($handle !== false) {
            while ($data = fgetcsv($handle, 0, '|')) {

                $behavior = new BehaviorCategory();
                $behavior->setTranslatableLocale('en_GB');
                $behavior->setBehaviorCategory($data[0]);

                echo '[BEHAVIOR CATEGORY][EN] preparing row ' . $row . PHP_EOL;

                $manager->persist($behavior);
                $manager->flush();

                $this->addReference('behavior-category-'.$row, $behavior);

                if(isset($data[1])) {
                    echo '[BEHAVIOR CATEGORY][GR] preparing row ' . $row . PHP_EOL;

                    $behavior->setTranslatableLocale('el_GR');
                    $behavior->setBehaviorCategory($data[1]);
                    $manager->persist($behavior);
                    $manager->flush();
                }

                if(isset($data[2])) {
                    echo '[BEHAVIOR CATEGORY][IT] preparing row ' . $row . PHP_EOL;

                    $behavior->setTranslatableLocale('it_IT');
                    $behavior->setBehaviorCategory($data[2]);
                    $manager->persist($behavior);
                    $manager->flush();
                }

                if(isset($data[3])) {
                    echo '[BEHAVIOR CATEGORY][PT] preparing row ' . $row . PHP_EOL;

                    $behavior->setTranslatableLocale('pt_PT');
                    $behavior->setBehaviorCategory($data[3]);
                    $manager->persist($behavior);
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