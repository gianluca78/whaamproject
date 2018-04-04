<?php

namespace WHAAM\PrivateApplication\Bundle\ChildBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request;

use Symfony\Component\HttpFoundation\Response;
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\Child;
use WHAAM\PrivateApplication\Bundle\ChildBundle\Entity\ChildUserInvitation;

/**
 * @Route("/children")
 *
 * Class ChildController
 * @package WHAAM\PrivateApplication\Bundle\ChildBundle\Controller
 */
class ChildController extends Controller
{
    /**
     * @Route("/{slug}/delete", name="child_delete", schemes={"http", "https"})
     * @Method({"GET"})
     *
     * @param Request $request
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     *
     */
    public function deleteAction(Request $request, $slug)
    {
        $child = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:Child')
            ->findOneBySlug($slug);

        $em = $this->getDoctrine()->getManager();

        if (!$child) {
            throw $this->createNotFoundException(
                'Child ' . $slug . ' not found'
            );
        }

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        try {
            $deletedChild = $child;

            $message = $this->get('translator')->trans('success', array(), 'flash_messages') .
                ' ' . $this->get('translator')->trans('child.delete.success', array(), 'flash_messages');

            $this->get('session')->getFlashBag()->add('success', $message);

            $this->get('whaam_notification.util.notification_manager')->send(
                $deletedChild->getChildUsers(),
                $this->get('translator')->trans('child_delete_title', array(), 'notification'),
                sprintf(
                    $this->get('translator')->trans('child_delete', array(), 'notification'),
                    (string) $deletedChild,
                    (string) $this->getUser()
                ),
                null
            );

            $em->remove($child);
            $em->flush();

        } catch (Exception $e){
            $message = $this->get('translator')->trans('child.delete.error', array(), 'flash_messages');

            $this->get('session')->getFlashBag()->add('error', $message);
        }

        return $this->redirect($this->generateUrl('children_list'));
    }

    /**
     * @Route("/{slug}/edit", name="child_edit", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     * @Template
     *
     * @param Request $request
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function editAction(Request $request, $slug)
    {
        $child = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:Child')
            ->findOneBySlug($slug);

        if (!$child) {
            throw $this->createNotFoundException(
                'Child ' . $slug . ' not found'
            );
        }

        $this->get('whaam_breadcrumbs')->load('Child:Child:edit')
            ->processUrls(array('%child_name%' => array('slug' => $slug)), array('%child_name%' => $child->getNickname()));

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        $form = $this->createForm('child_edit', $child, array(
            'action' => $this->generateUrl('child_edit', array(
                        'slug'=> $slug
                    )
                )
        ));
        $form->get('base_child')->get('countChildSiblings')->setData(count($child->getSiblings()));
        $formHandler = $this->get('whaam_child.form.handler.child_form_handler');
        $formHandler->setOriginalChildSiblingsFromChild($child);

        if($formHandler->handle($form, $request, 'child.edit.success')) {
            $this->get('whaam_notification.util.notification_manager')->send(
                $child->getChildUsers(),
                $this->get('translator')->trans('child_edit_title', array(), 'notification'),
                sprintf(
                    $this->get('translator')->trans('child_edit', array(), 'notification'),
                    (string) $child,
                    (string) $this->getUser()
                ),
                $this->generateUrl('child_view', array('slug' => $child->getSlug()))
            );

            return $this->redirect($this->generateUrl('children_list'));
        }

        return array(
            'child' => $child,
            'form' => $form->createView(),
            'removeSiblingTranslation' => $this->get('translator')->trans('remove_sibling', array(), 'interface')
        );
    }

    /**
     * @Route("/network-data", name="child_network", schemes={"http", "https"})
     * @Method({"POST"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function recipientDataAction(Request $request)
    {
        if(!$request->isXmlHttpRequest())
        {
            throw new \Exception('This controller allows only ajax requests');
        }

        $child = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:Child')
            ->find($request->get('id'));

        if (!$child) {
            throw $this->createNotFoundException(
                'Child with id ' . $request->get('id') . ' not found'
            );
        }

        $result = array();

        foreach ($child->getChildUsers() as $childUser) {
            if($childUser->getUser() != $this->getUser()) {
                $result[] = array('id' => $childUser->getUser()->getId(), 'user' => (string) $childUser->getUser());
            }
        }

        usort($result, function ($a, $b)
        {
            if ($a['user'] == $b['user']) {
                return 0;
            }

            return ($a['user'] < $b['user']) ? -1 : 1;
        });

        return new Response(json_encode($result));
    }

   /**
    * @Route("/", name="children_list", schemes={"http", "https"})
    * @Method({"GET"})
    * @Template
    *
    * @param Request $request
    * @return \Symfony\Component\HttpFoundation\Response
    */
   public function indexAction(Request $request)
   {
       $user = $this->getUser();

       $children = $this->getDoctrine()
           ->getRepository('WHAAMPrivateApplicationChildBundle:Child')
           ->findChildrenByUserSlug($user->getSlug(), $request->getLocale());

       $this->get('whaam_breadcrumbs')->load('Child:Child:index')
           ->processUrls(array(), array());

       return array('children' => $children);
   }

    /**
     * @Route("/invitation/{childSlug}", name="invitation", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     * @Template
     *
     * @param Request $request
     * @param string $childSlug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function invitationAction(Request $request, $childSlug) {
        $child = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:Child')
            ->findOneBySlug($childSlug);

        if (!$child) {
            throw $this->createNotFoundException(
                'Child ' . $childSlug . ' not found'
            );
        }

        $this->get('whaam_breadcrumbs')->load('Child:Child:invitation')
            ->processUrls(array('%child_name%' => array('slug' => $childSlug)), array('%child_name%' => $child->getNickname()));

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        $childUserInvitation = new ChildUserInvitation();
        $childUserInvitation->setChild($child);
        $childUserInvitation->setSenderUser($this->getUser());

        $form = $this->createForm('childUserInvitation', $childUserInvitation, array(
            'action' => $this->generateUrl('invitation', array(
                        'childSlug'=> $childSlug
                    )
                ),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));

        $formHandler = $this->get('whaam_child.form.handler.child_user_invitation_form_handler');

        if($formHandler->handle($form, $request)) {
            $this->get('whaam_notification.util.notification_manager')->send(
                $child->getChildUsers(),
                $this->get('translator')->trans('invitation_new_title', array(), 'notification'),
                sprintf(
                    $this->get('translator')->trans('invitation_new', array(), 'notification'),
                    $form->get('email')->getData(),
                    (string) $child
                ),
                null
            );

            return $this->redirect($this->generateUrl('invitation', array('childSlug' => $child->getSlug())));
        }

        return array(
            'child' => $child,
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/new", name="child_new", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $child = new Child();
        $child->setChildCreatorUser($this->getUser());

        $this->get('whaam_breadcrumbs')->load('Child:Child:new')
            ->processUrls(array(), array());

        $form = $this->createForm('child', $child, array(
            'action' => $this->generateUrl('child_new'),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));
        $form->get('base_child')->get('countChildSiblings')->setData('0');

        $formHandler = $this->get('whaam_child.form.handler.child_form_handler');

        if($child = $formHandler->handle($form, $request, 'child.new.success')) {
            $this->get('whaam_notification.util.notification_manager')->send(
                $child->getChildUsers(),
                $this->get('translator')->trans('child_new_title', array(), 'notification'),
                sprintf(
                    $this->get('translator')->trans('child_new', array(), 'notification'),
                    (string) $child,
                    (string) $this->getUser()
                ),
                $this->generateUrl('child_view', array('slug' => $child->getSlug()))
            );

            return $this->redirect($this->generateUrl('child_user_edit', array('childSlug' => $child->getSlug())));
        }

        return array(
            'child' => $child,
            'form' => $form->createView(),
            'removeSiblingTranslation' => $this->get('translator')->trans('remove_sibling', array(), 'interface')
        );
    }

    /**
     * @Route("/{slug}/view", name="child_view", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @param $slug
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request, $slug)
    {
        $child = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationChildBundle:Child')
            ->findOneBySlug($slug);

        if (!$child) {
            throw $this->createNotFoundException(
                'Child ' . $slug . ' not found'
            );
        }

        $this->get('whaam_breadcrumbs')->load('Child:Child:view')
            ->processUrls(array('%child_name%' => array('slug' => $slug)), array('%child_name%' => $child->getNickname()));

        $this->get('common_util.child_network_members_checker')->check($this->getUser(), $child);

        return array('child' => $child);
    }
}
