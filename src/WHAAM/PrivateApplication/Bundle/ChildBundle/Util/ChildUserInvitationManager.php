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
use WHAAM\PrivateApplication\Bundle\ChildBundle\Util\ChildUserManager,
    WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUserInvitation,
    WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User;

class ChildUserInvitationManager {

    private $childUserManager;
    private $entityManager;

    public function __construct(EntityManager $entityManager, ChildUserManager $childUserManager)
    {
        $this->childUserManager = $childUserManager;
        $this->entityManager = $entityManager;
    }

    /**
     * Cancel an user pending invitation
     *
     * @param ChildUserInvitation $childUserInvitation
     */
    private function cancelPendingInvitation(ChildUserInvitation $childUserInvitation)
    {
        $childUserInvitation->setIsPending(0);
        $this->entityManager->persist($childUserInvitation);
        $this->entityManager->flush();
    }

    /**
     * Add a new registered user to the networks of children for who has been invited
     *
     * @param User $user
     */
    public function managePendingInvitations(User $user)
    {
        $childUserInvitations = $this->entityManager->getRepository('WHAAMPrivateApplicationChildBundle:ChildUserInvitation')
            ->findByEmail($user->getEmail());

        if($childUserInvitations) {
            foreach($childUserInvitations as $childUserInvitation) {
                $this->childUserManager->addUserToChildNetwork($childUserInvitation->getChild(), $user);
                $this->cancelPendingInvitation($childUserInvitation);
            }
        }
    }
}