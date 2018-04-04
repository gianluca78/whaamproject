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
namespace WHAAM\PrivateApplication\Bundle\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Gedmo\Translatable\TranslatableListener;

class UserRepository extends EntityRepository
{
    public function findOneFilteredUserDataByUserIdAndLocale($userId, $locale)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT userTable.surname, userTable.name, userTable.dateOfBirth, userTable.username, userTable.email, userTable.isHealthProfessional, userTable.createdAt,
                nationTable.nation,
                sexTable.sex
                FROM WHAAMPrivateApplicationUserBundle:User userTable
                JOIN userTable.nation nationTable
                JOIN userTable.sex sexTable
                WHERE userTable.id = :userId
                '

            )
            ->setParameter('userId', $userId)
            ->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
                'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
            )
            ->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
                $locale
            )
            ->getOneOrNullResult();
    }

    public function findNetworkUsersByChildNickname($childNickname, $user)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT u
                FROM WHAAMPrivateApplicationUserBundle:User u
                JOIN u.childUsers cu
                JOIN cu.child as c
                WHERE c.nickname = :nickname
                AND u <> :user
                AND cu.isApprovedByParent = 1
                '
            )
            ->setParameter('nickname', $childNickname)
            ->setParameter('user', $user)
            ->getResult();
    }

}