<?php
/*
 * This file is part of the WHAAM project funded with support from the European Commission.
 *
 * Reference project number: 531244-LLP-2012-IT-KA3MP
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 *
 * @author GGiuseppe Chiazzese
 */
namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Form\Type;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface;

class ChildUserInvitationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('email', null, array(
                    'label' => 'email.label'
                )
            )
            ->add('submit', 'submit', array(
                    'label' => 'send.label'
                )
            );
    }

    public function getName()
    {
        return 'childUserInvitation';
    }
}