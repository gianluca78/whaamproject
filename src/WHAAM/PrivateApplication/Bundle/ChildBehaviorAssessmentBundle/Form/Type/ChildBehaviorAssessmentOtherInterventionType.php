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
namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Doctrine\ORM\EntityRepository,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\Form\FormEvents,
    Symfony\Component\Form\FormEvent;

use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Form\Type\BaseChildBehaviorAssessmentPhaseType;

class ChildBehaviorAssessmentOtherInterventionType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('base_phase', new BaseChildBehaviorAssessmentPhaseType(), array(
            'data_class' => 'WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentIntervention'
        ))
        ->add('submit', 'submit', array(
                'label' => 'Save'
            )
        );
    }

    public function getName()
    {
        return 'child_behavior_assessment_other_intervention';
    }
} 