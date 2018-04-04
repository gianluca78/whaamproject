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
use WHAAM\PrivateApplication\Bundle\UserBundle\Entity\HealthProfessionalTreatmentApproach;

class LoadHealthProfessionalTreatmentApproachData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $handle = fopen(__DIR__.'/../data/hp-treatment-approaches.csv', 'r');

        $row = 0;

        if($handle !== false) {
            while ($data = fgetcsv($handle, 0, '|')) {

                $healthProfessionalTreatmentApproach = new HealthProfessionalTreatmentApproach();
                $healthProfessionalTreatmentApproach->setTranslatableLocale('en_GB');
                $healthProfessionalTreatmentApproach->setApproach($data[0]);

                echo '[HP TREATMENT APPROACH][EN] preparing row ' . $row . PHP_EOL;

                $manager->persist($healthProfessionalTreatmentApproach);
                $manager->flush();

                $this->addReference('hp-treatment-approach-' . $row, $healthProfessionalTreatmentApproach);

                if(isset($data[1])) {
                    echo '[HP TREATMENT APPROACH][GR] preparing row ' . $row . PHP_EOL;

                    $healthProfessionalTreatmentApproach->setApproach($data[1]);
                    $healthProfessionalTreatmentApproach->setTranslatableLocale('el_GR');

                    $manager->persist($healthProfessionalTreatmentApproach);
                    $manager->flush();
                }

                if(isset($data[2])) {
                    echo '[HP TREATMENT APPROACH][IT] preparing row ' . $row . PHP_EOL;

                    $healthProfessionalTreatmentApproach->setApproach($data[2]);
                    $healthProfessionalTreatmentApproach->setTranslatableLocale('it_IT');

                    $manager->persist($healthProfessionalTreatmentApproach);
                    $manager->flush();
                }

                if(isset($data[3])) {
                    echo '[HP TREATMENT APPROACH][PT] preparing row ' . $row . PHP_EOL;

                    $healthProfessionalTreatmentApproach->setApproach($data[3]);
                    $healthProfessionalTreatmentApproach->setTranslatableLocale('pt_PT');

                    $manager->persist($healthProfessionalTreatmentApproach);
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