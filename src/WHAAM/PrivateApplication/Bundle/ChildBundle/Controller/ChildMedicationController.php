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
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildMedication;

/**
 * @Route("/child-medication")
 *
 * Class ChildMedicationController
 * @package WHAAM\PrivateApplication\Bundle\ChildBundle\Controller
 */
class ChildMedicationController extends Controller
{
    /**
     * @Route("/{id}/delete", name="child_medication_delete", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $id)
    {
        $childMedication = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:ChildMedication')
            ->find($id);

        if (!$childMedication) {
            throw $this->createNotFoundException(
                'Child medication with id ' . $id . ' not found'
            );
        }

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $childMedication->getChild());
        $em = $this->getDoctrine()->getManager();

        try {
            $childMedicationDeleted = $childMedication;

            $em->remove($childMedication);
            $em->flush();
            $message = $this->get('translator')->trans('medication.delete.success', array(), 'flash_messages');

            $this->get('session')->getFlashBag()->add('success', $message);

            $this->get('whaam_notification.util.notification_manager')->send(
                $childMedicationDeleted->getChild()->getChildUsers(),
                $this->get('translator')->trans('medication_delete_title', array(), 'notification'),
                sprintf(
                    $this->get('translator')->trans('medication_delete', array(), 'notification'),
                    (string) $childMedicationDeleted->getChild(),
                    (string) $this->getUser()
                ),
                $this->generateUrl('child_medication_view', array('id' => $id))
            );


        } catch (Exception $e) {
            $message = $this->get('translator')->trans('medication.delete.error', array(), 'flash_messages');

            $this->get('session')->getFlashBag()->add('error', $message);
        }


        return $this->redirect($this->generateUrl('case_data_list', array(
                'childSlug' => $childMedication->getChild()->getSlug()
            ))
        );
    }

    /**
     * @Route("/{id}/edit", name="child_medication_edit", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $childMedication = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:ChildMedication')
            ->find($id);

        if (!$childMedication) {
            throw $this->createNotFoundException(
                'Child medication with id ' . $id . ' not found'
            );
        }

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $childMedication->getChild());

        $this->get('whaam_breadcrumbs')->load('Child:ChildMedication:edit')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childMedication->getChild()->getSlug()),
                    'case_data' => array('childSlug' => $childMedication->getChild()->getSlug()),
                    '%medication_name%' => array('id' => $childMedication->getId())
                ),
                array(
                    '%child_name%' => $childMedication->getChild()->getNickname(),
                    '%medication_name%' => (string) $childMedication->getName(),

                ));

        $form = $this->createForm('childMedication', $childMedication, array(
            'action' => $this->generateUrl('child_medication_edit', array(
                        'id' => $id
                    )
                ),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));
        $formHandler = $this->get('whaam_child_medication.form.handler.child_medication_form_handler');

        if ($formHandler->handle($form, $request, 'medication.edit.success')) {
            $this->get('whaam_notification.util.notification_manager')->send(
                $childMedication->getChild()->getChildUsers(),
                $this->get('translator')->trans('medication_edit_title', array(), 'notification'),
                sprintf(
                    $this->get('translator')->trans('medication_edit', array(), 'notification'),
                    (string) $childMedication->getChild(),
                    (string) $this->getUser()
                ),
                $this->generateUrl('child_medication_view', array('id' => $childMedication->getId()))
            );

            return $this->redirect($this->generateUrl('case_data_list', array(
                    'childSlug' => $childMedication->getChild()->getSlug()
                ))
            );
        }

        return $this->render(
            'WHAAMPrivateApplicationChildBundle:ChildMedication:formPage.html.twig',
            array(
                'child' => $childMedication->getChild(),
                'childSlug' => $childMedication->getChild()->getSlug(),
                'form' => $form->createView(),
                'formTitle' => $this->get('translator')->trans('edit_medication', array(), 'interface')
            )
        );
    }

    /**
     * @Route("/{childSlug}/new", name="child_medication_new", schemes={"http", "https"})
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

        $this->get('whaam_breadcrumbs')->load('Child:ChildMedication:new')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childSlug),
                    'case_data' => array('childSlug' => $childSlug),
                ),
                array(
                    '%child_name%' => $child->getNickname(),
                ));

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        $childMedication = new ChildMedication();
        $childMedication->setChild($child);

        $form = $this->createForm('childMedication', $childMedication, array(
            'action' => $this->generateUrl('child_medication_new', array(
                        'childSlug' => $childSlug
                    )
                ),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));
        $formHandler = $this->get('whaam_child_medication.form.handler.child_medication_form_handler');

        if ($childMedication = $formHandler->handle($form, $request, 'medication.new.success')) {

            $this->get('whaam_notification.util.notification_manager')->send(
                $child->getChildUsers(),
                $this->get('translator')->trans('medication_new_title', array(), 'notification'),
                sprintf(
                    $this->get('translator')->trans('medication_new', array(), 'notification'),
                    (string) $child,
                    (string) $this->getUser()
                ),
                $this->generateUrl('child_medication_view', array('id' => $childMedication->getId()))
            );

            return $this->redirect($this->generateUrl('case_data_list', array(
                    'childSlug' => $childSlug
                ))
            );
        }

        return $this->render(
            'WHAAMPrivateApplicationChildBundle:ChildMedication:formPage.html.twig',
            array(
                'child' => $child,
                'childSlug' => $childSlug,
                'form' => $form->createView(),
                'formTitle' => $this->get('translator')->trans('new_medication', array(), 'interface')
            )
        );
    }

    /**
     * @Route("/{id}/view", name="child_medication_view", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request, $id)
    {
        $childMedication = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:ChildMedication')
            ->find($id);

        if (!$childMedication) {
            throw $this->createNotFoundException(
                'Child medication with id ' . $id . ' not found'
            );
        }

        $this->get('whaam_breadcrumbs')->load('Child:ChildMedication:view')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childMedication->getChild()->getSlug()),
                    'case_data' => array('childSlug' => $childMedication->getChild()->getSlug()),
                ),
                array(
                    '%child_name%' => $childMedication->getChild()->getNickname(),
                    '%medication_name%' => (string) $childMedication->getName(),
                ));

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $childMedication->getChild());


        return array(
            'childMedication' => $childMedication,
            'child' => $childMedication->getChild(),
            'childSlug' => $childMedication->getChild()->getSlug()
        );
    }
}
