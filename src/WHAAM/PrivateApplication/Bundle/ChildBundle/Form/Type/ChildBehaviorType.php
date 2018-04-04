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
use Doctrine\ORM\EntityManager;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

use WHAAM\PrivateApplication\Bundle\ChildBundle\Form\EventListener\AddBehaviorFieldSubscriber;

class ChildBehaviorType extends AbstractType
{
    private $entityManager;

    public function __construct(EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addEventSubscriber(new AddBehaviorFieldSubscriber($builder->getFormFactory(), $this->entityManager))
            ->add('behaviorCategory', 'translatedEntity', array(
                    'label' => 'behavior_category.label',
                    'empty_value' => 'behavior_category_empty_value.label',
                    'class' => 'WHAAMPrivateApplicationChildBundle:BehaviorCategory',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('b');
                    },
                    'required' => true,
                    'attr' => array(
                        'class' => 'select-width'
                    )
                )
            )
            ->add('hasOtherBehavior', null, array())
            ->add('otherBehavior', 'textarea', array(
                    'label' => 'behavior.label',
                    'required' => true
                )
            )
            ->add('description', 'textarea', array(
                    'label' => 'description.label',
                    'required' => true
                )
            )
            ->add('place', 'textarea', array(
                    'label' => 'place.label',
                    'required' => true
                )
            )
            ->add('setting', 'textarea', array(
                    'label' => 'setting.label',
                    'required' => true
                )
            )
            ->add('submit', 'submit', array(
                    'label' => 'save.label'
                )
            );

    }

    public function getName()
    {
        return 'childBehavior';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'cascade_validation' => true,
            'validation_groups' => function(FormInterface $form) {
                $validation_groups = array('Default');

                if($form->getData()->getHasOtherBehavior() === TRUE) {
                    $validation_groups[] = 'not-vocabulary';
                } else {
                    $validation_groups[] = 'vocabulary';
                }

                return $validation_groups;
            },
        ));
    }

}