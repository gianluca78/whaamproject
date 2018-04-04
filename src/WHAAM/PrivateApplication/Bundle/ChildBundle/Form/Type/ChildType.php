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

use WHAAM\PrivateApplication\Bundle\ChildBundle\Form\Type\BaseChildType;

class ChildType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('nickname', null, array(
                'label' => 'nickname.label',
                'required' => true
            )
        )
            ->add('base_child', new BaseChildType(), array(
            'data_class' => 'WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child'
        ))
            ->add('submit', 'submit', array(
                    'label' => 'save.label'
                )
            );

    }

    public function getName()
    {
        return 'child';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'cascade_validation' => true
        ));
    }
}