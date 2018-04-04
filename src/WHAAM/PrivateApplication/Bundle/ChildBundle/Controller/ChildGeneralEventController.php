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
namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child;
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildGeneralEvent;

/**
 * @Route("/child-event")
 *
 * Class ChildGeneralEventController
 * @package WHAAM\PrivateApplication\Bundle\ChildBundle\Controller
 */
class ChildGeneralEventController extends Controller
{
    /**
     * @Route("/{id}/delete", name="child_general_event_delete", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $id)
    {
        $childGeneralEvent = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:ChildGeneralEvent')
            ->find($id);

        if (!$childGeneralEvent) {
            throw $this->createNotFoundException(
                'Child General event with id ' . $id . ' not found'
            );
        }

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $childGeneralEvent->getChild());
        $em = $this->getDoctrine()->getManager();

        try {
            $childGeneralEventDeleted = $childGeneralEvent;

            $em->remove($childGeneralEvent);
            $em->flush();
            $message = $this->get('translator')->trans('general_event.delete.success', array(), 'flash_messages');

            $this->get('session')->getFlashBag()->add('success', $message);

            $this->get('whaam_notification.util.notification_manager')->send(
                $childGeneralEventDeleted->getChild()->getChildUsers(),
                $this->get('translator')->trans('event_delete_title', array(), 'notification'),
                sprintf(
                    $this->get('translator')->trans('event_delete', array(), 'notification'),
                    (string) $childGeneralEventDeleted->getChild(),
                    (string) $this->getUser()
                ),
                null
            );

        } catch (Exception $e) {
            $message = $this->get('translator')->trans('general_event.delete.error', array(), 'flash_messages');

            $this->get('session')->getFlashBag()->add('error', $message);
        }

        return $this->redirect($this->generateUrl('case_data_list', array(
                'childSlug' => $childGeneralEvent->getChild()->getSlug()
            ))
        );
    }

    /**
     * @Route("/{id}/edit", name="child_general_event_edit", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $childGeneralEvent = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:ChildGeneralEvent')
            ->find($id);

        if (!$childGeneralEvent) {
            throw $this->createNotFoundException(
                'Child General event with id ' . $id . ' not found'
            );
        }

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $childGeneralEvent->getChild());

        $eventDescription = $childGeneralEvent->getDescription();
        $eventDescription = strlen($eventDescription)<10 ? $eventDescription : substr($eventDescription, 0, 10) . ' [...]';

        $this->get('whaam_breadcrumbs')->load('Child:ChildGeneralEvent:edit')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childGeneralEvent->getChild()->getSlug()),
                    'case_data' => array('childSlug' => $childGeneralEvent->getChild()->getSlug()),
                    '%other_event_description%' => array('id' => $childGeneralEvent->getId())
                ),
                array(
                    '%child_name%' => $childGeneralEvent->getChild()->getNickname(),
                    '%other_event_description%' => $eventDescription
                ));


        $form = $this->createForm('childGeneralEvent', $childGeneralEvent, array(
            'action' => $this->generateUrl('child_general_event_edit', array(
                        'id' => $id
                    )
                ),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));
        $formHandler = $this->get('whaam_child_event.form.handler.child_event_form_handler');

        if ($formHandler->handle($form, $request, 'general_event.edit.success')) {
            $this->get('whaam_notification.util.notification_manager')->send(
                $childGeneralEvent->getChild()->getChildUsers(),
                $this->get('translator')->trans('event_edit_title', array(), 'notification'),
                sprintf(
                    $this->get('translator')->trans('event_edit', array(), 'notification'),
                    (string) $childGeneralEvent->getChild(),
                    (string) $this->getUser()
                ),
                $this->generateUrl('child_general_event_view', array('id' => $childGeneralEvent->getId()))
            );

            return $this->redirect($this->generateUrl('case_data_list', array(
                    'childSlug' => $childGeneralEvent->getChild()->getSlug()
                ))
            );
        }

        return $this->render(
            'WHAAMPrivateApplicationChildBundle:ChildGeneralEvent:formPage.html.twig',
            array(
                'child' => $childGeneralEvent->getChild(),
                'childSlug' => $childGeneralEvent->getChild()->getSlug(),
                'form' => $form->createView(),
                'formTitle' => $this->get('translator')->trans('edit_other_event', array(), 'interface')
            )
        );
    }

    /**
     * @Route("/{childSlug}/new", name="child_general_event_new", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param $childSlug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request, $childSlug)
    {
        $child = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:Child')
            ->findOneBySlug($childSlug);

        if (!$child) {
            throw $this->createNotFoundException(
                'Child ' . $childSlug . ' not found'
            );
        }

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        $this->get('whaam_breadcrumbs')->load('Child:ChildGeneralEvent:new')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childSlug),
                    'case_data' => array('childSlug' => $childSlug),
                ),
                array(
                    '%child_name%' => $child->getNickname(),
                ));

        $childGeneralEvent = new ChildGeneralEvent();
        $childGeneralEvent->setChild($child);

        $form = $this->createForm('childGeneralEvent', $childGeneralEvent, array(
            'action' => $this->generateUrl('child_general_event_new', array(
                        'childSlug' => $childSlug
                    )
                ),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));
        $formHandler = $this->get('whaam_child_event.form.handler.child_event_form_handler');

        if ($childGeneralEvent = $formHandler->handle($form, $request, 'event.new.success')) {
            $this->get('whaam_notification.util.notification_manager')->send(
                $child->getChildUsers(),
                $this->get('translator')->trans('event_new_title', array(), 'notification'),
                sprintf(
                    $this->get('translator')->trans('event_new', array(), 'notification'),
                    (string) $child,
                    (string) $this->getUser()
                ),
                $this->generateUrl('child_general_event_view', array('id' => $childGeneralEvent->getId()))
            );

            return $this->redirect($this->generateUrl('case_data_list', array(
                    'childSlug' => $childSlug
                ))
            );
        }

        return $this->render(
            'WHAAMPrivateApplicationChildBundle:ChildGeneralEvent:formPage.html.twig',
            array(
                'child' => $child,
                'childSlug' => $childSlug,
                'form' => $form->createView(),
                'formTitle' => $this->get('translator')->trans('new_other_event', array(), 'interface')
            )
        );
    }

    /**
     * @Route("/{id}/view", name="child_general_event_view", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request, $id)
    {
        $childGeneralEvent = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:ChildGeneralEvent')
            ->find($id);

        if (!$childGeneralEvent) {
            throw $this->createNotFoundException(
                'Child General event with id ' . $id . ' not found'
            );
        }

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $childGeneralEvent->getChild());

        $eventDescription = $childGeneralEvent->getDescription();
        $eventDescription = strlen($eventDescription)<10 ? $eventDescription : substr($eventDescription, 0, 10) . ' [...]';


        $this->get('whaam_breadcrumbs')->load('Child:ChildGeneralEvent:view')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childGeneralEvent->getChild()->getSlug()),
                    'case_data' => array('childSlug' => $childGeneralEvent->getChild()->getSlug()),
                ),
                array(
                    '%child_name%' => $childGeneralEvent->getChild()->getNickname(),
                    '%other_event_description%' => $eventDescription
                ));


        return array(
            'childGeneralEvent' => $childGeneralEvent,
            'child' => $childGeneralEvent->getChild(),
            'childSlug' => $childGeneralEvent->getChild()->getSlug()
        );
    }
}
