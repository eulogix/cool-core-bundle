<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Listener;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

use Symfony\Component\HttpFoundation\RequestStack,
    Symfony\Component\HttpFoundation\RedirectResponse,
    Symfony\Component\HttpFoundation\Session\Session,
    Symfony\Component\Routing\Router;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class AjaxAuthenticationListener
{
    protected $_session;
    protected $_router;
    protected $_request;

    protected $defaultRoute;

    public function __construct(Session $session, Router $router, RequestStack $requestStack, $defaultRoute)
    {
        $this->_session = $session;
        $this->_router = $router;
        $this->_request = $requestStack->getCurrentRequest();
        $this->defaultRoute = $defaultRoute;
    }

    /**
     * Handles security related exceptions.
     *
     * @param GetResponseForExceptionEvent $event An GetResponseForExceptionEvent instance
     */
    public function onCoreException(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();
        $request = $event->getRequest();

        if ($exception instanceof AuthenticationException || $exception instanceof AccessDeniedException) {
            if ($request->isXmlHttpRequest()) {
                $event->setResponse(new Response('', 403));
            } else {
                $this->_session->set('cool_last_denied_url', full_url($_SERVER));

                $pi = $request->getPathInfo();

                //hack that redirects the user to the admin login instead of the main application login
                //it MAY be done with security.yml but I've lost enough hair trying to make it work, this will do for now
                if(preg_match('%/admin(/.*)%im', $pi)) {
                    $event->setResponse(new RedirectResponse( $this->_router->generate('_coolAdminLogin')));
                } elseif(!preg_match('%.*?/api($|/.+?)$%m', $pi)) //any url with this format refers to a REST api, so no redirection
                    $event->setResponse(new RedirectResponse( $this->_router->generate( $this->defaultRoute )));
            }
        }
    }
}