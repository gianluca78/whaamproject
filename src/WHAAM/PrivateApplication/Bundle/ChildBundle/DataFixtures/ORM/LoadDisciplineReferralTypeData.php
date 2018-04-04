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
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\DisciplineReferralType;

class LoadDisciplineReferralTypeData extends AbstractFixture implements OrderedFixtureInterface
{
    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $handle = fopen(__DIR__.'/../data/discipline-referral-types.csv', 'r');

        $row = 0;

        if($handle !== false) {
            while ($data = fgetcsv($handle, 0, '|')) {
                
                $disciplineReferralType = new DisciplineReferralType();
                $disciplineReferralType->setTranslatableLocale('en_GB');
                $disciplineReferralType->setType($data[0]);
                
                echo '[DISCIPLINE REFERRAL TYPE][EN] preparing row ' . $row . PHP_EOL;

                $manager->persist($disciplineReferralType);
                $manager->flush();

                $this->addReference('discipline-referral-'.$row, $disciplineReferralType);

                if(isset($data[1])) {
                    echo '[DISCIPLINE REFERRAL TYPE][GR] preparing row ' . $row . PHP_EOL;

                    $disciplineReferralType->setType($data[1]);
                    $disciplineReferralType->setTranslatableLocale('el_GR');
                    $manager->persist($disciplineReferralType);
                    $manager->flush();
                }

                if(isset($data[2])) {
                    echo '[DISCIPLINE REFERRAL TYPE][IT] preparing row ' . $row . PHP_EOL;

                    $disciplineReferralType->setType($data[2]);
                    $disciplineReferralType->setTranslatableLocale('it_IT');
                    $manager->persist($disciplineReferralType);
                    $manager->flush();
                }

                if(isset($data[3])) {
                    echo '[DISCIPLINE REFERRAL TYPE][PT] preparing row ' . $row . PHP_EOL;

                    $disciplineReferralType->setType($data[3]);
                    $disciplineReferralType->setTranslatableLocale('pt_PT');
                    $manager->persist($disciplineReferralType);
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