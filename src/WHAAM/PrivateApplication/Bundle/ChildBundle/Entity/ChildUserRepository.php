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

use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child,
    WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User;

class ChildUserRepository extends EntityRepository
{
    /**
     * Return a ChildUser or null
     *
     * @param Child $child
     * @param User $user
     * @param $isApproved
     * @return mixed ChildUser | null
     */
    public function findOneByChildUserParentApproval(Child $child, User $user, $isApproved)
    {
        return $this->getEntityManager()->createQueryBuilder()
            ->select('cu')
            ->from('WHAAMPrivateApplicationChildBundle:ChildUser', 'cu')
            ->where('cu.child = :child')
            ->andWhere('cu.user = :user')
            ->andWhere('cu.isApprovedByParent = :isApproved')
            ->setParameter(':child', $child)
            ->setParameter(':user', $user)
            ->setParameter(':isApproved', $isApproved)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    public function findUsersByChildNickname($childNickname)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT u.id, r.roleName as role
                FROM WHAAMPrivateApplicationChildBundle:ChildUser cu
                JOIN cu.user u
                JOIN cu.child c
                JOIN cu.role as r

                WHERE c.nickname = :nickname
                AND cu.isApprovedByParent = 1
                '
            )
            ->setParameter('nickname', $childNickname)
            ->getArrayResult();
    }
}