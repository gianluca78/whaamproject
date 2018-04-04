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
namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Gedmo\Translatable\TranslatableListener;

class ChildBehaviorAssessmentRepository extends EntityRepository
{
    /**
     * Return an array of ChildBehaviorAssessment data
     * for specific user and child when user is observer or allowed to take abcs,
     * is approved by a parent and current date is between the baseline
     * or intervention start and end date
     *
     * @param $userId
     * @param $childNickname
     * @return array
     */
    public function findActiveAssessmentsByUserIdAndChildNickname($userId, $childNickname)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT cb
                FROM WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessment cb
                JOIN cb.baselines bas
                LEFT JOIN bas.ABCUsers abcusers
                JOIN bas.observer obs
                LEFT JOIN bas.intervention i
                JOIN cb.childBehavior b
                JOIN b.child ch
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
                '
            )
            ->setParameter('today', new \DateTime())
            ->setParameter('userId', $userId)
            ->setParameter('childNickname', $childNickname)
            ->getResult();
    }

    public function findBaselineOrInterventionInDateRange($assessmentId, $startDate, $endDate, $baselineId=null, $interventionId=null)
    {
        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select('cb')
            ->from('WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessment', 'cb')
            ->join('cb.baselines', 'bas')
            ->leftJoin('bas.intervention', 'i')
            ->where('cb.id = :assessmentId')
            ->andWhere(
                '(
                    (:startDate BETWEEN bas.startDate AND bas.endDate)
                    OR
                    (:endDate BETWEEN bas.startDate AND bas.endDate)
                    OR
                    (:startDate BETWEEN i.startDate AND i.endDate)
                    OR
                    (:endDate BETWEEN i.startDate AND i.endDate)
                )'
            )
            ->setParameter('assessmentId', $assessmentId)
            ->setParameter('startDate', $startDate)
            ->setParameter('endDate', $endDate);

        if($baselineId) {
            $qb->andWhere('bas.id <> :baselineId');
            $qb->setParameter('baselineId', $baselineId);
        }

        if($interventionId) {
            $qb->andWhere('i.id <> :interventionId');
            $qb->setParameter('interventionId', $interventionId);
        }

        return $qb->getQuery()->getOneOrNullResult();
    }
}