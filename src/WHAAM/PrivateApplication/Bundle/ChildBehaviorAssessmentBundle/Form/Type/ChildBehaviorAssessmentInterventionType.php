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
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface;

use WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Form\Type\ChildBehaviorAssessmentInterventionStrategyType,
    WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Form\Type\BaseChildBehaviorAssessmentPhaseType;

class ChildBehaviorAssessmentInterventionType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $child = $builder->getData()
            ->getChildBehaviorAssessmentBaseline()
            ->getChildBehaviorAssessment()
            ->getChildBehavior()
            ->getChild();

        $builder->add('base_phase', new BaseChildBehaviorAssessmentPhaseType(), array(
            'data_class' => 'WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentIntervention'
        ));

        $builder->add('strategies', 'collection', array(
                    'type' => new ChildBehaviorAssessmentInterventionStrategyType($child),
                    'label' => 'strategies.label',
                    'allow_add'    => true,
                    'allow_delete' => true,
                    'by_reference' => false
                )
            )
            ->add('countInterventionStrategies', 'hidden', array(
                    'mapped' => false
                )
            )
            ->add('removeTranslation', 'hidden', array(
                    'mapped' => false
                )
            )
            ->add('submit', 'submit', array(
                'label' => 'save.label'
            ))
        ;
    }

    public function getName()
    {
        return 'child_behavior_assessment_intervention';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'cascade_validation' => true
        ));
    }
} 