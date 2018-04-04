<?php
namespace WHAAM\PrivateApplication\Bundle\NotificationBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Method,
    Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Request,
    Symfony\Component\HttpFoundation\Response;

use WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\Message;
use WHAAM\PrivateApplication\Bundle\NotificationBundle\Entity\Answer;

/**
 * @Route("/messages")
 *
 * Class MessageController
 * @package WHAAM\PrivateApplication\Bundle\NotificationBundle\Controller
 */
class MessageController extends Controller
{
    /**
     * @Route("/not-displayed-list", name="message_not_displayed_list", schemes={"http", "https"})
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

        $messages = $em->getRepository('WHAAMPrivateApplicationNotificationBundle:Message')
            ->findNotDisplayedMessagesByUser($this->getUser());

        return new Response(json_encode($messages));
    }

    /**
     * @Route("/count-not-read", name="message_count_not_read", schemes={"http", "https"})
     * @Method({"GET"})
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function countNotReadMessageAction(Request $request)
    {
        if(!$request->isXmlHttpRequest())
        {
            throw new \Exception('This controller allows only ajax requests');
        }

        $em = $this->getDoctrine()->getManager();

        $messages = $em->getRepository('WHAAMPrivateApplicationNotificationBundle:Message')
            ->findNotReadMessagesByUser($this->getUser());

        return new Response(count($messages));
    }

    /**
     * @Route("/inbox", name="message_inbox", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function inboxAction()
    {
        $this->get('whaam_breadcrumbs')->load('Notification:Message:inbox')
            ->processUrls(array(), array());

        $messages = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationNotificationBundle:Message')
            ->findInboxByUser($this->getUser())
        ;

        return array('messages' => $messages);
    }

    /**
     * @Route("/sent", name="message_sent", schemes={"http", "https"})
     * @Method({"GET"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function sentAction()
    {
        $this->get('whaam_breadcrumbs')->load('Notification:Message:sent')
            ->processUrls(array(), array());

        $messages = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationNotificationBundle:Message')
            ->findSentByUser($this->getUser())
        ;

        return array('messages' => $messages);
    }

    /**
     * @Route("/new", name="message_new", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function newAction(Request $request)
    {
        $message = new Message();
        $message->setSender($this->getUser());

        $this->get('whaam_breadcrumbs')->load('Notification:Message:new')
            ->processUrls(array(), array());

        $form = $this->createForm('message', $message, array(
            'action' => $this->generateUrl('message_new'),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));

        $formHandler = $this->get('whaam_notification.form.handler.message_form_handler');

        if($formHandler->handle($form, $request, 'message.new.success')) {
            return $this->redirect($this->generateUrl('message_inbox'));
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/answer/{slug}", name="message_answer", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function answerAction(Request $request, $slug)
    {
        $message = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationNotificationBundle:Message')
            ->findOneBySlug($slug);

        if (!$message) {
            throw $this->createNotFoundException(
                'Message with slug ' . $slug . ' not found'
            );
        }

        $answer = new Answer();
        $answer->setSender($this->getUser());
        $answer->setMessage($message);

        $this->get('whaam_breadcrumbs')->load('Notification:Message:answer')
            ->processUrls(array(), array('%message_subject%' => $message->getSubject()));

        $form = $this->createForm('answer', $answer, array(
            'action' => $this->generateUrl('message_answer', array('slug' => $message->getSlug())),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));

        $formHandler = $this->get('whaam_notification.form.handler.answer_form_handler');

        if($formHandler->handle($form, $request, 'message.reply.success')) {
            return $this->redirect($this->generateUrl('message_view', array('slug' => $message->getSlug())));
        }

        return array(
            'form' => $form->createView()
        );
    }

    /**
     * @Route("/update-displayed-status", name="message_update_displayed_status", schemes={"http", "https"})
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

        if($request->get('answerStatusId')) {
            $answerStatus = $em->getRepository('WHAAMPrivateApplicationNotificationBundle:AnswerStatus')
                ->find($request->get('answerStatusId'));

            if (!$answerStatus) {
                throw $this->createNotFoundException(
                    'Answer status with id ' . $request->get('answerStatusId') . ' not found'
                );
            }

            $answerStatus->setIsDisplayed(1);

            $em->persist($answerStatus);
            $em->flush();

        } else {
            $messageStatus = $em->getRepository('WHAAMPrivateApplicationNotificationBundle:MessageStatus')
                ->find($request->get('messageStatusId'));

            if (!$messageStatus) {
                throw $this->createNotFoundException(
                    'Message status with id ' . $request->get('messageStatusId') . ' not found'
                );
            }

            $messageStatus->setIsDisplayed(1);

            $em->persist($messageStatus);
            $em->flush();
        }

        return new Response();
    }

    /**
     * @Route("/view/{slug}", name="message_view", schemes={"http", "https"})
     * @Method({"GET", "POST"})
     * @Template
     *
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function viewAction(Request $request, $slug)
    {
        $this->get('whaam_breadcrumbs')->load('Notification:Message:view')
            ->processUrls(array(), array());

        $message = $this->getDoctrine()
            ->getRepository('WHAAMPrivateApplicationNotificationBundle:Message')
            ->findOneBySlug($slug);

        if (!$message) {
            throw $this->createNotFoundException(
                'Message with slug ' . $slug . ' not found'
            );
        }

        foreach ($message->getStatuses() as $status) {
            if($status->getUser() == $this->getUser()) {
                $status->setIsRead(1);
            }
        }

        foreach ($message->getAnswers() as $answer) {
            foreach ($answer->getStatuses() as $status) {
                if($status->getUser() == $this->getUser()) {
                    $status->setIsRead(1);
                }
            }
        }

        $em = $this->getDoctrine()->getManager();
        $em->persist($message);
        $em->flush();

        $answer = new Answer();
        $answer->setSender($this->getUser());
        $answer->setMessage($message);

        $form = $this->createForm('answer', $answer, array(
            'action' => $this->generateUrl('message_view', array('slug' => $message->getSlug())),
            'attr' => array(
                'novalidate' => 'true'
            )
        ));

        $formHandler = $this->get('whaam_notification.form.handler.answer_form_handler');

        if($formHandler->handle($form, $request, 'message.reply.success')) {
            return $this->redirect($this->generateUrl('message_view', array('slug' => $message->getSlug())));
        }

        return array('message' => $message, 'form' => $form->createView());
    }
}