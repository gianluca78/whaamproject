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

class ChildBehaviorAssessmentABCRepository extends EntityRepository
{
    /**
     * Return an array of ChildBehaviorAssessmentABC data for a specific baseline
     *
     * @param $baseline
     * @return array
     */
    public function findByBaseline($baseline)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT abc, us.name AS abcCreatorName, us.surname AS abcCreatorSurname, us.username AS abcCreatorUsername
                FROM WHAAMPrivateApplicationChildBehaviorAssessmentBundle:ChildBehaviorAssessmentABC abc
                JOIN abc.user us
                WHERE abc.childBehaviorAssessmentBaseline = :baseline
                '
            )
            ->setParameter('baseline', $baseline)
            ->getResult(\Doctrine\ORM\Query::HYDRATE_ARRAY);
    }
}