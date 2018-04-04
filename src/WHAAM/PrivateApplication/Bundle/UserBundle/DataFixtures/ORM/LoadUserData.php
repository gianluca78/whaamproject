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
 * @author Giuseppe Chiazzese
 */
namespace WHAAM\PrivateApplication\Bundle\UserBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Faker\Factory;
use WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User;

class LoadUserData extends AbstractFixture implements OrderedFixtureInterface, ContainerAwareInterface
{
    private $container;
    private $hpSpecialtiesNumber = 4;
    private $hpTreatmentApproachesNumber = 4;
    private $nationsNumber = 4;

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $languages = array("en_EN","el_GR","it_IT","pt_BR");

        $faker = Factory::create($languages[0]);

        //user for functional tests
        $user = new User();
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $user->setName($faker->name);
        $user->setSurname($faker->lastName);
        $user->setDateOfBirth($faker->dateTimeThisCentury);
        $user->setSex($this->getReference('sex-' . array_rand(array(0, 1))));
        $user->setUsername('test');
        $user->setPassword($encoder->encodePassword('test', $user->getSalt()));
        $user->setEmail($faker->unique()->email);
        $user->setRole($this->getReference('application-role-2'));
        $user->addHealthProfessionalClientsAgeRange($this->getReference('hp-client-age-' . rand(1, 5)));
        $user->addHealthProfessionalSpecialty($this->getReference('hp-specialty-' . rand(1, $this->hpSpecialtiesNumber)));
        $user->addHealthProfessionalTreatmentApproach($this->getReference('hp-treatment-approach-' . rand(1, $this->hpTreatmentApproachesNumber)));
        $user->addHealthProfessionalTreatmentModality($this->getReference('hp-treatment-modality-' . rand(1, 2)));
        $user->setNation($this->getReference('nation-' . rand(0, $this->nationsNumber)));
        $user->setIsActive(1);
        $user->setIsLocked(0);
        $user->setIsHealthProfessional(0);
        $user->setSelectedLocale('en_GB');

        $encryptedSlug = $this->container->get('common_security.encoder.open_ssl_encoder')
            ->encrypt($user->getUsername());
        $user->setSlug($encryptedSlug);

        $manager->persist($user);
        $manager->flush();

        $this->addReference('test', $user);

        //user for other tests
        $user = new User();
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $user->setName($faker->name);
        $user->setSurname($faker->lastName);
        $user->setDateOfBirth($faker->dateTimeThisCentury);
        $user->setSex($this->getReference('sex-' . array_rand(array(0, 1))));
        $user->setUsername('testd');
        $user->setPassword($encoder->encodePassword('testd', $user->getSalt()));
        $user->setEmail($faker->unique()->email);
        $user->setRole($this->getReference('application-role-2'));
        $user->addHealthProfessionalClientsAgeRange($this->getReference('hp-client-age-' . rand(1, 5)));
        $user->addHealthProfessionalSpecialty($this->getReference('hp-specialty-' . rand(1, $this->hpSpecialtiesNumber)));
        $user->addHealthProfessionalTreatmentApproach($this->getReference('hp-treatment-approach-' . rand(1, $this->hpTreatmentApproachesNumber)));
        $user->addHealthProfessionalTreatmentModality($this->getReference('hp-treatment-modality-' . rand(1, 2)));
        $user->setNation($this->getReference('nation-' . rand(0, $this->nationsNumber)));
        $user->setIsActive(1);
        $user->setIsLocked(0);
        $user->setIsHealthProfessional(0);
        $user->setSelectedLocale('en_GB');

        $encryptedSlug = $this->container->get('common_security.encoder.open_ssl_encoder')
            ->encrypt($user->getUsername());
        $user->setSlug($encryptedSlug);

        $manager->persist($user);
        $manager->flush();

        $this->addReference('testd', $user);

        //user for other tests
        $user = new User();
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $user->setName($faker->name);
        $user->setSurname($faker->lastName);
        $user->setDateOfBirth($faker->dateTimeThisCentury);
        $user->setSex($this->getReference('sex-' . array_rand(array(0, 1))));
        $user->setUsername('testp');
        $user->setPassword($encoder->encodePassword('testp', $user->getSalt()));
        $user->setEmail($faker->unique()->email);
        $user->setRole($this->getReference('application-role-2'));
        $user->addHealthProfessionalClientsAgeRange($this->getReference('hp-client-age-' . rand(1, 5)));
        $user->addHealthProfessionalSpecialty($this->getReference('hp-specialty-' . rand(1, $this->hpSpecialtiesNumber)));
        $user->addHealthProfessionalTreatmentApproach($this->getReference('hp-treatment-approach-' . rand(1, $this->hpTreatmentApproachesNumber)));
        $user->addHealthProfessionalTreatmentModality($this->getReference('hp-treatment-modality-' . rand(1, 2)));
        $user->setNation($this->getReference('nation-' . rand(0, $this->nationsNumber)));
        $user->setIsActive(1);
        $user->setIsLocked(0);
        $user->setIsHealthProfessional(0);
        $user->setSelectedLocale('en_GB');

        $encryptedSlug = $this->container->get('common_security.encoder.open_ssl_encoder')
            ->encrypt($user->getUsername());
        $user->setSlug($encryptedSlug);

        $manager->persist($user);
        $manager->flush();

        $this->addReference('testp', $user);

        //apple user
        $user = new User();
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $user->setName($faker->name);
        $user->setSurname($faker->lastName);
        $user->setDateOfBirth($faker->dateTimeThisCentury);
        $user->setSex($this->getReference('sex-' . array_rand(array(0, 1))));
        $user->setUsername('Apple');
        $user->setPassword($encoder->encodePassword('Apple!', $user->getSalt()));
        $user->setEmail($faker->unique()->email);
        $user->setRole($this->getReference('application-role-2'));
        $user->addHealthProfessionalClientsAgeRange($this->getReference('hp-client-age-' . rand(1, 5)));
        $user->addHealthProfessionalSpecialty($this->getReference('hp-specialty-' . rand(1, $this->hpSpecialtiesNumber)));
        $user->addHealthProfessionalTreatmentApproach($this->getReference('hp-treatment-approach-' . rand(1, $this->hpTreatmentApproachesNumber)));
        $user->addHealthProfessionalTreatmentModality($this->getReference('hp-treatment-modality-' . rand(1, 2)));
        $user->setNation($this->getReference('nation-' . rand(0, $this->nationsNumber)));
        $user->setIsActive(1);
        $user->setIsLocked(0);
        $user->setIsHealthProfessional(0);
        $user->setSelectedLocale('en_GB');

        $encryptedSlug = $this->container->get('common_security.encoder.open_ssl_encoder')
            ->encrypt($user->getUsername());
        $user->setSlug($encryptedSlug);

        $manager->persist($user);
        $manager->flush();

        $this->addReference('apple-user', $user);

        //google user
        $user = new User();
        $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
        $user->setName($faker->name);
        $user->setSurname($faker->lastName);
        $user->setDateOfBirth($faker->dateTimeThisCentury);
        $user->setSex($this->getReference('sex-' . array_rand(array(0, 1))));
        $user->setUsername('Google');
        $user->setPassword($encoder->encodePassword('Google!', $user->getSalt()));
        $user->setEmail($faker->unique()->email);
        $user->setRole($this->getReference('application-role-2'));
        $user->addHealthProfessionalClientsAgeRange($this->getReference('hp-client-age-' . rand(1, 5)));
        $user->addHealthProfessionalSpecialty($this->getReference('hp-specialty-' . rand(1, $this->hpSpecialtiesNumber)));
        $user->addHealthProfessionalTreatmentApproach($this->getReference('hp-treatment-approach-' . rand(1, $this->hpTreatmentApproachesNumber)));
        $user->addHealthProfessionalTreatmentModality($this->getReference('hp-treatment-modality-' . rand(1, 2)));
        $user->setNation($this->getReference('nation-' . rand(0, $this->nationsNumber)));
        $user->setIsActive(1);
        $user->setIsLocked(0);
        $user->setIsHealthProfessional(0);
        $user->setSelectedLocale('en_GB');

        $encryptedSlug = $this->container->get('common_security.encoder.open_ssl_encoder')
            ->encrypt($user->getUsername());
        $user->setSlug($encryptedSlug);

        $manager->persist($user);
        $manager->flush();

        $this->addReference('google-user', $user);

        foreach($languages as $key => $language)
        {
            $faker = Factory::create($language);
            for($i=1; $i<=100; $i++) {
                $user = new User();
                $encoder = $this->container->get('security.encoder_factory')->getEncoder($user);
                $user->setName($faker->name);
                $user->setSurname($faker->lastName);
                $user->setDateOfBirth($faker->dateTimeThisCentury);
                $user->setSex($this->getReference('sex-' . array_rand(array(0, 1))));
                $user->setUsername($faker->unique()->userName);
                $user->setPassword($encoder->encodePassword($faker->word, $user->getSalt()));
                $user->setEmail($faker->unique()->email);
                $user->setRole($this->getReference('application-role-2'));
                $user->addHealthProfessionalClientsAgeRange($this->getReference('hp-client-age-' . rand(1, 5)));
                $user->addHealthProfessionalSpecialty($this->getReference('hp-specialty-' . rand(1, $this->hpSpecialtiesNumber)));
                $user->addHealthProfessionalTreatmentApproach($this->getReference('hp-treatment-approach-' . rand(1, $this->hpTreatmentApproachesNumber)));
                $user->addHealthProfessionalTreatmentModality($this->getReference('hp-treatment-modality-' . rand(1, 2)));
                $user->setNation($this->getReference('nation-' . rand(0, $this->nationsNumber)));
                $user->setIsActive(1);
                $user->setIsLocked(0);
                $user->setIsHealthProfessional(0);
                $user->setSelectedLocale('en_GB');
                $encryptedSlug = $this->container->get('common_security.encoder.open_ssl_encoder')
                    ->encrypt($user->getUsername());
                $user->setSlug($encryptedSlug);


                $manager->persist($user);

                if($key === 0) {
                    $this->addReference('user-'.$i, $user);
                }

                if($i % 20 == 0) {
                    echo '[USER]['. $language[3] . $language[4] . '] preparing row ' . $i . PHP_EOL;
                    $manager->flush();
                    $manager->clear();
                }
            }
            $manager->flush();
            $manager->clear();
        }
    }

    public function getOrder()
    {
        return 2;
    }

    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}