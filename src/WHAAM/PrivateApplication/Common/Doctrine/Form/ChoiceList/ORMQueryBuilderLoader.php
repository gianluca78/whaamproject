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
namespace WHAAM\PrivateApplication\Common\Doctrine\Form\ChoiceList;

use Symfony\Bridge\Doctrine\Form\ChoiceList\ORMQueryBuilderLoader as BaseORMQueryBuilderLoader;

class ORMQueryBuilderLoader extends BaseORMQueryBuilderLoader
{
    private $queryBuilder;

    public function __construct($queryBuilder, $manager = null, $class = null)
    {
        parent::__construct($queryBuilder, $manager, $class);

        $this->queryBuilder = $queryBuilder($manager->getRepository($class));
    }

    public function getEntities()
    {
        $query = $this->queryBuilder->getQuery();

        $query->setHint(
            \Doctrine\ORM\Query::HINT_CUSTOM_OUTPUT_WALKER,
            'Gedmo\\Translatable\\Query\\TreeWalker\\TranslationWalker'
        )
            ->setHint(
                \Gedmo\Translatable\TranslatableListener::HINT_TRANSLATABLE_LOCALE,
                \Locale::getDefault()
            )
        ;

        return $query->execute();
    }
}