<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Tests\Lib\Graph;

use Eulogix\Lib\Graph\Graph;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class GraphTest extends \PHPUnit_Framework_TestCase
{

    public function testVertexSort()
    {
        $g = $this->getGraph();
        print_r($g->getVertices());
        $g->TopologicalVertexSort();
        print_r($g->getVertices());
    }

    /**
     * @return Graph
     */
    private function _getGraph() {
        $g = new Graph();
        $g->addVertex(1);
        $g->addVertex(2);
        $g->addVertex(3);
        $g->addVertex(4);
        $g->addVertex(5);
        $g->addEdge(null, 4, 4);
        $g->addEdge(null, 4, 3);
        $g->addEdge(null, 4, 2);
        $g->addEdge(null, 3, 2);
        $g->addEdge(null, 3, 1);
        $g->addEdge(null, 2, 1);
        $g->addEdge(null, 1, 5);
        $g->addEdge(null, 1, 2);
        return $g;
    }

    /**
     * @return Graph
     */
    private function getGraph() {
        $data = ['aprop'=>'avalue'];
        $g = new Graph();
        $g->addVertex('A',$data);
        $g->addVertex('B',$data);
        $g->addVertex('C',$data);
        $g->addVertex('D',$data);
        $g->addEdge(null, 'D', 'C');
        $g->addEdge(null, 'D', 'B');
        $g->addEdge(null, 'B', 'C');
        $g->addEdge(null, 'B', 'A');
        return $g;
    }
}
