<?php
namespace WHAAM\PrivateApplication\Bundle\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints\NotBlank;

class UserPasswordEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            /*
            ->add('oldPassword', 'password', array(
            'constraints' => array(
                new UserPassword(array(
                        'message' => 'password_current.invalid',
                        'groups' => 'user-password-edit'
                    )
                ),
                new NotBlank(array(
                    'message' => 'not_blank',
                    'groups' => 'user-password-edit'
                ))
            ),
            'mapped' => false,
            'required' => true,
        ))*/
            ->add('password', 'repeated', array(
                'type' => 'password',
                'invalid_message' => 'password_repeat.invalid',
                'required' => true,
                'first_options' => array('label' => 'password.label'),
                'second_options' => array('label' => 'password_repeat.label'),
            ))
            ->add('save', 'submit', array(
                'label' => 'save.label'
            ));
    }

    public function getName()
    {
        return 'user_edit_password';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => array('user-password-edit'),
        ));
    }
}