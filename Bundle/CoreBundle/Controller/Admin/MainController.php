<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Controller\Admin;

use Eulogix\Cool\Lib\Cool;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class MainController extends Controller
{
    /**
     * @Route("/", name="_coolAdminDesktop")
     * @Template()
     */
    public function mainDesktopAction()
    {       
        return array('connectionsTreeUrl'  =>  $this->get('router')->generate('_getConnectionsTree') );
    }

    /**
     * @Route("/getConnectionsTree", name="_getConnectionsTree")
     * @Template()
     */
    public function getConnectionsTreeAction()
    {
        $arr = array(array(
            'id'=>'_root',
            'name'=>'root'
        ));
        $conns = Cool::getInstance()->getAvailableSchemas();
        foreach($conns as $connName => $schemaProperties)
            if(($db = Cool::getInstance()->getSchema($connName)) && !$db->isMultiTenant()) {
                $arr[] = array(
                    'id'=>$connName,
                    'name'=>$connName,
                    'databaseName'=>$connName,
                    'parent'=>'_root'
                );

                $dict = $db->getDictionary();
                $tblnames = $dict->getTableNames();

                foreach($tblnames as $tblname) {
                    $tableMap = $dict->getPropelTableMap($tblname);

                    if($tableMap->getCoolIsEditable()) {
                        $arr[] = array(
                            'id'=>$connName.'_'.$tblname,
                            'name'=>$tableMap->getCoolRawName(),
                            'databaseName'=>$connName,
                            'tableName'=>$tblname,
                            'defaultLister'=> $tableMap->getCoolDefaultLister(),
                            //'records'=>12,

                            'parent'=>$connName,
                            '_leaf'=>true);
                    }
                }

            }

        return new JsonResponse( $arr );
    }

    /**
     * @Route("/login", name="_coolAdminLogin")
     * @Template()
     */
    public function loginAction()
    {       
        return array();
    }
}