<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Tests\Files;

use Eulogix\Cool\Bundle\CoreBundle\Tests\Cases\baseTestCase;
use Eulogix\Cool\Lib\File\FileSystemFileRepository;
use Eulogix\Cool\Lib\File\FileUtil;
use Eulogix\Lib\File\Proxy\SimpleFileProxy;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FilesTest extends baseTestCase
{

    public function testFsRepo() {
        $tmpDir = $this->createTempDir();
        $frepo = new FileSystemFileRepository($tmpDir);

        $this->assertEquals(7, $frepo->getChildrenOf(null, true)->count());

        $frepo->createFolder('/', 'dirC');

        $this->assertEquals(8, $frepo->getChildrenOf(null, true)->count());

        $fp = new SimpleFileProxy();
        $fp->setName("test.txt")->setContent("hello");

        $storedFile = $frepo->storeFileAt($fp, '/dirC');
        $this->assertEquals(9, $frepo->getChildrenOf(null, true)->count());
        $this->assertEquals($storedFile->getContent(), "hello");
        $this->assertEquals($storedFile->getParentId(), "/dirC");

        $frepo->move("/dirC/test.txt", "/dirA");
        $movedFile = $frepo->get("/dirA/test.txt");
        $this->assertEquals($movedFile->getContent(), "hello");

        $frepo->rename("/dirA/test.txt", "test2.txt");
        $renamedFile = $frepo->get("/dirA/test2.txt");
        $this->assertEquals($renamedFile->getContent(), "hello");

        $this->assertEquals($frepo->search(null, ["name"=>"test2.txt"])->count(), 1);

        exec("rm -rf \"$tmpDir\"");
    }

    private function createTempDir() {
        $tmpDir = FileUtil::getTempFolder();
        mkdir($dirA = $tmpDir.DIRECTORY_SEPARATOR.'dirA');
        mkdir($dirB = $tmpDir.DIRECTORY_SEPARATOR.'dirB');
        touch($tmpDir.DIRECTORY_SEPARATOR.'fileRoot');
        touch($dirA.DIRECTORY_SEPARATOR.'fileA');
        touch($dirA.DIRECTORY_SEPARATOR.'fileA2');
        touch($dirB.DIRECTORY_SEPARATOR.'fileB');
        touch($dirB.DIRECTORY_SEPARATOR.'fileB2');
        return $tmpDir;
    }

}