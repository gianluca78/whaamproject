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
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildDisciplineReferral;

/**
 * @Route("/child-discipline-referral")
 *
 * Class ChildDisciplineReferralController
 * @package WHAAM\PrivateApplication\Bundle\ChildBundle\Controller
 */
class ChildDisciplineReferralController extends Controller
{
    /**
     * @Route("/{id}/delete", name="child_discipline_referral_delete", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $id)
    {
        $childDisciplineReferral = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:ChildDisciplineReferral')
            ->find($id);

        if (!$childDisciplineReferral) {
            throw $this->createNotFoundException(
                'Discipline referral with id ' . $id . ' not found'
            );
        }

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $childDisciplineReferral->getChild());
        $em = $this->getDoctrine()->getManager();

        try {
            $childDisciplineReferralDeleted = $childDisciplineReferral;

            $em->remove($childDisciplineReferral);
            $em->flush();
            $message = $this->get('translator')->trans('discipline_referral.delete.success', array(), 'flash_messages');

            $this->get('session')->getFlashBag()->add('success', $message);

            $this->get('whaam_notification.util.notification_manager')->send(
                $childDisciplineReferralDeleted->getChild()->getChildUsers(),
                $this->get('translator')->trans('discipline_referral_delete_title', array(), 'notification'),
                sprintf(
                    $this->get('translator')->trans('discipline_referral_delete', array(), 'notification'),
                    (string) $childDisciplineReferralDeleted->getChild(),
                    (string) $this->getUser()
                ),
                null
            );

        } catch (Exception $e) {
            $message = $this->get('translator')->trans('discipline_referral.delete.error', array(), 'flash_messages');

            $this->get('session')->getFlashBag()->add('error', $message);
        }


        return $this->redirect($this->generateUrl('case_data_list', array(
                'childSlug' => $childDisciplineReferral->getChild()->getSlug()
            ))
        );
    }

    /**
     * @Route("/{id}/edit", name="child_discipline_referral_edit", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $id)
    {
        $childDisciplineReferral = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:ChildDisciplineReferral')
            ->find($id);

        if (!$childDisciplineReferral) {
            throw $this->createNotFoundException(
                'Discipline referral with id ' . $id . ' not found'
            );
        }

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $childDisciplineReferral->getChild());

        $this->get('whaam_breadcrumbs')->load('Child:ChildDisciplineReferral:edit')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childDisciplineReferral->getChild()->getSlug()),
                    'case_data' => array('childSlug' => $childDisciplineReferral->getChild()->getSlug()),
                    '%discipline_referral_type%' => array('id' => $childDisciplineReferral->getId())
                ),
                array(
                    '%child_name%' => $childDisciplineReferral->getChild()->getNickname(),
                    '%discipline_referral_type%' => $childDisciplineReferral->getDisciplineReferralType()->getType()

                ));

        $form = $this->createForm('childDisciplineReferral', $childDisciplineReferral, array(
            'action' => $this->generateUrl('child_discipline_referral_edit', array(
                        'id' => $id
                    )
                ),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));
        $formHandler = $this->get('whaam_child_discipline_referral.form.handler.child_discipline_referral_form_handler');

        if ($formHandler->handle($form, $request, 'discipline_referral.edit.success')) {
            $this->get('whaam_notification.util.notification_manager')->send(
                $childDisciplineReferral->getChild()->getChildUsers(),
                $this->get('translator')->trans('discipline_referral_edit_title', array(), 'notification'),
                sprintf(
                    $this->get('translator')->trans('discipline_referral_edit', array(), 'notification'),
                    (string) $childDisciplineReferral->getChild(),
                    (string) $this->getUser()
                ),
                $this->generateUrl('child_discipline_referral_view', array('id' => $childDisciplineReferral->getId()))
            );

            return $this->redirect($this->generateUrl('case_data_list', array(
                    'childSlug' => $childDisciplineReferral->getChild()->getSlug()
                ))
            );
        }

        return $this->render(
            'WHAAMPrivateApplicationChildBundle:ChildDisciplineReferral:formPage.html.twig',
            array(
                'child' => $childDisciplineReferral->getChild(),
                'childSlug' => $childDisciplineReferral->getChild()->getSlug(),
                'form' => $form->createView(),
                'formTitle' => $this->get('translator')->trans('edit_discipline_referral', array(), 'interface')
            )
        );
    }

    /**
     * @Route("/{childSlug}/new", name="child_discipline_referral_new", schemes={"http", "https"})
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

        $this->get('whaam_breadcrumbs')->load('Child:ChildDisciplineReferral:new')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childSlug),
                    'case_data' => array('childSlug' => $childSlug),
                ),
                array(
                    '%child_name%' => $child->getNickname(),
                ));

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        $childDisciplineReferral = new ChildDisciplineReferral();
        $childDisciplineReferral->setChild($child);
        $childDisciplineReferral->setDate(new \DateTime());

        $form = $this->createForm('childDisciplineReferral', $childDisciplineReferral, array(
            'action' => $this->generateUrl('child_discipline_referral_new', array(
                        'childSlug' => $childSlug
                    )
                ),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));
        $formHandler = $this->get('whaam_child_discipline_referral.form.handler.child_discipline_referral_form_handler');

        if ($childDisciplineReferral = $formHandler->handle($form, $request, 'discipline_referral.new.success')) {
            $this->get('whaam_notification.util.notification_manager')->send(
                $child->getChildUsers(),
                $this->get('translator')->trans('discipline_referral_new_title', array(), 'notification'),
                sprintf(
                    $this->get('translator')->trans('discipline_referral_new', array(), 'notification'),
                    (string) $child,
                    (string) $this->getUser()
                ),
                $this->generateUrl('child_discipline_referral_view', array('id' => $childDisciplineReferral->getId()))
            );

            return $this->redirect($this->generateUrl('case_data_list', array(
                    'childSlug' => $childSlug
                ))
            );
        }

        return $this->render(
            'WHAAMPrivateApplicationChildBundle:ChildDisciplineReferral:formPage.html.twig',
            array(
                'child' => $child,
                'childSlug' => $childSlug,
                'form' => $form->createView(),
                'formTitle' => $this->get('translator')->trans('new_discipline_referral', array(), 'interface')
            )
        );
    }

    /**
     * @Route("/{id}/view", name="child_discipline_referral_view", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @param $id
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request, $id)
    {
        $childDisciplineReferral = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:ChildDisciplineReferral')
            ->find($id);

        if (!$childDisciplineReferral) {
            throw $this->createNotFoundException(
                'Discipline referral with id ' . $id . ' not found'
            );
        }

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $childDisciplineReferral->getChild());

        $this->get('whaam_breadcrumbs')->load('Child:ChildDisciplineReferral:view')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childDisciplineReferral->getChild()->getSlug()),
                    'case_data' => array('childSlug' => $childDisciplineReferral->getChild()->getSlug()),
                ),
                array(
                    '%child_name%' => $childDisciplineReferral->getChild()->getNickname(),
                    '%discipline_referral_type%' =>  $childDisciplineReferral->getDisciplineReferralType()->getType(),

                ));

        return array(
            'childDisciplineReferral' => $childDisciplineReferral,
            'child' => $childDisciplineReferral->getChild(),
            'childSlug' => $childDisciplineReferral->getChild()->getSlug()
        );
    }
}
