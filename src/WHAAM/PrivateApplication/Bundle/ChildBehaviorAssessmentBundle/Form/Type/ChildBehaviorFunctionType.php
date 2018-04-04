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
namespace WHAAM\PrivateApplication\Bundle\ChildBehaviorAssessmentBundle\Form\Type;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface;

class ChildBehaviorFunctionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('behaviorWhen', null, array(
                    'label' => 'behavior_when.label',
                    'required' => true
                )
            )
            ->add('behaviorFunction', 'translatedEntity', array(
                    'label' => 'behavior_function.label',
                    'required' => true,
                    'empty_value' => 'behavior_function_empty_value.label',
                    'class' => 'WHAAMPrivateApplicationChildBehaviorAssessmentBundle:BehaviorFunction',
                    'query_builder' => function (EntityRepository $er) {
                            return $er->createQueryBuilder('f')
                                ->orderBy('f.behaviorFunction', 'ASC');
                        },
                    'attr' => array('class' => 'select-width')
                )
            )
            ->add('note', null, array(
                    'label' => 'note.label',
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
        return 'childBehaviorFunction';
    }
}