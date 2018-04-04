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

class ChildADHDDiagnosisRepository extends EntityRepository
{
    public function findComorbiditiesByIdAndLocale($id, $locale)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT dc.comorbidity
                FROM WHAAMPrivateApplicationChildBundle:ChildADHDDiagnosis d
                JOIN d.child c
                JOIN d.subtype ds
                JOIN d.comorbidities dc
                WHERE d.id = :id
                '
            )
            ->setParameter('id', $id)
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

    public function findDiagnosesByChildNicknameAndLocale($childNickname, $locale)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT d.id, d.diagnosisDate, d.onsetAge, d.isSecondaryDisorder, ds.subtype
                FROM WHAAMPrivateApplicationChildBundle:ChildADHDDiagnosis d
                JOIN d.child c
                JOIN d.subtype ds
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