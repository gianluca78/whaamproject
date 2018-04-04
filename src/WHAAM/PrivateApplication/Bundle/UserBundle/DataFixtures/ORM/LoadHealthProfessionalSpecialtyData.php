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
use WHAAM\PrivateApplication\Bundle\UserBundle\Entity\HealthProfessionalSpecialty;

class LoadHealthProfessionalSpecialtyData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $handle = fopen(__DIR__.'/../data/hp-specialties.csv', 'r');

        $row = 0;

        if($handle !== false) {
            while ($data = fgetcsv($handle, 0, '|')) {

                $healthProfessionalSpecialty = new HealthProfessionalSpecialty();
                $healthProfessionalSpecialty->setTranslatableLocale('en_GB');
                $healthProfessionalSpecialty->setSpecialty($data[0]);

                echo '[HP SPECIALTY][EN] preparing row ' . $row . PHP_EOL;

                $manager->persist($healthProfessionalSpecialty);
                $manager->flush();

                $this->addReference('hp-specialty-' . $row, $healthProfessionalSpecialty);

                if(isset($data[1])) {
                    echo '[HP SPECIALTY][GR] preparing row ' . $row . PHP_EOL;

                    $healthProfessionalSpecialty->setSpecialty($data[1]);
                    $healthProfessionalSpecialty->setTranslatableLocale('el_GR');
                    $manager->persist($healthProfessionalSpecialty);
                    $manager->flush();
                }

                if(isset($data[2])) {
                    echo '[HP SPECIALTY][IT] preparing row ' . $row . PHP_EOL;

                    $healthProfessionalSpecialty->setSpecialty($data[2]);
                    $healthProfessionalSpecialty->setTranslatableLocale('it_IT');
                    $manager->persist($healthProfessionalSpecialty);
                    $manager->flush();
                }

                if(isset($data[3])) {
                    echo '[HP SPECIALTY][PT] preparing row ' . $row . PHP_EOL;

                    $healthProfessionalSpecialty->setSpecialty($data[3]);
                    $healthProfessionalSpecialty->setTranslatableLocale('pt_PT');
                    $manager->persist($healthProfessionalSpecialty);
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