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
namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Util;

use Doctrine\ORM\EntityManager;
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child,
    WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUser,
    WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User;

class ChildUserManager {

    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Add an user to a child network
     *
     * @param Child $child
     * @param User $user
     */
    public function addUserToChildNetwork(Child $child, User $user)
    {
        $childUser = new ChildUser();
        $childUser->setChild($child);
        $childUser->setUser($user);
        $childUser->setIsApprovedByParent(1);

        $child->addChildUser($childUser);

        $this->entityManager->persist($child);
        $this->entityManager->flush();
    }
}