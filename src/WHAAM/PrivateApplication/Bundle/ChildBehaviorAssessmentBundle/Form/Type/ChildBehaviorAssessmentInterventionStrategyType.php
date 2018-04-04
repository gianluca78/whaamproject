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
    Symfony\Component\Form\FormEvent,
    Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ChildBehaviorAssessmentInterventionStrategyType extends AbstractType {

    private $child;

    public function __construct($child)
    {
        $this->child = $child;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $child = $this->child;

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
                    'expanded' => true,
                    'multiple' => true,
                    'label' => 'assign_to.label',
                    'empty_value' => 'assign_to_empty_value.label'
                );

                $form->add('assignedUsers', null, $formOptions);
            }
        );

        $builder->add('name', null, array(
            'label' => 'name.label',
            'required' => true,
        ))
            ->add('description', 'textarea', array(
                'label' => 'description.label',
                'required' => true,
            ))
        ;
    }

    public function getName()
    {
        return 'child_behavior_assessment_intervention_strategy';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
                'data_class' => 'WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Entity\ChildBehaviorAssessmentInterventionStrategy'
            )
        );
    }
} 