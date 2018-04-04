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
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildBehavior;

/**
 * @Route("/child-behaviors")
 *
 * Class ChildBehaviorController
 * @package WHAAM\PrivateApplication\Bundle\ChildBundle\Controller
 */
class ChildBehaviorController extends Controller
{
    /**
     * @Route("/{behaviorId}/delete", name="behavior_delete", schemes={"http", "https"})
     * @Method({"GET"})
     *
     * @param Request $request
     * @param $behaviorId
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */
    public function deleteAction(Request $request, $behaviorId)
    {
        $behavior = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:ChildBehavior')
            ->find($behaviorId);

        $em = $this->getDoctrine()->getManager();

        if (!$behavior) {
            throw $this->createNotFoundException(
                'Behavior with id ' . $behaviorId . ' not found'
            );
        }

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $behavior->getChild());

        try {
            $behaviorDeleted = $behavior;

            $em->remove($behavior);
            $em->flush();
            $message = $this->get('translator')->trans('behavior.delete.success', array(), 'flash_messages');

            $this->get('session')->getFlashBag()->add('success', $message);

            $this->get('whaam_notification.util.notification_manager')->send(
                $behaviorDeleted->getChild()->getChildUsers(),
                $this->get('translator')->trans('behavior_delete_title', array(), 'notification'),
                sprintf(
                    $this->get('translator')->trans('behavior_delete', array(), 'notification'),
                    (string) $behaviorDeleted->getChild(),
                    (string) $this->getUser()
                ),
                null
            );

        } catch (Exception $e) {
            $message = $this->get('translator')->trans('behavior.delete.error', array(), 'flash_messages');

            $this->get('session')->getFlashBag()->add('error', $message);
        }

        return $this->redirect($this->generateUrl('behaviors_list', array(
                'childSlug' => $behavior->getChild()->getSlug()
            ))
        );
    }

    /**
     * @Route("/{behaviorId}/edit", name="behavior_edit", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     * @Template
     *
     * @param Request $request
     * @param $behaviorId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $behaviorId)
    {
        $childBehavior = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:ChildBehavior')
            ->find($behaviorId);

        if (!$childBehavior) {
            throw $this->createNotFoundException(
                'Child behavior with id ' . $behaviorId . ' not found'
            );
        }

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $childBehavior->getChild());

        $behaviorName = (string) $childBehavior;
        $behaviorName = strlen($behaviorName) < 10 ? $behaviorName : substr($behaviorName, 0, 10) . ' [...]';

        $this->get('whaam_breadcrumbs')->load('Child:ChildBehavior:edit')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childBehavior->getChild()->getSlug()),
                    'behaviours' => array('childSlug' => $childBehavior->getChild()->getSlug()),
                    '%behaviour_name%' => array('behaviorId' => $behaviorId)
                ),
                array(
                    '%child_name%' => $childBehavior->getChild()->getNickname(),
                    '%behaviour_name%' => $behaviorName
                ));

        $form = $this->createForm('childBehavior', $childBehavior, array(
            'action' => $this->generateUrl('behavior_edit', array(
                        'behaviorId' => $behaviorId
                    )
                )
        ));
        $formHandler = $this->get('whaam_child_behavior.form.handler.child_behavior_form_handler');

        if ($formHandler->handle($form, $request, 'behavior.edit.success')) {
            $this->get('whaam_notification.util.notification_manager')->send(
                $childBehavior->getChild()->getChildUsers(),
                $this->get('translator')->trans('behavior_edit_title', array(), 'notification'),
                sprintf(
                    $this->get('translator')->trans('behavior_edit', array(), 'notification'),
                    (string) $childBehavior->getChild(),
                    (string) $this->getUser()
                ),
                $this->generateUrl('behavior_view', array('behaviorId' => $childBehavior->getId()))
            );

            return $this->redirect($this->generateUrl('behaviors_list', array(
                    'childSlug' => $childBehavior->getChild()->getSlug()
                )
            ));
        }

        return $this->render(
            'WHAAMPrivateApplicationChildBundle:ChildBehavior:formPage.html.twig',
            array(
                'child' => $childBehavior->getChild(),
                'childSlug' => $childBehavior->getChild()->getSlug(),
                'form' => $form->createView(),
                'formTitle' => $this->get('translator')->trans('edit_behavior', array(), 'interface')
            )
        );
    }

    /**
     * @Route("/{childSlug}", name="behaviors_list", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $childSlug)
    {
        $child = $this->getDoctrine()->
            getRepository('WHAAMPrivateApplicationChildBundle:Child')
            ->findOneBySlug($childSlug);

        if (!$child) {
            throw $this->createNotFoundException(
                'Child with slug ' . $childSlug . ' not found'
            );
        }

        $this->get('whaam_breadcrumbs')->load('Child:ChildBehavior:index')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childSlug),
                    'behaviours' => array('childSlug' => $childSlug),
                ),
                array(
                    '%child_name%' => $child->getNickname(),
                ));

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        return array(
            'behaviors' => $child->getBehaviors(),
            'child' => $child,
            'childSlug' => $childSlug
        );
    }

    /**
     * @Route("/{childSlug}/new", name="child_behavior_new", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     * @Template
     *
     * @param Request $request
     * @param $slug
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

        $this->get('whaam_breadcrumbs')->load('Child:ChildBehavior:new')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childSlug),
                    'behaviours' => array('childSlug' => $childSlug),
                ),
                array(
                    '%child_name%' => $child->getNickname(),
                ));

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        $childBehavior = new ChildBehavior();
        $childBehavior->setChildBehaviorCreatorUser($this->getUser());
        $childBehavior->setChild($child);

        $form = $this->createForm('childBehavior', $childBehavior, array(
            'action' => $this->generateUrl('child_behavior_new', array(
                        'childSlug' => $childSlug
                    )
                ),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));
        $formHandler = $this->get('whaam_child_behavior.form.handler.child_behavior_form_handler');

        if ($childBehavior = $formHandler->handle($form, $request, 'behavior.new.success')) {
            $this->get('whaam_notification.util.notification_manager')->send(
                $child->getChildUsers(),
                $this->get('translator')->trans('behavior_new_title', array(), 'notification'),
                sprintf(
                    $this->get('translator')->trans('behavior_new', array(), 'notification'),
                    (string) $child,
                    (string) $this->getUser()
                ),
                $this->generateUrl('behavior_view', array('behaviorId' => $childBehavior->getId()))
            );

            return $this->redirect($this->generateUrl('behaviors_list', array(
                    'childSlug' => $childSlug
                ))
            );
        }

        return $this->render(
            'WHAAMPrivateApplicationChildBundle:ChildBehavior:formPage.html.twig',
            array(
                'child' => $child,
                'childSlug' => $childSlug,
                'form' => $form->createView(),
                'formTitle' => $this->get('translator')->trans('new_behavior', array(), 'interface')
            )
        );
    }

    /**
     * @Route("/{behaviorId}/view", name="behavior_view", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @param $behaviorId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request, $behaviorId)
    {
        $childBehavior = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:ChildBehavior')
            ->find($behaviorId);

        if (!$childBehavior) {
            throw $this->createNotFoundException(
                'Child behavior with id ' . $behaviorId . ' not found'
            );
        }

        $behaviorName = (string) $childBehavior;
        $behaviorName = strlen($behaviorName)<10 ? $behaviorName : substr($behaviorName, 0, 10) . ' [...]';

        $this->get('whaam_breadcrumbs')->load('Child:ChildBehavior:view')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childBehavior->getChild()->getSlug()),
                    'behaviours' => array('childSlug' => $childBehavior->getChild()->getSlug()),
                    '%behaviour_name%' => array('behaviorId' => $behaviorId)
                ),
                array(
                    '%child_name%' => $childBehavior->getChild()->getNickname(),
                    '%behaviour_name%' => $behaviorName
                ));

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $childBehavior->getChild());

        return array(
            'childBehavior' => $childBehavior,
            'childSlug' => $childBehavior->getChild()->getSlug(),
            'child' => $childBehavior->getChild()
        );
    }
}
