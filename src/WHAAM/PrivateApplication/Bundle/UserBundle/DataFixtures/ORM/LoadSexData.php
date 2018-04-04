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
use WHAAM\PrivateApplication\Bundle\UserBundle\Entity\Sex;

class LoadSexData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $handle = fopen(__DIR__.'/../data/sex.csv', 'r');

        $row = 0;

        if($handle !== false) {
            while ($data = fgetcsv($handle, 0, '|')) {
                $sex = new Sex();
                $sex->setTranslatableLocale('en_GB');
                $sex->setSex($data[0]);
                
                echo '[SEX][EN] preparing row ' . $row . PHP_EOL;

                $manager->persist($sex);
                $manager->flush();

                $this->addReference('sex-'.$row, $sex);

                if(isset($data[1])) {
                    echo '[SEX][GR] preparing row ' . $row . PHP_EOL;

                    $sex->setTranslatableLocale('el_GR');
                    $sex->setSex($data[1]);

                    $manager->persist($sex);
                    $manager->flush();
                }

                if(isset($data[2])) {
                    echo '[SEX][IT] preparing row ' . $row . PHP_EOL;

                    $sex->setTranslatableLocale('it_IT');
                    $sex->setSex($data[2]);

                    $manager->persist($sex);
                    $manager->flush();
                }

                if(isset($data[3])) {
                    echo '[SEX][PT] preparing row ' . $row . PHP_EOL;

                    $sex->setTranslatableLocale('pt_PT');
                    $sex->setSex($data[3]);

                    $manager->persist($sex);
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