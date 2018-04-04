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

class ChildBehaviorAssessmentBaselineType extends AbstractType {

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $child = $builder->getData()->getChildBehaviorAssessment()->getChildBehavior()->getChild();

        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) use ($child) {
                $form = $event->getForm();

                $formOptions = array(
                    'class' => 'WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User',
                    'property' => 'username',
                    'query_builder' => function (EntityRepository $er) use ($child) {
                        return $er->createQueryBuilder('u')
                            ->join('u.childUsers', 'cu')
                            ->where('cu.child = :child')
                            ->orderBy('u.username', 'ASC')
                            ->setParameter('child', $child);
                    },
                    'required' => true,
                );

                $formOptionsABCUsers = $formOptions;
                $formOptionsABCUsers['expanded'] = true;
                $formOptionsABCUsers['multiple'] = true;
                $formOptionsABCUsers['label'] = 'abc_users.label';

                $formOptionsObserver = $formOptions;
                $formOptionsObserver['expanded'] = false;
                $formOptionsObserver['multiple'] = false;
                $formOptionsObserver['label'] = 'observer.label';
                $formOptionsObserver['empty_value'] = 'observer_empty_value.label';

                $form->add('observer', null, $formOptionsObserver)
                    ->add('ABCUsers', null, $formOptionsABCUsers)
                    ->add('submit', 'submit', array(
                        'label' => 'save.label'
                    ))
                ;
            }
        );

        $builder->add('base_phase', new BaseChildBehaviorAssessmentPhaseType(), array(
            'data_class' => 'WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentBaseline'
        ));

        $builder->add('minimumNumberOfObservations', null, array(
                'label' => 'minimum_number_of_observations.label',
                'required' => true,
            ))
            ->add('observationLength', null, array(
                'label' => 'observation_length.label',
                'required' => true,
            ))
            ->add('observationType', 'choice', array(
                'choices' => array('duration' => 'observation_type_duration.option', 'frequency' => 'observation_type_frequency.option'),
                'empty_value' => 'observation_type_empty_value.label',
                'required' => true,
                'label' => 'observation_type.label'
            ))
        ;
    }

    public function getName()
    {
        return 'child_behavior_assessment_baseline';
    }
} 