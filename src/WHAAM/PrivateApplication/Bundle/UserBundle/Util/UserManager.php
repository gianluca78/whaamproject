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
namespace WHAAM\PrivateApplication\Bundle\UserBundle\Util;

use Barzo\Password\Generator;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User;

class UserManager {

    private $encoderFactory;
    private $entityManager;

    public function __construct(EncoderFactory $encoderFactory, EntityManager $entityManager)
    {
        $this->encoderFactory = $encoderFactory;
        $this->entityManager = $entityManager;
    }

    public function refreshEmailToken(User $user)
    {
        $user->setEmailToken($user->generateToken());
        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $user;
    }

    public function refreshPassword(User $user)
    {
        $plainTextNewPassword = Generator::generateEn(4, '-');

        $encoder = $this->encoderFactory->getEncoder($user);
        $encodedPassword = $encoder->encodePassword($plainTextNewPassword, $user->getSalt());
        $user->setPassword($encodedPassword);

        $this->refreshEmailToken($user);

        $this->entityManager->persist($user);
        $this->entityManager->flush();

        return $plainTextNewPassword;
    }
} 