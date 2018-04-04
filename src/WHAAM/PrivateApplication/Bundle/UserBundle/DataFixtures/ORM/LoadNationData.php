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
namespace WHAAM\PrivateApplication\Bundle\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use WHAAM\PrivateApplication\Bundle\UserBundle\Entity\Nation;

class LoadNationData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $handle = fopen(__DIR__.'/../data/project-nation.csv', 'r');

        $row = 0;

        if($handle !== false) {
            while ($data = fgetcsv($handle, 0, '|')) {
                $nation = new Nation();
                $nation->setTranslatableLocale('en_GB');
                $nation->setNation($data[0]);

                echo '[NATION][EN] preparing row ' . $row . PHP_EOL;

                $manager->persist($nation);
                $manager->flush();

                $this->addReference('nation-'.$row, $nation);

                if(isset($data[1])) {
                    echo '[NATION][GR] preparing row ' . $row . PHP_EOL;

                    $nation->setNation($data[1]);
                    $nation->setTranslatableLocale('el_GR');
                    $manager->persist($nation);
                    $manager->flush();
                }

                if(isset($data[2])) {
                    echo '[NATION][IT] preparing row ' . $row . PHP_EOL;

                    $nation->setNation($data[2]);
                    $nation->setTranslatableLocale('it_IT');
                    $manager->persist($nation);
                    $manager->flush();
                }

                if(isset($data[3])) {
                    echo '[NATION][PT] preparing row ' . $row . PHP_EOL;

                    $nation->setNation($data[3]);
                    $nation->setTranslatableLocale('pt_PT');
                    $manager->persist($nation);
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