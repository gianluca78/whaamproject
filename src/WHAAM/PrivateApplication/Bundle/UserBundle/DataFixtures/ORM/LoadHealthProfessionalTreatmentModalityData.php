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
use WHAAM\PrivateApplication\Bundle\UserBundle\Entity\HealthProfessionalTreatmentModality;

class LoadHealthProfessionalTreatmentModalityData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $handle = fopen(__DIR__.'/../data/hp-treatment-modalities.csv', 'r');

        $row = 0;

        if($handle !== false) {
            while ($data = fgetcsv($handle, 0, '|')) {

                $healthProfessionalTreatmentModality = new HealthProfessionalTreatmentModality();
                $healthProfessionalTreatmentModality->setTranslatableLocale('en_GB');
                $healthProfessionalTreatmentModality->setModality($data[0]);

                echo '[HP TREATMENT MODALITY][EN] preparing row ' . $row . PHP_EOL;

                $manager->persist($healthProfessionalTreatmentModality);
                $manager->flush();

                $this->addReference('hp-treatment-modality-'. $row, $healthProfessionalTreatmentModality);

                if(isset($data[1])) {
                    echo '[HP TREATMENT MODALITY][GR] preparing row ' . $row . PHP_EOL;

                    $healthProfessionalTreatmentModality->setModality($data[1]);
                    $healthProfessionalTreatmentModality->setTranslatableLocale('el_GR');
                    $manager->persist($healthProfessionalTreatmentModality);
                    $manager->flush();
                }

                if(isset($data[2])) {
                    echo '[HP TREATMENT MODALITY][IT] preparing row ' . $row . PHP_EOL;

                    $healthProfessionalTreatmentModality->setModality($data[2]);
                    $healthProfessionalTreatmentModality->setTranslatableLocale('it_IT');
                    $manager->persist($healthProfessionalTreatmentModality);
                    $manager->flush();
                }

                if(isset($data[3])) {
                    echo '[HP TREATMENT MODALITY][PT] preparing row ' . $row . PHP_EOL;

                    $healthProfessionalTreatmentModality->setModality($data[3]);
                    $healthProfessionalTreatmentModality->setTranslatableLocale('pt_PT');
                    $manager->persist($healthProfessionalTreatmentModality);
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