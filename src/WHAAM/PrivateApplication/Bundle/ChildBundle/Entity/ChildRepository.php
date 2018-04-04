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
use Doctrine\ORM\Query;
use Gedmo\Translatable\TranslatableListener;

class ChildRepository extends EntityRepository
{
    /**
     * Return children associated to an user
     *
     * @param $slug
     * @param $locale
     * @return array Child object
     */
    public function findChildrenByUserSlug($slug, $locale)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT c, d, s, com, disc, discT, sex
                FROM WHAAMPrivateApplicationChildBundle:Child c
                JOIN c.sex sex
                JOIN c.childUsers cu
                JOIN cu.user u
                LEFT JOIN c.diagnoses d
                LEFT JOIN c.disciplineReferrals disc
                LEFT JOIN d.subtype s
                LEFT JOIN d.comorbidities com
                LEFT JOIN disc.disciplineReferralType discT
                WHERE u.slug = :slug
                ORDER BY c.nickname ASC
                '
            )
            ->setParameter('slug', $slug)
            ->setHint(
                \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
                'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
            )
            ->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
                $locale
            )
            ->getResult();
    }

    public function findApprovedChildrenByUserIdAndLocale($userId, $locale)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT
                childrenTable.nickname, childrenTable.surname, childrenTable.name, childrenTable.yearOfBirth,
                childSexTable.sex as childSex

                FROM WHAAMPrivateApplicationChildBundle:Child childrenTable

                JOIN childrenTable.sex childSexTable

                JOIN childrenTable.childUsers childUsersTable
                JOIN childUsersTable.user userTable

                WHERE userTable.id = :userId
                AND childUsersTable.user = userTable
                AND childUsersTable.isApprovedByParent = 1
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
            ->getArrayResult();
    }
}