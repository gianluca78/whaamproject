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
namespace WHAAM\PrivateApplication\Bundle\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use WHAAM\PrivateApplication\Bundle\UserBundle\Entity\HealthProfessionalClientAgeRange;

class LoadHealthProfessionalClientAgeRangeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $handle = fopen(__DIR__.'/../data/hp-clients-age-range.csv', 'r');

        $row = 0;

        if($handle !== false) {
            while ($data = fgetcsv($handle, 0, '|')) {

                $healthProfessionalClientAge = new HealthProfessionalClientAgeRange();
                $healthProfessionalClientAge->setTranslatableLocale('en_GB');
                $healthProfessionalClientAge->setAgeRange($data[0]);

                echo '[HP CLIENT AGE][EN] preparing row ' . $row . PHP_EOL;

                $manager->persist($healthProfessionalClientAge);
                $manager->flush();

                $this->addReference('hp-client-age-'.$row, $healthProfessionalClientAge);

                if(isset($data[1])) {
                    echo '[HP CLIENT AGE][GR] preparing row ' . $row . PHP_EOL;

                    $healthProfessionalClientAge->setAgeRange($data[1]);
                    $healthProfessionalClientAge->setTranslatableLocale('el_GR');
                    $manager->persist($healthProfessionalClientAge);
                    $manager->flush();
                }

                if(isset($data[2])) {
                    echo '[HP CLIENT AGE][IT] preparing row ' . $row . PHP_EOL;

                    $healthProfessionalClientAge->setAgeRange($data[2]);
                    $healthProfessionalClientAge->setTranslatableLocale('it_IT');
                    $manager->persist($healthProfessionalClientAge);
                    $manager->flush();
                }

                if(isset($data[3])) {
                    echo '[HP CLIENT AGE][PT] preparing row ' . $row . PHP_EOL;

                    $healthProfessionalClientAge->setAgeRange($data[3]);
                    $healthProfessionalClientAge->setTranslatableLocale('pt_PT');
                    $manager->persist($healthProfessionalClientAge);
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