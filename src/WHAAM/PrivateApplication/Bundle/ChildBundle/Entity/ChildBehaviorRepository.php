<?php
/*
 * This file is part of the WHAAM project funded with support from the European Commission.
 *
 * Reference project number: 531244-LLP-2012-IT-KA3MP
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @author Gianluca Merlo Giuseppe Chiazzese
 */
namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ChildBehaviorRepository extends EntityRepository
{
    /**
     * Return child behaviors
     *
     * @param $nickname
     * @return array
     */
    public function findChildBehaviorsByNickname($nickname)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT cb.behavior, cb.description, cb.place, cb.setting, cb.createdAt, u.slug AS createdByUserSlug
                FROM WHAAMPrivateApplicationChildBundle:ChildBehavior cb
                JOIN cb.user u
                JOIN cb.child ch
                WHERE ch.nickname = :nickname
                ORDER BY cb.createdAt DESC
                '
            )
            ->setParameter('nickname', $nickname)
            ->getArrayResult();
    }

    public function findBehaviorsWithActiveAssessmentsByUserIdAndChildNickname($userId, $childNickname)
    {
        $today = new \DateTime();

        return $this->getEntityManager()
            ->createQuery(
                'SELECT b.id, beh.behavior, b.description, b.place, b.setting, b.otherBehavior
                FROM WHAAMPrivateApplicationChildBundle:ChildBehavior b
                LEFT JOIN b.behavior beh
                JOIN b.child ch
                JOIN b.childBehaviorAssessments cb
                JOIN cb.baselines bas
                LEFT JOIN bas.ABCUsers abcusers
                JOIN bas.observer obs
                LEFT JOIN bas.intervention i
                JOIN ch.childUsers cus
                JOIN cus.user cuser
                WHERE (
                    (bas.startDate <= :today AND bas.endDate >= :today)
                    OR
                    (i.startDate <= :today AND i.endDate >= :today)
                )
                AND (
                  obs.id = :userId
                  OR
                  abcusers.id = :userId
                )
                AND ch.nickname = :childNickname
                AND cuser.id = :userId
                AND cus.isApprovedByParent = 1
                GROUP BY b.id
                '
            )
            ->setParameter('today', $today->format('Y-m-d'))
            ->setParameter('userId', $userId)
            ->setParameter('childNickname', $childNickname)
            ->getResult();
    }
}