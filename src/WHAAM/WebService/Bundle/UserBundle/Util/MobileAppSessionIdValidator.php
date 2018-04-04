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
namespace WHAAM\WebService\Bundle\UserBundle\Util;

use Doctrine\ORM\EntityManager;

class MobileAppSessionIdValidator {

    private $entityManager;
    private $loggedUser;

    public function __construct(EntityManager $entityManager) {
        $this->entityManager = $entityManager;
    }

    public function isValidSessionId($sessionId)
    {
        $this->loggedUser = $this->entityManager
            ->getRepository('WHAAMPrivateApplicationUserBundle:User')
            ->findOneByMobileAppSessionId($sessionId);

        return ($this->loggedUser) ? true : false;
    }

    public function getLoggedUser()
    {
        return $this->loggedUser;
    }
} 