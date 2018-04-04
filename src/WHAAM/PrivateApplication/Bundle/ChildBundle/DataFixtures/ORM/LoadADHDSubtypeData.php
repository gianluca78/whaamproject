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
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ADHDSubtype;

class LoadADHDSubtypeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $handle = fopen(__DIR__.'/../data/adhd-subtypes.csv', 'r');

        $row = 0;

        if($handle !== false) {
            while ($data = fgetcsv($handle, 0, '|')) {
                
                $adhdSubtype = new ADHDSubtype();
                $adhdSubtype->setTranslatableLocale('en_GB');
                $adhdSubtype->setSubtype($data[0]);
                
                echo '[ADHD SUBTYPE][EN] preparing row ' . $row . PHP_EOL;

                $manager->persist($adhdSubtype);
                $manager->flush();

                $this->addReference('adhd-subtype-'.$row, $adhdSubtype);

                if(isset($data[1])) {
                    echo '[ADHD SUBTYPE][GR] preparing row ' . $row . PHP_EOL;

                    $adhdSubtype->setSubtype($data[1]);
                    $adhdSubtype->setTranslatableLocale('el_GR');
                    $manager->persist($adhdSubtype);
                    $manager->flush();
                }

                if(isset($data[2])) {
                    echo '[ADHD SUBTYPE][IT] preparing row ' . $row . PHP_EOL;

                    $adhdSubtype->setSubtype($data[2]);
                    $adhdSubtype->setTranslatableLocale('it_IT');
                    $manager->persist($adhdSubtype);
                    $manager->flush();
                }

                if(isset($data[3])) {
                    echo '[ADHD SUBTYPE][PT] preparing row ' . $row . PHP_EOL;

                    $adhdSubtype->setSubtype($data[3]);
                    $adhdSubtype->setTranslatableLocale('pt_PT');
                    $manager->persist($adhdSubtype);
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