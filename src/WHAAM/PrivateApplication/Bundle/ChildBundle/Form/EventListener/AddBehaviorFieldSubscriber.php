<?php
namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Form\EventListener;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class AddBehaviorFieldSubscriber implements EventSubscriberInterface{
    private $factory;
    private $entityManager;

    public function __construct(FormFactoryInterface $factory, EntityManager $entityManager)
    {
        $this->factory = $factory;
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit'
        );
    }

    private function addBehaviorField($form, $behaviorCategoryId, $behavior)
    {
        $behaviors = array();

        if($behaviorCategoryId) {
            $behaviorCategory = $this->entityManager
                ->getRepository('WHAAMPrivateApplicationChildBundle:BehaviorCategory')
                ->find($behaviorCategoryId);

            $behaviors = $behaviorCategory->getBehaviors();

        }

        $form->add($this->factory->createNamed('behavior','entity', $behavior, array(
            'label' => 'behavior.label',
            'class' => 'WHAAMPrivateApplicationChildBundle:Behavior',
            'empty_value' => 'behavior_empty_value.label',
            'auto_initialize' => false,
            'choices' => $behaviors,
            'multiple' => false,
            'expanded' => false,
            'attr' => array(
                'class' => 'select-width'
            )
        )));
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }

        $behaviorCategory = ($data->getBehaviorCategory()) ? ($data->getBehaviorCategory()) : null;
        $behavior = ($data->getBehavior()) ? ($data->getBehavior()) : null;

        $behaviorCategoryId = ($behaviorCategory) ? $behaviorCategory->getId() : null;

        $this->addBehaviorField($form, $behaviorCategoryId, $behavior);
    }

    public function preSubmit(FormEvent $event)
    {
        $data = $event->getData();
        $form = $event->getForm();

        if (null === $data) {
            return;
        }

        $behaviorCategoryId = array_key_exists('behaviorCategory', $data) ? $data['behaviorCategory'] : null;
        $behavior = array_key_exists('behavior', $data) ? $data['behavior'] : null;

        $this->addBehaviorField($form, $behaviorCategoryId, $behavior);
    }
} 