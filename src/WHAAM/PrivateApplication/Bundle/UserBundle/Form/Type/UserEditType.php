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
use WHAAM\PrivateApplication\Bundle\UserBundle\Form\Type\BaseUserType;

class UserEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
            $builder->add('base_user', new BaseUserType(), array(
                'data_class' => 'WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User'
            ))
            ->add('submit', 'submit', array(
                    'label' => 'save.label'
                )
            );
    }

    public function getName()
    {
        return 'user_edit';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'validation_groups' => function(FormInterface $form) {
                $validation_groups = array('edit');

                if($form->getData()->getIsHealthProfessional()) {
                    $validation_groups[] = 'health-professional-registration';
                }

                return $validation_groups;
            },
        ));
    }
}