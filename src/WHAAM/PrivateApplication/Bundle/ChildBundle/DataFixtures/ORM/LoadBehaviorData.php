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
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Behavior;

class LoadBehaviorData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $handle = fopen(__DIR__.'/../data/behaviors.csv', 'r');

        $row = 0;

        if($handle !== false) {
            while ($data = fgetcsv($handle, 0, '|')) {

                $behavior = new Behavior();
                $behavior->setTranslatableLocale('en_GB');
                $behavior->setBehavior($data[1]);
                $behavior->setBehaviorCategory($this->getReference('behavior-category-' . $data[0]));

                echo '[BEHAVIOR][EN] preparing row ' . $row . PHP_EOL;

                $manager->persist($behavior);
                $manager->flush();

                $this->addReference('behavior-'.$row, $behavior);

                if(isset($data[2])) {
                    echo '[BEHAVIOR][GR] preparing row ' . $row . PHP_EOL;

                    $behavior->setTranslatableLocale('el_GR');
                    $behavior->setBehavior($data[2]);
                    $behavior->setBehaviorCategory($this->getReference('behavior-category-' . $data[0]));
                    $manager->persist($behavior);
                    $manager->flush();
                }

                if(isset($data[3])) {
                    echo '[BEHAVIOR][IT] preparing row ' . $row . PHP_EOL;

                    $behavior->setTranslatableLocale('it_IT');
                    $behavior->setBehavior($data[3]);
                    $behavior->setBehaviorCategory($this->getReference('behavior-category-' . $data[0]));
                    $manager->persist($behavior);
                    $manager->flush();
                }

                if(isset($data[4])) {
                    echo '[BEHAVIOR][PT] preparing row ' . $row . PHP_EOL;

                    $behavior->setTranslatableLocale('pt_PT');
                    $behavior->setBehavior($data[4]);
                    $behavior->setBehaviorCategory($this->getReference('behavior-category-' . $data[0]));
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
        return 2;
    }
}