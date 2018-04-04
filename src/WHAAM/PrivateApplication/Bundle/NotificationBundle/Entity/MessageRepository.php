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

class MessageRepository extends EntityRepository
{
    /**
     * Return the messages received by a specific user
     *
     * @param $user
     * @return array Notification object
     */
    public function findInboxByUser($user)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT m
                FROM WHAAMPrivateApplicationNotificationBundle:Message m
                JOIN m.recipientUsers r
                WHERE r = :user
                ORDER BY m.createdAt DESC
                '
            )
            ->setParameter('user', $user)
            ->getResult();
    }

    /**
     * Return the not displayed messages by a specific user
     *
     * @param $user
     * @return array
     */
    public function findNotDisplayedMessagesByUser($user)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT m.id, m.subject, a.id as answerId, u.surname, u.name, u.username,
                s.id as messageStatusId, ast.id as answerStatusId
                FROM WHAAMPrivateApplicationNotificationBundle:Message m
                JOIN m.statuses s
                JOIN m.sender u
                LEFT JOIN m.answers a
                LEFT JOIN a.statuses ast
                WHERE (s.user = :user AND s.isDisplayed = 0) OR
                (ast.user = :user AND ast.isDisplayed = 0)
                '
            )
            ->setParameter('user', $user)
            ->getResult();
    }

    /**
     * Return the not read messages by a specific user
     *
     * @param $user
     * @return array
     */
    public function findNotReadMessagesByUser($user)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT m
                FROM WHAAMPrivateApplicationNotificationBundle:Message m
                JOIN m.statuses s
                LEFT JOIN m.answers a
                LEFT JOIN a.statuses ast
                WHERE (s.user = :user AND s.isRead = 0) OR
                (ast.user = :user AND ast.isRead = 0)
                '
            )
            ->setParameter('user', $user)
            ->getResult();
    }

    /**
     * Return the messages sent by a specific user
     *
     * @param $user
     * @return array Notification object
     */
    public function findSentByUser($user)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT m
                FROM WHAAMPrivateApplicationNotificationBundle:Message m
                WHERE m.sender = :user
                ORDER BY m.createdAt DESC
                '
            )
            ->setParameter('user', $user)
            ->getResult();
    }
}