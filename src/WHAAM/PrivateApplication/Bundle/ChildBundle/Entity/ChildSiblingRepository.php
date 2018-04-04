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

class ChildSiblingRepository extends EntityRepository
{
    public function findSiblingsByChildNicknameAndLocale($childNickname, $locale)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT cs.nickname, cs.name, cs.yearOfBirth, sexTable.sex
                FROM WHAAMPrivateApplicationChildBundle:ChildSibling cs
                JOIN cs.child c
                JOIN cs.sex sexTable
                WHERE c.nickname = :childNickname
                '
            )
            ->setParameter('childNickname', $childNickname)
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