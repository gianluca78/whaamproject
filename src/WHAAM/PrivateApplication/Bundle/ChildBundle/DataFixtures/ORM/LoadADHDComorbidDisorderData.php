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
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ADHDComorbidity;

class LoadADHDComorbidDisorderData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $handle = fopen(__DIR__.'/../data/adhd-comorbid-disorders.csv', 'r');

        $row = 0;

        if($handle !== false) {
            while ($data = fgetcsv($handle, 0, '|')) {
                
                $adhdComorbidity = new ADHDComorbidity();
                $adhdComorbidity->setTranslatableLocale('en_GB');
                $adhdComorbidity->setComorbidity($data[0]);
                
                echo '[ADHD COMORBIDITY][EN] preparing row ' . $row . PHP_EOL;

                $manager->persist($adhdComorbidity);
                $manager->flush();

                $this->addReference('adhd-comorbidity-'.$row, $adhdComorbidity);

                if(isset($data[1])) {
                    echo '[ADHD COMORBIDITY][GR] preparing row ' . $row . PHP_EOL;

                    $adhdComorbidity->setComorbidity($data[1]);
                    $adhdComorbidity->setTranslatableLocale('el_GR');
                    $manager->persist($adhdComorbidity);
                    $manager->flush();
                }

                if(isset($data[2])) {
                    echo '[ADHD COMORBIDITY][IT] preparing row ' . $row . PHP_EOL;

                    $adhdComorbidity->setComorbidity($data[2]);
                    $adhdComorbidity->setTranslatableLocale('it_IT');
                    $manager->persist($adhdComorbidity);
                    $manager->flush();
                }

                if(isset($data[3])) {
                    echo '[ADHD COMORBIDITY][PT] preparing row ' . $row . PHP_EOL;

                    $adhdComorbidity->setComorbidity($data[3]);
                    $adhdComorbidity->setTranslatableLocale('pt_PT');
                    $manager->persist($adhdComorbidity);
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