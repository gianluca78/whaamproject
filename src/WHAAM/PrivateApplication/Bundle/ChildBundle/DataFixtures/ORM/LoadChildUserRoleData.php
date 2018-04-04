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
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUserRole;

class LoadChildUserRoleData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $handle = fopen(__DIR__.'/../data/child-users-roles.csv', 'r');

        $row = 0;

        if($handle !== false) {
            while ($data = fgetcsv($handle, 0, '|')) {
                $childUserRole = new ChildUserRole();
                $childUserRole->setTranslatableLocale('en_GB');
                $childUserRole->setRoleName($data[0]);
                $childUserRole->setRole($data[4]);

                echo '[CHILD USER ROLE][EN] preparing row ' . $row . PHP_EOL;

                $manager->persist($childUserRole);
                $manager->flush();

                $this->addReference('child-user-role-'.$row, $childUserRole);

                if(isset($data[1])) {
                    echo '[CHILD USER ROLE][GR] preparing row ' . $row . PHP_EOL;

                    $childUserRole->setRoleName($data[1]);
                    $childUserRole->setTranslatableLocale('el_GR');
                    $manager->persist($childUserRole);
                    $manager->flush();
                }

                if(isset($data[2])) {
                    echo '[CHILD USER ROLE][IT] preparing row ' . $row . PHP_EOL;

                    $childUserRole->setRoleName($data[2]);
                    $childUserRole->setTranslatableLocale('it_IT');
                    $manager->persist($childUserRole);
                    $manager->flush();
                }

                if(isset($data[3])) {
                    echo '[CHILD USER ROLE][PT] preparing row ' . $row . PHP_EOL;

                    $childUserRole->setRoleName($data[3]);
                    $childUserRole->setTranslatableLocale('pt_PT');
                    $manager->persist($childUserRole);
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