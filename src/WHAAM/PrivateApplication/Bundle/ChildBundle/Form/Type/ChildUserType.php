<?php
/*
 * This file is part of the WHAAM project funded with support from the European Commission.
 *
 * Reference project number: 531244-LLP-2012-IT-KA3MP
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @author GGiuseppe Chiazzese
 */
namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Form\Type;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface;

class ChildUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('role', 'translatedEntity', array(
                'label' => 'role.label',
                'empty_value' => 'role_empty_value.label',
                'class' => 'WHAAMPrivateApplicationChildBundle:ChildUserRole',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a');
                    }
                )
            )
            ->add('isAuthorizedToAccessData', null, array())
            ->add('submit', 'submit', array(
                    'label' => 'save.label'
                )
            );
    }

    public function getName()
    {
        return 'childUser';
    }
}