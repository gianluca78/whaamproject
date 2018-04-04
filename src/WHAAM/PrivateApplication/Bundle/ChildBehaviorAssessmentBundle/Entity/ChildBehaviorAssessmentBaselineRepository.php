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

class ChildBehaviorAssessmentBaselineRepository extends EntityRepository
{
    /**
     * Return an array of baseline observation data
     *
     * @param $baselineId
     * @return array
     */
    public function findObservationDataByPhaseId($baselineId)
    {
        $results = $this->getEntityManager()
            ->createQuery(
                'SELECT b, os, obs
                FROM WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentBaseline b
                LEFT JOIN b.observationSessions os
                LEFT JOIN os.observations obs
                WHERE b.id = :baselineId
                '
            )
            ->setParameter('baselineId', $baselineId)
            ->getOneOrNullResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);

        return $results;
    }
}