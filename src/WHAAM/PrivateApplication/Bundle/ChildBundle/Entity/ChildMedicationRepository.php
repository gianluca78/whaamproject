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

class ChildMedicationRepository extends EntityRepository
{
    public function findMedicationsByChildNickname($childNickname)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT m.name, m.dosage, m.frequency, m.startDate, m.endDate
                FROM WHAAMPrivateApplicationChildBundle:ChildMedication m
                JOIN m.child c
                WHERE c.nickname = :childNickname
                '
            )
            ->setParameter('childNickname', $childNickname)
            ->getArrayResult();
    }
}