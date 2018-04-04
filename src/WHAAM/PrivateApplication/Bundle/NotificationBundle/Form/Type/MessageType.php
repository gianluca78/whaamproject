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
namespace WHAAM\PrivateApplication\Bundle\NotificationBundle\Form\Type;

use Doctrine\ORM\EntityRepository;

use Symfony\Component\Form\AbstractType,
    Symfony\Component\Form\FormBuilderInterface,
    Symfony\Component\OptionsResolver\OptionsResolverInterface,
    Doctrine\ORM\EntityManager,
    WHAAM\PrivateApplication\Bundle\NotificationBundle\Form\EventListener\AddChildFieldSubscriber,
    WHAAM\PrivateApplication\Bundle\NotificationBundle\Form\EventListener\AddRecipientUsersFieldSubscriber,
    Symfony\Component\Security\Core\SecurityContext;

use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormEvent;

use WHAAM\PrivateApplication\Bundle\NotificationBundle\Form\Type\BaseMessageType;

class MessageType extends AbstractType
{
    private $entityManager;
    private $securityContext;

    public function __construct(EntityManager $entityManager, SecurityContext $securityContext)
    {
        $this->entityManager = $entityManager;
        $this->securityContext = $securityContext;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $user = $this->securityContext->getToken()->getUser();

        $builder->add('base_message', new BaseMessageType(), array(
                'data_class' => 'WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\Message'
            ))
            ->add('subject', null, array(
                    'label' => 'message_subject.label',
                    'required' => true
                )
            )
            ->add('child', 'entity', array(
                    'label' => 'message_child.label',
                    'class' => 'WHAAMPrivateApplicationChildBundle:Child',
                    'empty_value' => 'message_child_empty_value.label',
                    'auto_initialize' => false,
                    'query_builder' => function (EntityRepository $er) use ($user) {
                        return $er->createQueryBuilder('c')
                            ->join('c.childUsers', 'cu')
                            ->where('cu.user = :user')
                            ->setParameter('user', $user)
                            ->orderBy('c.nickname', 'ASC');
                    }
            ))
            ->addEventSubscriber(new AddRecipientUsersFieldSubscriber(
                $builder->getFormFactory(),
                $this->entityManager,
                $this->securityContext->getToken()->getUser())
            )
            ->add('submit', 'submit', array(
                    'label' => 'save.label'
                )
            );
    }

    public function getName()
    {
        return 'message';
    }

    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'cascade_validation' => true
        ));
    }
}