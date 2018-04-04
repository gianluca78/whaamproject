<?php

namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUserInvitationModeration;

/**
 * @Route("/child-network")
 *
 * Class ChildUserController
 * @package WHAAM\PrivateApplication\Bundle\ChildBundle\Controller
 */
class ChildUserController extends Controller
{
    /**
     * @Route("/{childSlug}/edit", name="child_user_edit", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     * @Template
     *
     * @param Request $request
     * @param $childSlug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $childSlug)
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

        $this->get('whaam_breadcrumbs')->load('Child:ChildUser:edit')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childSlug)
                ),
                array(
                    '%child_name%' => $child->getNickname()
                ));

        $childUser = $this->getDoctrine()->getRepository('WHAAMPrivateApplicationChildBundle:ChildUser')
            ->find(array('child' => $child, 'user' => $this->getUser()));

        $form = $this->createForm('childUser', $childUser, array(
            'action' => $this->generateUrl('child_user_edit', array(
                        'childSlug'=> $childSlug
                    )
                )
        ));
        $formHandler = $this->get('whaam_child.form.handler.child_user_form_handler');

        if($formHandler->handle($form, $request, 'child_user.edit.success')) {
            return $this->redirect($this->generateUrl('children_list'));
        }

        return array(
            'child' => $childUser->getChild(),
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/{childSlug}/index", name="child_network_list", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @param $childSlug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request, $childSlug)
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

        $this->get('whaam_breadcrumbs')->load('Child:ChildUser:index')
            ->processUrls(
                array(
                    '%child_name%' => array('slug' => $childSlug)
                ),
                array(
                    '%child_name%' => $child->getNickname()
                ));

        $loggedChildUser = $child->getChildUser($this->getUser());

        return array(
            'child' => $child,
            'childUsers' => $child->getChildUsers(),
            'loggedChildUser' => $loggedChildUser
        );
    }

    /**
     * @Route("/moderation-approval/{id}", name = "moderation_approval")
     */
    public function moderationApprovalAction(Request $request, ChildUserInvitationModeration $childUserInvitationModeration)
    {
        $message = '';
        $em = $this->getDoctrine()->getManager();

        if($this->getUser() != $childUserInvitationModeration->getModeratorUser()) {
            $message = $this->get('translator')->trans('moderation.authorization.error', array(), 'flash_messages');

            $this->get('session')->getFlashBag()->add('error', $message);
        } else {
            $message = $this->get('translator')->trans('success', array(), 'flash_messages') .
                ' ' . $this->get('translator')->trans('moderation.authorization.success', array(), 'flash_messages');

            $childUserInvitationModeration->setIsAccepted(1);
            $em->persist($childUserInvitationModeration);
            $em->flush();

            $invitationEmail = $this->get('whaam_child.util.child_user_invitation_email');

            $user = $em->getRepository('WHAAMPrivateApplicationUserBundle:User')
                ->findOneByEmail($childUserInvitationModeration->getChildUserInvitation()->getEmail());

            if($user) {
                $invitationEmail->sendToRegisteredUser($childUserInvitationModeration->getChildUserInvitation());
                $this->get('whaam_child.util.child_user_manager')
                    ->addUserToChildNetwork($childUserInvitationModeration->getChildUserInvitation()->getChild(),
                        $user
                    );
            } else {
                $invitationEmail->sendToUnregisteredUser($childUserInvitationModeration->getChildUserInvitation());
            }

            $invitationEmail->sendModerationApproval($childUserInvitationModeration->getChildUserInvitation());

            $this->get('session')->getFlashBag()->add('success', $message);
        }

        return $this->redirect($this->generateUrl('children_list'));

    }

    /**
     * @Route("/moderation-refusal/{id}", name = "moderation_refusal")
     */
    public function moderationRefusalAction(Request $request, ChildUserInvitationModeration $childUserInvitationModeration) {
        $message = '';
        $em = $this->getDoctrine()->getManager();

        if($this->getUser() != $childUserInvitationModeration->getModeratorUser()) {
            $message = $this->get('translator')->trans('moderation.authorization.error', array(), 'flash_messages');

            $this->get('session')->getFlashBag()->add('error', $message);
        } else {
            $message = $this->get('translator')->trans('success', array(), 'flash_messages') .
                ' ' . $this->get('translator')->trans('moderation.authorization.success', array(), 'flash_messages');

            $childUserInvitationModeration->setIsAccepted(0);
            $em->persist($childUserInvitationModeration);
            $em->flush();

            $this->get('whaam_child.util.child_user_invitation_email')
                ->sendModerationRefusal($childUserInvitationModeration->getChildUserInvitation());

            $this->get('session')->getFlashBag()->add('success', $message);
        }

        return $this->redirect($this->generateUrl('children_list'));
    }

    /**
     * @Route("/{childId}/{userId}/remove-user", name="child_network_remove_user", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @param $childId
     * @param $userId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function removeUserFromNetworkAction(Request $request, $childId, $userId)
    {
        $child = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:Child')
            ->find($childId);

        if (!$child) {
            throw $this->createNotFoundException(
                'Child with id ' . $childId . ' not found'
            );
        }

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);
        $this->get('common_util.child_network_members_checker')->isGranted($this->getUser(), $child, 'ROLE_PARENT');

        $user = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationUserBundle:User')
            ->find($userId);

        if (!$user) {
            throw $this->createNotFoundException(
                'User with id ' . $childId . ' not found'
            );
        }

        $selectedChildUser = $child->getChildUser($user);

        $em = $this->getDoctrine()->getManager();

        $selectedChildUser->setIsApprovedByParent(0);

        $em->persist($selectedChildUser);
        $em->flush();

        return $this->redirect($this->generateUrl(
                'child_network_list',
                array(
                    'childSlug' => $child->getSlug())
            )
        );
    }
}
