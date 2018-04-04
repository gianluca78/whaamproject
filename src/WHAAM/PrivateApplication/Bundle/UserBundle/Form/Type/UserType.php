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
namespace WHAAM\PrivateApplication\Bundle\UserBundle\Form\Type;

use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormInterface,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Validator\Constraints\True;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('base_user', new BaseUserType(), array(
            'data_class' => 'WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User'
        ))
            ->add('username', null, array(
                    'label' => 'username.label'
                )
            )
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'password_repeat.invalid',
                'required' => true,
                'first_options' => array('label' => 'password.label'),
                'second_options' => array('label' => 'password_repeat.label'),
            ))
            ->add('email', null, array(
                    'label' => 'email.label'
                )
            )
            ->add('terms_acceptance', 'checkbox', array(
                    'label' => 'terms_acceptance.label',
                    'mapped' => false,
                    'required' => true,
                    'constraints' => array(
                        new True(array(
                            'message' => 'terms_acceptance.invalid',
                            'groups' => array('global')
                        ))
                    ),
                )
            )
            ->add('submit', 'submit', array(
                    'label' => 'register.label'
                )
            );
    }

    public function getName()
    {
        return 'user';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => function(FormInterface $form) {
                $validation_groups = array('global');

                if($form->getData()->getIsHealthProfessional()) {
                    $validation_groups[] = 'health-professional-registration';
                }

                if($form->getData()->getNation() && $form->getData()->getNation()->getNation() == 'Other') {
                    $validation_groups[] = 'other-nation';
                }

                return $validation_groups;
            },
        ));
    }
}