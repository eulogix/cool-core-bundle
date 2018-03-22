<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Controller\Reminders;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

// these import the "@Route" and "@Template" annotations
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

use Eulogix\Cool\Lib\Cool;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class RemindersController extends Controller
{
     
    /**
     * @Route("/panel", name="remindersPanel", options={"expose"=true})
     * @Template(engine="twig")
     */
    public function remindersPanelAction()
    {
        return $this->render('EulogixCoolCoreBundle:Reminders:panel.html.twig');
    }

    /**
     * @Route("/getSimpleMatrix", name="getSimpleMatrix", options={"expose"=true})
     */
    public function getSimpleMatrix(Request $request) {
        $manager = Cool::getInstance()->getFactory()->getRemindersManager();
        $manager->getParameters()->replace($request->request->all());
        return new JsonResponse( $manager->getSimpleCountMatrix() );
    }

    /**
     * @Route("/getDatedMatrix/{dateStart}/{days}", name="getDatedMatrix", options={"expose"=true})
     */
    public function getDatedMatrix($dateStart, $days, Request $request) {
        $dtDateStart = new \DateTime($dateStart);
        $manager = Cool::getInstance()->getFactory()->getRemindersManager();
        $manager->getParameters()->replace($request->request->all());
        return new JsonResponse( $manager->getDatedCountMatrix($dtDateStart, $days) );
    }

    /**
     * @Route("/getCategories", name="getReminderCategories", options={"expose"=true})
     */
    public function getCategories(Request $request) {
        $manager = Cool::getInstance()->getFactory()->getRemindersManager();
        $manager->getParameters()->replace($request->request->all());
        return new JsonResponse( $manager->getCategories() );
    }

    /**
     * @Route("/getParameters", name="NotificationsGetParameters", options={"expose"=true})
     */
    public function getParameters() {
        $manager = Cool::getInstance()->getFactory()->getRemindersManager();
        return new JsonResponse([

        ]);
    }
}