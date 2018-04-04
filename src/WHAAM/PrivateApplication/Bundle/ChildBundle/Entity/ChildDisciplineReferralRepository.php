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

class ChildDisciplineReferralRepository extends EntityRepository
{
    public function findDisciplineReferralsByChildNicknameAndLocale($childNickname, $locale)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT cdr.motivation, cdr.date, drt.type
                FROM WHAAMPrivateApplicationChildBundle:ChildDisciplineReferral cdr
                JOIN cdr.child c
                JOIN cdr.disciplineReferralType drt
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