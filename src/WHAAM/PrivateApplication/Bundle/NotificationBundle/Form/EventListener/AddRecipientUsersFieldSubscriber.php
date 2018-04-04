<?php
namespace WHAAM\PrivateApplication\Bundle\NotificationBundle\Form\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddRecipientUsersFieldSubscriber implements EventSubscriberInterface{
    private $factory;
    private $entityManager;
    private $user;

    public function __construct(FormFactoryInterface $factory, EntityManager $entityManager, $user)
    {
        $this->factory = $factory;
        $this->entityManager = $entityManager;
        $this->user = $user;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit'
        );
    }

    private function addRecipientUsersField($form, $childId, $data)
    {
        $users = array();

        if($childId) {
            $child = $this->entityManager->getRepository('WHAAMPrivateApplicationChildBundle:Child')->find($childId);

            $userRepository = $this->entityManager->getRepository('WHAAMPrivateApplicationUserBundle:User');
            $users = $userRepository->findNetworkUsersByChildNickname($child->getNickname(), $this->user);
        }

        $form->add($this->factory->createNamed('recipientUsers','entity', $data, array(
            'label' => 'message_child_user_value.label',
            'class' => 'WHAAMPrivateApplicationUserBundle:User',
            'auto_initialize' => false,
            'choices' => $users,
            'multiple' => true,
            'expanded' => false
        )));
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }

        $child = ($data->getChild()) ? ($data->getChild()) : null;
        $users = ($data->getRecipientUsers()) ? ($data->getRecipientUsers()) : null;
        $this->addRecipientUsersField($form, $child, $users);
    }

    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }

        $child = array_key_exists('child', $data) ? $data['child'] : null;
        $users = array_key_exists('recipientUsers', $data) ? $data['recipientUsers'] : null;

        $this->addRecipientUsersField($form, $child, $users);
    }
} 