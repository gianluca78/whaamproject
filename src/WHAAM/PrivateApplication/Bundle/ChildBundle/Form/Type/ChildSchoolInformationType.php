<?php
/*
 * This file is part of the WHAAM project funded with support from the European Commission.
 *
 * Reference project number: 531244-LLP-2012-IT-KA3MP
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @author Giuseppe Chiazzese
 */
namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Form\Type;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface;

class ChildSchoolInformationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('year',null, array(
                'label' => 'school_date.label',
                'required' => true,
                'attr' => array(
                    'placeholder' => 'school_date.placeholder'
                )
            ))
            ->add('schoolName', null, array(
                    'label' => 'school_name.label'
                )
            )
            ->add('grade', null, array(
                    'label' => 'school_grade.label'
                )
            )
            ->add('hasSpecialNeedSupportTeacher', null, array(
                    'label' => 'has_special_need_support.label',
                    'required' => false
                )
            )
            ->add('hasIndividualEducationPlan', null, array(
                    'label' => 'has_iep.label',
                    'required' => false
                )
            )
             ->add('submit', 'submit', array(
                    'label' => 'save.label'
                )
            );
    }

    public function getName()
    {
        return 'childSchoolInformation';
    }
}