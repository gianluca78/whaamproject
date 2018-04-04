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
namespace WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity;

use Doctrine\ORM\EntityRepository;

class NotificationRepository extends EntityRepository
{
    /**
     * Delete all notifications for a specific user
     *
     * @param $user
     * @return array Notification object
     */
    public function deleteNotificationsByUser($user)
    {
        return $this->getEntityManager()
            ->createQuery(
                'DELETE
                FROM WHAAMPrivateApplicationNotificationBundle:Notification n
                WHERE n.recipientUser = :user
                '
            )
            ->setParameter('user', $user)
            ->getResult();
    }

    /**
     * Return the notifications for a specific user
     *
     * @param $user
     * @return array Notification object
     */
    public function findNotificationsByUser($user)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT n
                FROM WHAAMPrivateApplicationNotificationBundle:Notification n
                WHERE n.recipientUser = :user
                ORDER BY n.createdAt DESC
                '
            )
            ->setParameter('user', $user)
            ->getResult();
    }

    /**
     * Return the not displayed notifications for a specific user
     *
     * @param $user
     * @return array Notification object
     */
    public function findNotDisplayedNotificationsByUser($user)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT n.id, n.title, n.content
                FROM WHAAMPrivateApplicationNotificationBundle:Notification n
                WHERE n.isDisplayed = 0
                AND n.recipientUser = :user
                ORDER BY n.createdAt DESC
                '
            )
            ->setParameter('user', $user)
            ->getResult();
    }

    /**
     * Return the not read notifications for a specific user
     *
     * @param $user
     * @return array Notification object
     */
    public function findNotReadNotificationsByUser($user)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT n.id, n.title, n.content
                FROM WHAAMPrivateApplicationNotificationBundle:Notification n
                WHERE n.isRead = 0
                AND n.recipientUser = :user
                ORDER BY n.createdAt DESC
                '
            )
            ->setParameter('user', $user)
            ->getResult();
    }

    /**
     * Update all notifications as read for a specific user
     *
     * @param $user
     * @return array Notification object
     */
    public function allReadNotificationsByUser($user)
    {
        return $this->getEntityManager()
            ->createQuery(
                'UPDATE WHAAMPrivateApplicationNotificationBundle:Notification n
                SET n.isRead = 1
                WHERE n.recipientUser = :user
                '
            )
            ->setParameter('user', $user)
            ->getResult();
    }

}