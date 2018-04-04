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
namespace WHAAM\PrivateApplication\Bundle\UserBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

use WHAAM\PrivateApplication\Bundle\UserBundle\Entity\User;

/**
 * @Route("/users")
 *
 * Class UserController
 * @package WHAAM\PrivateApplication\Bundle\UserBundle\Controller
 */
class UserController extends Controller
{
    /**
     * @Route("/account-activation/{userSlug}/{emailToken}", name="user_account_activation", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @param $userSlug
     * @param $emailToken
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function accountActivationAction(Request $request, $userSlug, $emailToken)
    {
        $user = $this->getDoctrine()->getRepository('WHAAMPrivateApplicationUserBundle:User')->findOneBySlug($userSlug);

        if (!$user) {
            throw $this->createNotFoundException(
                'User with slug ' . $userSlug . ' not found'
            );
        }

        $em = $this->getDoctrine()->getManager();

        if($user->getEmailToken() == $emailToken) {
            $user->setIsActive(true);
            $em->persist($user);
            $em->flush();

            $this->get('whaam_child.util.child_user_invitation_manager')->managePendingInvitations($user);

            $message = $this->get('translator')->trans('account_activated', array(), 'interface');
            $this->get('session')->getFlashBag()->add('success', $message);

        } else {
            $message = $this->get('translator')->trans('invalid_salt', array(), 'error_messages');
            $this->get('session')->getFlashBag()->add('error', $message);
        }

        return $this->redirect($this->generateUrl('children_list'));
    }

    /**
     * @Route("/edit", name="user_edit", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request)
    {
        $user = $this->getUser();

        $this->get('whaam_breadcrumbs')->load('User:User:edit')
            ->processUrls(
                array(
                    '%user_name%' => array()
                ),
                array('%user_name%' => $user->getSurnameNameOrUsername()));

        $form = $this->createForm('user_edit', $user, array(
            'action' => $this->generateUrl('user_edit', array('userSlug' => $user->getSlug())),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));

        $formHandler = $this->get('whaam_user.form.handler.user_edit_form_handler');

        if($formHandler->handle($form, $request, 'user.edit.success')) {
            return $this->redirect($this->generateUrl('user_index'));
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/edit-password", name="user_edit_password", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editPasswordAction(Request $request)
    {
        $user = $this->getUser();

        $this->get('whaam_breadcrumbs')->load('User:User:editPassword')
            ->processUrls(
                array(
                    '%user_name%' => array()
                ),
                array('%user_name%' => $user->getSurnameNameOrUsername()));

        $form = $this->createForm('user_edit_password', $user, array(
            'action' => $this->generateUrl('user_edit_password'),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));

        $formHandler = $this->get('whaam_user.form.handler.user_edit_password_form_handler');

        if($formHandler->handle($form, $request, 'user.edit_password.success')) {
            return $this->redirect($this->generateUrl('user_index'));
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/forgot-password", name="user_forgot_password", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function forgotPasswordAction(Request $request)
    {
        $form = $this->createForm('user_email', new User(), array(
            'action' => $this->generateUrl('user_forgot_password'),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));

        $formHandler = $this->get('whaam_user.form.handler.user_forgot_password_form_handler');
        if($formHandler->handle($form, $request, 'user.forgot_password.success')) {
            return $this->redirect($this->generateUrl('_login'));
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/account", name="user_index", schemes={"https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction()
    {
        $this->get('whaam_breadcrumbs')->load('User:User:index')
            ->processUrls(
                array(
                    '%user_name%' => array()
                ),
                array('%user_name%' => $this->getUser()->getSurnameNameOrUsername()));

        return array();
    }

    /**
     * @Route("/new/{_locale}", name="user_new", defaults={"_locale" = "en_GB"}, schemes={"http", "https"})
     * @Method({"GET", "POST"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $userRole = $this->getDoctrine()->getRepository('WHAAMPrivateApplicationUserBundle:ApplicationRole')
            ->findOneByRole('ROLE_USER');

        $user = new User();
        $user->setRole($userRole);
        $user->setSelectedLocale($request->get('_locale'));

        $form = $this->createForm('user', $user, array(
            'action' => $this->generateUrl('user_new'),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));

        $formHandler = $this->get('whaam_user.form.handler.user_form_handler');

        if($formHandler->handle($form, $request, 'user.new.success')) {
            $emailAccountActivation = $this->get('whaam_user.util.email_account_activation');
            $emailAccountActivation->send($user);

            return $this->redirect($this->generateUrl('user_new'));
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/reset-password/{emailToken}", name="user_reset_password", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @param $emailToken
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function resetPasswordAction(Request $request, $emailToken)
    {
        $user = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationUserBundle:User')
            ->findOneByEmailToken($emailToken);

        if (!$user) {
            $message = $this->get('translator')->trans('invalid_password_reset', array(), 'error_messages');
            $this->get('session')->getFlashBag()->add('error', $message);
        }

        $plainTextNewPassword = $this->get('whaam_user.util.user_manager')->refreshPassword($user);
        $this->get('whaam_user.util.email_new_password')->send($user, $plainTextNewPassword);

        $message = $this->get('translator')->trans('password_reset', array(), 'interface');
        $this->get('session')->getFlashBag()->add('success', $message);

        return $this->redirect($this->generateUrl('children_list'));
    }

    /**
     * @Route("/view", name="user_view", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request)
    {
        $this->get('whaam_breadcrumbs')->load('User:User:view')
            ->processUrls(
                array(
                    '%user_name%' => array()

                ),
                array('%user_name%' => $this->getUser()->getSurnameNameOrUsername()));

        $user = $this->getUser();

        return array(
            'user' => $user
        );
    }
}