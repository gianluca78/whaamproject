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
namespace WHAAM\PrivateApplication\Common\Doctrine\Form\Type;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bridge\Doctrine\Form\Type\DoctrineType;

use WHAAM\PrivateApplication\Common\Doctrine\Form\ChoiceList\ORMQueryBuilderLoader;

class EntityType extends DoctrineType
{
    public function getLoader(ObjectManager $manager, $queryBuilder, $class)
    {
        return new ORMQueryBuilderLoader($queryBuilder, $manager, $class);
    }

    public function getName()
    {
        return 'translatedEntity';
    }
}