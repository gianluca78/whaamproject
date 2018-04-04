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

class BehaviorRepository extends EntityRepository
{
    public function findBehaviorsByCategoryIdAndLocale($categoryId, $locale)
    {
        return $this->getEntityManager()
            ->createQuery(
                'SELECT b
                FROM WHAAMPrivateApplicationChildBundle:Behavior b
                INNER JOIN b.behaviorCategory bc
                WHERE bc.id = :categoryId
                ORDER BY b.behavior ASC
                '
            )
            ->setParameter('categoryId', $categoryId)
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
}