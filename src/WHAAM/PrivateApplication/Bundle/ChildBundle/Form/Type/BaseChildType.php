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
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface;

use WHAAM\PrivateApplication\Bundle\ChildBundle\Form\Type\ChildSiblingType;

class BaseChildType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('surname', null, array(
                    'label' => 'surname.label'
                )
            )
            ->add('name', null, array(
                    'label' => 'name.label'
                )
            )
            ->add('sex', 'translatedEntity', array(
                    'label' => 'sex.label',
                    'empty_value' => 'sex_empty_value.label',
                    'class' => 'WHAAMPrivateApplicationUserBundle:Sex',
                    'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('s');
                        },
                    'required' => true
                )
            )
            ->add('yearOfBirth', 'choice', array(
                    'label' => 'year_of_birth.label',
                    'choices' => array_combine(range(1980, date('Y')-5, 1), range(1980, date('Y')-5, 1)),
                    'empty_value' => 'year_empty_value.label',
                    'required' => true
                )
            )
            ->add('siblings', 'collection', array(
                    'type' => new ChildSiblingType(),
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'by_reference' => false
                )
            )
            ->add('countChildSiblings', 'hidden', array(
                    'mapped' => false
                )
            )
            ->add('removeTranslation', 'hidden', array(
                    'mapped' => false
                )
            );

    }

    public function getName()
    {
        return 'base_child';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'cascade_validation' => true,
            'inherit_data' => true
        ));
    }
}