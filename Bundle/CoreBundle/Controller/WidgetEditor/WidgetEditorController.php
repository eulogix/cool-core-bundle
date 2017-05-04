<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Controller\WidgetEditor;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class WidgetEditorController extends Controller
{
    /**
     * @Route("/formEditor", name="_coolFormEditor", options={"expose"=true})
     * @Template(engine="php")
     */
    public function formEditorAction()
    {       
        return $this->render('EulogixCoolCoreBundle:WidgetEditor:editor.html.php', array('editorServerId' => 'EulogixCoolCore/WidgetEditor/FormEditor',
            'editorTable' => 'form_config'
        ));
    }

    /**
     * @Route("/listerEditor", name="_coolListerEditor", options={"expose"=true})
     * @Template(engine="php")
     */
    public function listerEditorAction()
    {
        return $this->render('EulogixCoolCoreBundle:WidgetEditor:editor.html.php', array('editorServerId' => 'EulogixCoolCore/WidgetEditor/ListerEditor',
            'editorTable' => 'lister_config'
        ));
    }
}