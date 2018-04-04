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
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildADHDDiagnosis;

/**
 * @Route("/child-diagnosis")
 *
 * Class ChildADHDDiagnosisController
 * @package WHAAM\PrivateApplication\Bundle\ChildBundle\Controller
 */
class ChildADHDDiagnosisController extends Controller
{
    /**
     * @Route("/{id}/delete", name="child_diagnosis_delete", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $id)
    {
        $childADHDDiagnosis = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:ChildADHDDiagnosis')
            ->find($id);

        if (!$childADHDDiagnosis) {
            throw $this->createNotFoundException(
                'Diagnosis with id ' . $id . ' not found'
            );
        }

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $childADHDDiagnosis->getChild());
        $em = $this->getDoctrine()->getManager();

        try {
            $deletedDiagnosis = $childADHDDiagnosis;

            $em->remove($childADHDDiagnosis);
            $em->flush();
            $message = $this->get('translator')->trans('diagnosis.delete.success', array(), 'flash_messages');

            $this->get('session')->getFlashBag()->add('success', $message);

            $this->get('whaam_notification.util.notification_manager')->send(
                $deletedDiagnosis->getChild()->getChildUsers(),
                $this->get('translator')->trans('diagnosis_delete_title', array(), 'notification'),
                sprintf(
                    $this->get('translator')->trans('diagnosis_delete', array(), 'notification'),
                    (string) $deletedDiagnosis->getChild(),
                    (string) $this->getUser()
                ),
                null
            );


        } catch (Exception $e) {
            $message = $this->get('translator')->trans('diagnosis.delete.error', array(), 'flash_messages');

            $this->get('session')->getFlashBag()->add('error', $message);
        }


        return $this->redirect($this->generateUrl('case_data_list', array(
                'childSlug' => $childADHDDiagnosis->getChild()->getSlug()
            ))
        );
    }

    /**
     * @Route("/{id}/edit", name="child_diagnosis_edit", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $childADHDDiagnosis = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:ChildADHDDiagnosis')
            ->find($id);

        if (!$childADHDDiagnosis) {
            throw $this->createNotFoundException(
                'Diagnosis with id ' . $id . ' not found'
            );
        }

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $childADHDDiagnosis->getChild());

        $this->get('whaam_breadcrumbs')->load('Child:ChildADHDDiagnosis:edit')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childADHDDiagnosis->getChild()->getSlug()),
                    'case_data' => array('childSlug' => $childADHDDiagnosis->getChild()->getSlug()),
                    '%diagnosis_name%' => array('id' => $childADHDDiagnosis->getId()),

                ),
                array(
                    '%child_name%' => $childADHDDiagnosis->getChild()->getNickname(),
                    '%diagnosis_name%' => (string) $childADHDDiagnosis->getSubtype(),

                ));

        $form = $this->createForm('childADHDDiagnosis', $childADHDDiagnosis, array(
            'action' => $this->generateUrl('child_diagnosis_edit', array(
                        'id' => $id
                    )
                ),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));
        $formHandler = $this->get('whaam_child_ADHDDiagnosis.form.handler.child_ADHDDiagnosis_form_handler');

        if ($diagnosis = $formHandler->handle($form, $request, 'diagnosis.edit.success')) {
            $this->get('whaam_notification.util.notification_manager')->send(
                $childADHDDiagnosis->getChild()->getChildUsers(),
                $this->get('translator')->trans('diagnosis_edit_title', array(), 'notification'),
                sprintf(
                    $this->get('translator')->trans('diagnosis_new', array(), 'notification'),
                    (string) $childADHDDiagnosis->getChild(),
                    (string) $this->getUser()
                ),
                $this->generateUrl('child_diagnosis_view', array('id' => $diagnosis->getId()))
            );

            return $this->redirect($this->generateUrl('case_data_list', array(
                    'childSlug' => $childADHDDiagnosis->getChild()->getSlug()
                ))
            );
        }

        return $this->render(
            'WHAAMPrivateApplicationChildBundle:ChildADHDDiagnosis:formPage.html.twig',
            array(
                'child' => $childADHDDiagnosis->getChild(),
                'childSlug' => $childADHDDiagnosis->getChild()->getSlug(),
                'form' => $form->createView(),
                'formTitle' => $this->get('translator')->trans('edit_adhd_diagnosis', array(), 'interface')
            )
        );
    }

    /**
     * @Route("/{childSlug}/new", name="child_diagnosis_new", schemes={"http", "https"})
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

        $this->get('whaam_breadcrumbs')->load('Child:ChildADHDDiagnosis:new')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childSlug),
                    'case_data' => array('childSlug' => $childSlug),
                ),
                array('%child_name%' => $child->getNickname()));

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        $childADHDDiagnosis = new ChildADHDDiagnosis();
        $childADHDDiagnosis->setChild($child);

        $form = $this->createForm('childADHDDiagnosis', $childADHDDiagnosis, array(
            'action' => $this->generateUrl('child_diagnosis_new', array(
                        'childSlug' => $childSlug
                    )
                ),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));
        $formHandler = $this->get('whaam_child_ADHDDiagnosis.form.handler.child_ADHDDiagnosis_form_handler');

        if ($diagnosis = $formHandler->handle($form, $request, 'diagnosis.new.success')) {
            $this->get('whaam_notification.util.notification_manager')->send(
                $child->getChildUsers(),
                $this->get('translator')->trans('diagnosis_new_title', array(), 'notification'),
                sprintf(
                    $this->get('translator')->trans('diagnosis_new', array(), 'notification'),
                    (string) $child,
                    (string) $this->getUser()
                ),
                $this->generateUrl('child_diagnosis_view', array('id' => $diagnosis->getId()))
            );

            return $this->redirect($this->generateUrl('case_data_list', array(
                    'childSlug' => $childSlug
                ))
            );
        }

        return $this->render(
            'WHAAMPrivateApplicationChildBundle:ChildADHDDiagnosis:formPage.html.twig',
            array(
                'child' => $child,
                'childSlug' => $childSlug,
                'form' => $form->createView(),
                'formTitle' => $this->get('translator')->trans('new_adhd_diagnosis', array(), 'interface')

            )
        );
    }

    /**
     * @Route("/{id}/view", name="child_diagnosis_view", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request, $id)
    {
        $childADHDDiagnosis = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:ChildADHDDiagnosis')
            ->find($id);

        if (!$childADHDDiagnosis) {
            throw $this->createNotFoundException(
                'Diagnosis with id ' . $id . ' not found'
            );
        }

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $childADHDDiagnosis->getChild());

        $this->get('whaam_breadcrumbs')->load('Child:ChildADHDDiagnosis:view')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childADHDDiagnosis->getChild()->getSlug()),
                    'case_data' => array('childSlug' => $childADHDDiagnosis->getChild()->getSlug()),
                ),
                array(
                    '%child_name%' => $childADHDDiagnosis->getChild()->getNickname(),
                    '%diagnosis_name%' => (string) $childADHDDiagnosis->getSubtype(),

                ));

        return array(
            'childADHDDiagnosis' => $childADHDDiagnosis,
            'child' => $childADHDDiagnosis->getChild(),
            'childSlug' => $childADHDDiagnosis->getChild()->getSlug()
        );
    }
}
