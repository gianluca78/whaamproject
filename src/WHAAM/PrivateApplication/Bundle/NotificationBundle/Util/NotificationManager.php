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
namespace WHAAM\PrivateApplication\Bundle\NotificationBundle\Util;

use Doctrine\ORM\EntityManager;
use WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\Notification;

class NotificationManager {
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function send($users, $title, $content, $url)
    {
        foreach ($users as $user) {
            $notification = new Notification();
            $notification->setTitle($title);
            $notification->setContent($content);
            $notification->setRecipientUser($user->getUser());
            $notification->setUrl($url);

            $this->entityManager->persist($notification);
            $this->entityManager->flush();
        }
    }
} 