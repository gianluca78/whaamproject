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
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildSchoolInformation;

/**
 * @Route("/child-school-information")
 *
 * Class ChildSchoolInformationController
 * @package WHAAM\PrivateApplication\Bundle\ChildBundle\Controller
 */
class ChildSchoolInformationController extends Controller
{
    /**
     * @Route("/{id}/delete", name="child_school_information_delete", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $id)
    {
        $childSchoolInformation = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:ChildSchoolInformation')
            ->find($id);

        if (!$childSchoolInformation) {
            throw $this->createNotFoundException(
                'School information with id ' . $id . ' not found'
            );
        }

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $childSchoolInformation->getChild());
        $em = $this->getDoctrine()->getManager();

        try {
            $childSchoolInformationDeleted = $childSchoolInformation;

            $em->remove($childSchoolInformation);
            $em->flush();
            $message = $this->get('translator')->trans('school_information.delete.success', array(), 'flash_messages');

            $this->get('session')->getFlashBag()->add('success', $message);

            $this->get('whaam_notification.util.notification_manager')->send(
                $childSchoolInformationDeleted->getChild()->getChildUsers(),
                $this->get('translator')->trans('school_information_delete_title', array(), 'notification'),
                sprintf(
                    $this->get('translator')->trans('school_information_delete', array(), 'notification'),
                    (string) $childSchoolInformationDeleted->getChild(),
                    (string) $this->getUser()
                ),
                null
            );

        } catch (Exception $e) {
            $message = $this->get('translator')->trans('school_information.delete.error', array(), 'flash_messages');

            $this->get('session')->getFlashBag()->add('error', $message);
        }


        return $this->redirect($this->generateUrl('case_data_list', array(
                'childSlug' => $childSchoolInformation->getChild()->getSlug()
            ))
        );
    }

    /**
     * @Route("/{id}/edit", name="child_school_information_edit", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $childSchoolInformation = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:ChildSchoolInformation')
            ->find($id);

        if (!$childSchoolInformation) {
            throw $this->createNotFoundException(
                'School information with id ' . $id . ' not found'
            );
        }

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $childSchoolInformation->getChild());

        $childSchoolName = $childSchoolInformation->getSchoolName();
        $childSchoolName = strlen($childSchoolName)<10 ? $childSchoolName : substr($childSchoolName, 0, 10) . ' [...]';

        $this->get('whaam_breadcrumbs')->load('Child:ChildSchoolInformation:edit')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childSchoolInformation->getChild()->getSlug()),
                    'case_data' => array('childSlug' => $childSchoolInformation->getChild()->getSlug()),
                    '%school_name%' => array('id' => $childSchoolInformation->getId())
                ),
                array(
                    '%child_name%' => $childSchoolInformation->getChild()->getNickname(),
                    '%school_name%' => $childSchoolName
                ));

        $form = $this->createForm('childSchoolInformation', $childSchoolInformation, array(
            'action' => $this->generateUrl('child_school_information_edit', array(
                        'id' => $id
                    )
                ),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));
        $formHandler = $this->get('whaam_child_school_information.form.handler.child_school_information_form_handler');

        if ($formHandler->handle($form, $request, 'school_information.edit.success')) {
            $this->get('whaam_notification.util.notification_manager')->send(
                $childSchoolInformation->getChild()->getChildUsers(),
                $this->get('translator')->trans('school_information_edit_title', array(), 'notification'),
                sprintf(
                    $this->get('translator')->trans('school_information_edit', array(), 'notification'),
                    (string) $childSchoolInformation->getChild(),
                    (string) $this->getUser()
                ),
                $this->generateUrl('child_school_information_view', array('id' => $childSchoolInformation->getId()))
            );

            return $this->redirect($this->generateUrl('case_data_list', array(
                    'childSlug' => $childSchoolInformation->getChild()->getSlug()
                ))
            );
        }

        return $this->render(
            'WHAAMPrivateApplicationChildBundle:ChildSchoolInformation:formPage.html.twig',
            array(
                'child' => $childSchoolInformation->getChild(),
                'childSlug' => $childSchoolInformation->getChild()->getSlug(),
                'form' => $form->createView(),
                'formTitle' => $this->get('translator')->trans('edit_school_information', array(), 'interface')
            )
        );
    }

    /**
     * @Route("/{childSlug}/new", name="child_school_information_new", schemes={"http", "https"})
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

        $this->get('whaam_breadcrumbs')->load('Child:ChildSchoolInformation:new')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childSlug),
                    'case_data' => array('childSlug' => $childSlug),
                ),
                array(
                    '%child_name%' => $child->getNickname(),
                ));


        $childSchoolInformation = new ChildSchoolInformation();
        $childSchoolInformation->setChild($child);

        $form = $this->createForm('childSchoolInformation', $childSchoolInformation, array(
            'action' => $this->generateUrl('child_school_information_new', array(
                        'childSlug' => $childSlug
                    )
                ),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));
        $formHandler = $this->get('whaam_child_school_information.form.handler.child_school_information_form_handler');

        if ($childSchoolInformation = $formHandler->handle($form, $request, 'school_information.new.success')) {
            $this->get('whaam_notification.util.notification_manager')->send(
                $child->getChildUsers(),
                $this->get('translator')->trans('school_information_new_title', array(), 'notification'),
                sprintf(
                    $this->get('translator')->trans('school_information_new', array(), 'notification'),
                    (string) $child,
                    (string) $this->getUser()
                ),
                $this->generateUrl('child_school_information_view', array('id' => $childSchoolInformation->getId()))
            );

            return $this->redirect($this->generateUrl('case_data_list', array(
                    'childSlug' => $childSlug
                ))
            );
        }

        return $this->render(
            'WHAAMPrivateApplicationChildBundle:ChildSchoolInformation:formPage.html.twig',
            array(
                'child' => $child,
                'childSlug' => $childSlug,
                'form' => $form->createView(),
                'formTitle' => $this->get('translator')->trans('new_school_information', array(), 'interface')
            )
        );
    }

    /**
     * @Route("/{id}/view", name="child_school_information_view", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request, $id)
    {
        $childSchoolInformation = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:ChildSchoolInformation')
            ->find($id);

        if (!$childSchoolInformation) {
            throw $this->createNotFoundException(
                'School information with id ' . $id . ' not found'
            );
        }

        $childSchoolName = $childSchoolInformation->getSchoolName();
        $childSchoolName = strlen($childSchoolName)<10 ? $childSchoolName : substr($childSchoolName, 0, 10) . ' [...]';


        $this->get('whaam_breadcrumbs')->load('Child:ChildSchoolInformation:view')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childSchoolInformation->getChild()->getSlug()),
                    'case_data' => array('childSlug' => $childSchoolInformation->getChild()->getSlug()),
                ),
                array(
                    '%child_name%' => $childSchoolInformation->getChild()->getNickname(),
                    '%school_name%' => $childSchoolName
                ));

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $childSchoolInformation->getChild());


        return array(
            'childSchoolInformation' => $childSchoolInformation,
            'child' => $childSchoolInformation->getChild(),
            'childSlug' => $childSchoolInformation->getChild()->getSlug()
        );
    }
}
