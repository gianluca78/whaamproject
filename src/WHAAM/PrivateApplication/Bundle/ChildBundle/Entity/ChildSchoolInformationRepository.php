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
namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Entity;

use Doctrine\ORM\EntityRepository;

class ChildSchoolInformationRepository extends EntityRepository
{
    public function findSchoolInformationByChildNickname($childNickname)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT s.year, s.schoolName, s.grade, s.hasSpecialNeedSupportTeacher, s.hasIndividualEducationPlan
                FROM WHAAMPrivateApplicationChildBundle:ChildSchoolInformation s
                JOIN s.child c
                WHERE c.nickname = :childNickname
                '
            )
            ->setParameter('childNickname', $childNickname)
            ->getArrayResult();
    }
}