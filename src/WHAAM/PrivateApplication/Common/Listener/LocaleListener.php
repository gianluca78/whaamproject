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

namespace WHAAM\PrivateApplication\Common\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseEvent,
    Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface,
    Symfony\Component\Security\Core\SecurityContext;

class LocaleListener implements EventSubscriberInterface {

    private $defaultLocale;
    private $securityContext;

    public function __construct(SecurityContext $securityContext, $defaultLocale = 'en_GB')
    {
        $this->defaultLocale = $defaultLocale;
        $this->securityContext = $securityContext;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!$request->hasPreviousSession()) {
            return;
        }

        if ($locale = $request->attributes->get('_locale')) {
            $request->getSession()->set('_locale', $locale);
        } else {
            $request->setLocale($request->getSession()->get('_locale', $this->defaultLocale));
        }
    }

    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $request = $event->getRequest();

        $user = $this->securityContext->getToken()->getUser();

        $request->getSession()->set('_locale', $user->getSelectedLocale());
    }

    public static function getSubscribedEvents()
    {
        return array(
            KernelEvents::REQUEST => array(array('onKernelRequest', 17)),
        );
    }
} 