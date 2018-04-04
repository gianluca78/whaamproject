<?php
namespace WHAAM\PrivateApplication\Bundle\NotificationBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/notification")
 *
 * Class NotificationController
 * @package WHAAM\PrivateApplication\Bundle\NotificationBundle\Controller
 */

class NotificationController extends Controller
{
    /**
     * @Route("/count-not-read", name="notification_count_not_read", schemes={"http", "https"})
     * @Method({"GET"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function countNotReadNotificationAction(Request $request)
    {
        if(!$request->isXmlHttpRequest())
        {
            throw new \Exception('This controller allows only ajax requests');
        }

        $em = $this->getDoctrine()->getManager();

        $notifications = $em->getRepository('WHAAMPrivateApplicationNotificationBundle:Notification')
            ->findNotReadNotificationsByUser($this->getUser());

        return new Response(count($notifications));
    }

    /**
     * @Route("/delete/{notificationId}", name="notification_delete", schemes={"http", "https"})
     * @Method({"GET"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAction(Request $request, $notificationId)
    {



        $notification = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationNotificationBundle:Notification')
            ->find($notificationId);

        if (!$notification) {
            throw $this->createNotFoundException(
                'Notification with id ' . $notificationId . ' not found'
            );
        }

        $message = $this->get('translator')->trans('success', array(), 'flash_messages') .
            ' ' . $this->get('translator')->trans('notification.delete.success', array(), 'flash_messages');

        $this->get('session')->getFlashBag()->add('success', $message);

        $em = $this->getDoctrine()->getManager();
        $em->remove($notification);
        $em->flush();

        return $this->redirect($this->generateUrl('notification_list'));
    }

    /**
     * @Route("/delete-all", name="notification_delete_all", schemes={"http", "https"})
     * @Method({"GET"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function deleteAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $em->getRepository('WHAAMPrivateApplicationNotificationBundle:Notification')
            ->deleteNotificationsByUser($this->getUser());

        $message = $this->get('translator')->trans('success', array(), 'flash_messages') .
            ' ' . $this->get('translator')->trans('notification.delete.all.success', array(), 'flash_messages');

        $this->get('session')->getFlashBag()->add('success', $message);

        return $this->redirect($this->generateUrl('notification_list'));
    }

    /**
     * @Route("/list", name="notification_list", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $this->get('whaam_breadcrumbs')->load('Notification:Notification:index')->processUrls();

        $notifications = $em->getRepository('WHAAMPrivateApplicationNotificationBundle:Notification')
            ->findNotificationsByUser($this->getUser());

        return array('notifications' => $notifications);
    }

    /**
     * @Route("/not-displayed-list", name="notification_not_displayed_list", schemes={"http", "https"})
     * @Method({"GET"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function notDisplayedNotificationListAction(Request $request)
    {
        if(!$request->isXmlHttpRequest())
        {
            throw new \Exception('This controller allows only ajax requests');
        }

        $em = $this->getDoctrine()->getManager();

        $notifications = $em->getRepository('WHAAMPrivateApplicationNotificationBundle:Notification')
            ->findNotDisplayedNotificationsByUser($this->getUser());

        return new Response(json_encode($notifications));
    }

    /**
     * @Route("/read-all", name="notification_read_all", schemes={"http", "https"})
     * @Method({"GET"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function readAllAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        $em->getRepository('WHAAMPrivateApplicationNotificationBundle:Notification')
            ->allReadNotificationsByUser($this->getUser());

        return $this->redirect($this->generateUrl('notification_list'));
    }

    /**
     * @Route("/update-displayed-status", name="notification_update_displayed_status", schemes={"http", "https"})
     * @Method({"POST"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function updateDisplayedStatusAction(Request $request)
    {
        if(!$request->isXmlHttpRequest())
        {
            throw new \Exception('This controller allows only ajax requests');
        }

        $em = $this->getDoctrine()->getManager();

        $notification = $em->getRepository('WHAAMPrivateApplicationNotificationBundle:Notification')
            ->find($request->get('id'));

        if (!$notification) {
            throw $this->createNotFoundException(
                'Notification with id ' . $request->get('id') . ' not found'
            );
        }

        $notification->setIsDisplayed(1);

        $em->persist($notification);
        $em->flush();

        return new Response();
    }

    /**
     * @Route("/{notificationId}/update-read-status", name="notification_update_read_status", schemes={"http", "https"})
     * @Method({"GET"})
     *
     * @param Request $request
     */
    public function updateReadStatusAction(Request $request, $notificationId)
    {
        $em = $this->getDoctrine()->getManager();

        $notification = $em->getRepository('WHAAMPrivateApplicationNotificationBundle:Notification')
            ->find($notificationId);

        if (!$notification) {
            throw $this->createNotFoundException(
                'Notification with id ' . $request->get('id') . ' not found'
            );
        }

        $notification->setIsRead(1);

        $em->persist($notification);
        $em->flush();

        return $this->redirect($this->generateUrl('notification_list'));
    }
}
