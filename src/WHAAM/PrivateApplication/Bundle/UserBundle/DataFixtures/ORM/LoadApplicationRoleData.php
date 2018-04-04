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
use WHAAM\PrivateApplication\Bundle\UserBundle\Entity\ApplicationRole;

class LoadApplicationRoleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $handle = fopen(__DIR__.'/../data/application-roles.csv', 'r');

        $row = 0;

        if($handle !== false) {
            while ($data = fgetcsv($handle, 0, '|')) {
                $applicationRole = new ApplicationRole();
                $applicationRole->setTranslatableLocale('en_GB');
                $applicationRole->setRoleName($data[0]);
                $applicationRole->setRole($data[4]);

                echo '[APPLICATION ROLE][EN] preparing row ' . $row . PHP_EOL;

                $manager->persist($applicationRole);
                $manager->flush();

                $this->addReference('application-role-'.$row, $applicationRole);

                if(isset($data[1])) {
                    echo '[APPLICATION ROLE][GR] preparing row ' . $row . PHP_EOL;

                    $applicationRole->setRoleName($data[1]);
                    $applicationRole->setTranslatableLocale('el_GR');
                    $manager->persist($applicationRole);
                    $manager->flush();
                }

                if(isset($data[2])) {
                    echo '[APPLICATION ROLE][IT] preparing row ' . $row . PHP_EOL;

                    $applicationRole->setRoleName($data[2]);
                    $applicationRole->setTranslatableLocale('it_IT');
                    $manager->persist($applicationRole);
                    $manager->flush();
                }

                if(isset($data[3])) {
                    echo '[APPLICATION ROLE][PT] preparing row ' . $row . PHP_EOL;

                    $applicationRole->setRoleName($data[3]);
                    $applicationRole->setTranslatableLocale('pt_PT');
                    $manager->persist($applicationRole);
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