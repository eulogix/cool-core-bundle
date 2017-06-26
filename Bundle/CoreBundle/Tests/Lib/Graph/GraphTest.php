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
        $g = $this->getTSGraph();
        $g->TopologicalVertexSort();
        $this->assertEquals(['D','B','C','A'], array_keys($g->getVertices()));
    }

    public function testSC()
    {
        $g = $this->getSCGraph();
        $sc = $g->getStronglyConnectedComponents();
        $this->assertEquals(['H' => ['H','I','G'], 'C' => ['C','J','F','D']], $sc);
    }

    /**
     * @return Graph
     */
    private function getTSGraph() {
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

    /**
     * @return Graph
     * @see https://stackoverflow.com/questions/33590974/how-to-find-strongly-connected-components-in-a-graph
     */
    private function getSCGraph() {
        $data = ['aprop'=>'avalue'];
        $g = new Graph();
        $g->addVertex('A',$data);
        $g->addVertex('B',$data);
        $g->addVertex('C',$data);
        $g->addVertex('D',$data);
        $g->addVertex('E',$data);
        $g->addVertex('F',$data);
        $g->addVertex('G',$data);
        $g->addVertex('H',$data);
        $g->addVertex('I',$data);
        $g->addVertex('J',$data);
        $g->addEdge(null, 'C', 'D');
        $g->addEdge(null, 'A', 'C');
        $g->addEdge(null, 'A', 'H');
        $g->addEdge(null, 'J', 'C');
        $g->addEdge(null, 'F', 'J');
        $g->addEdge(null, 'D', 'F');
        $g->addEdge(null, 'H', 'F');
        $g->addEdge(null, 'H', 'G');
        $g->addEdge(null, 'I', 'H');
        $g->addEdge(null, 'E', 'A');
        $g->addEdge(null, 'E', 'I');
        $g->addEdge(null, 'B', 'A');
        $g->addEdge(null, 'B', 'G');
        $g->addEdge(null, 'G', 'I');
        return $g;
    }

}
