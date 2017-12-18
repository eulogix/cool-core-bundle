<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class RestFilesControllerTest extends WebTestCase
{

    private static function getClient() {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'demo',
        ]);
        return $client;
    }

    private function getMockFile($id) {
        switch($id) {
            case 1:     return __DIR__.'/data/attachment 1.pdf';
            case 2:     return __DIR__.'/data/attachment 2.xlsx';
        }
    }

    public function testGetApiVersion()
    {
        $client = self::getClient();
        $client->request('GET', '/cool/api/files/version');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('0.1', json_decode($client->getResponse()->getContent())->version);
    }

    public function testSearch()
    {
        $client = self::getClient();
        $client->request('POST', '/cool/api/files/search',
            [
                'schemaName'  => 'core',
                'actualSchema'  => 'core',
                //'table'  => 'account'
            ]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);

        $total = $data['total_count'];
        $pages = $data["pages_count"];

        $fetched = 0;
        for($i=0;$i<$pages;$i++) {

            $client = self::getClient();
            $client->request('POST', '/cool/api/files/search',
                [
                    'schemaName'  => 'core',
                    'actualSchema'  => 'core',
                    'page' => $i
                ]);
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $data = json_decode($client->getResponse()->getContent(), true);
            //echo "Page $i/$pages : ".count($data['files'])."\n";
            print_r($data["files"]);
            $fetched+=count($data['files']);
        }

        $this->assertEquals($total, $fetched);
        $this->assertTrue($total > 0);
        $this->assertTrue($pages > 0);
    }

    public function testGetAvailableProperties()
    {
        $client = self::getClient();
        $client->request('POST', '/cool/api/files/getAvailableProperties',
            [
                'schemaName'  => 'core',
                //'actualSchema'  => 'core',
                //'table'  => 'account'
            ]);
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $data = json_decode($client->getResponse()->getContent(), true);
    }

    public function testUploadFile()
    {
        $client = self::getClient();

        $client->request(
            'POST',
            '/cool/api/files/upload',
            [
                'schemaName'  => 'core',
                'actualSchema'  => 'core',
                'table'  => 'account',
                'pk'  => 1,

                'fileName'  => basename($this->getMockFile(1)),
                'fileContent' => base64_encode("lf"),
                //'fileContent' => base64_encode(file_get_contents($this->getMockFile(1))),
                'category' => 'UNCATEGORIZED'
            ]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        return json_decode($client->getResponse()->getContent(), true)['id'];
    }

    /**
     * @depends testUploadFile
     */
    public function testSetGetProperties($fileId)
    {

        //------------- basic property setting ----------------
        $client = self::getClient();

        $client->request(
            'POST',
            '/cool/api/files/setProperties',
            [
                'schemaName'  => 'core',
                'actualSchema'  => 'core',
                'fileId'  => $fileId,
                'fileProperties' => json_encode([
                        'prop1' => 'val1',
                        'prop2' => 'val2'
                    ]),
            ]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //------------- basic property getting ----------------
        $client = self::getClient();

        $client->request(
            'POST',
            '/cool/api/files/getProperties',
            [
                'schemaName'  => 'core',
                'actualSchema'  => 'core',
                'fileId'  => $fileId,
            ]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $properties = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(2, count($properties));
        $this->assertEquals('val1', $properties['prop1']);


        //------------- merged property setting ----------------
        $client = self::getClient();

        $client->request(
            'POST',
            '/cool/api/files/setProperties',
            [
                'schemaName'  => 'core',
                'actualSchema'  => 'core',
                'fileId'  => $fileId,
                'merge' => 1,
                'fileProperties' => json_encode([
                    'prop1' => 'val11',
                    'prop3' => 'val3'
                ]),
            ]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        //------------- final check ----------------
        $client = self::getClient();

        $client->request(
            'POST',
            '/cool/api/files/getProperties',
            [
                'schemaName'  => 'core',
                'actualSchema'  => 'core',
                'fileId'  => $fileId,
            ]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $properties = json_decode($client->getResponse()->getContent(), true);

        $this->assertEquals(3, count($properties));

        $this->assertEquals('val11', $properties['prop1']);
        $this->assertEquals('val2', $properties['prop2']);
        $this->assertEquals('val3', $properties['prop3']);

    }

    public function _getFilesInFolder()
    {
        $client = self::getClient();

        $client->request(
            'POST',
            '/cool/api/files/getRecordFiles',
            [
                'schemaName'  => 'core',
                'actualSchema'  => 'core',
                'table'  => 'account',
                'pk'  => 1,
                'recursive' => '1'
            ]
        );

        return json_decode($client->getResponse()->getContent(), true);
    }

    public function _getUploadedFile()
    {

        $data = $this->_getFilesInFolder();

        $this->assertTrue( count($data) > 1);

        foreach($data as $f) {
            if($f['name'] == 'attachment 1.pdf')
                return $f['id'];
        }

        return null;
    }


    public function testDeleteFile()
    {

        $fileToDelete = $this->_getUploadedFile();
        $this->assertTrue( $fileToDelete !== null );

        $client = self::getClient();

        $client->request(
            'POST',
            '/cool/api/files/delete',
            [
                'schemaName'  => 'core',
                'actualSchema'  => 'core',
                'fileId'  => $fileToDelete
            ]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $deletedFile = $this->_getUploadedFile();
        $this->assertTrue( $deletedFile === null );

    }

    public function testUploadFileMultiple()
    {

        echo "**** SKIP ****\n\n";

        for($i=0; $i<10; $i++) {
            $client = self::getClient();
            $client->request(
                'POST',
                '/cool/api/files/upload',
                [
                    'schemaName' => 'core',
                    'actualSchema' => 'core',
                    'table' => 'account',
                    'pk' => 1,
                    'fileName' => basename($this->getMockFile(1)),
                    'fileContent' => base64_encode("lf"),
                    'category' => 'UNCATEGORIZED',

                    'collisionStrategy' => 'skip'
                ]
            );
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            echo $client->getResponse()->getContent()."\n";
            $uploadedFileId = json_decode($client->getResponse()->getContent(), true)['id'];

            if($i==0) {
                $this->assertTrue($uploadedFileId != "-1");
                $firstUploadedId = $uploadedFileId;
            }
            else $this->assertTrue( $uploadedFileId == "-1");

            echo $uploadedFileId."\n";
        }

        echo "**** OVERWRITE ****\n";

        for($i=0; $i<10; $i++) {
            $client = self::getClient();
            $client->request(
                'POST',
                '/cool/api/files/upload',
                [
                    'schemaName' => 'core',
                    'actualSchema' => 'core',
                    'table' => 'account',
                    'pk' => 1,
                    'fileName' => basename($this->getMockFile(1)),
                    'fileContent' => base64_encode("lf"),
                    'category' => 'UNCATEGORIZED',

                    'collisionStrategy' => 'overwrite'
                ]
            );
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $uploadedFileId = json_decode($client->getResponse()->getContent(), true)['id'];

            $this->assertEquals($firstUploadedId, $uploadedFileId);

            echo $uploadedFileId."\n";
        }

        echo "**** APPEND ****\n";

        for($i=0; $i<10; $i++) {
            $client = self::getClient();
            $client->request(
                'POST',
                '/cool/api/files/upload',
                [
                    'schemaName' => 'core',
                    'actualSchema' => 'core',
                    'table' => 'account',
                    'pk' => 1,
                    'fileName' => basename($this->getMockFile(1)),
                    'fileContent' => base64_encode("lf"),
                    'category' => 'UNCATEGORIZED',

                    'collisionStrategy' => 'append'
                ]
            );
            $this->assertEquals(200, $client->getResponse()->getStatusCode());
            $uploadedFileId = json_decode($client->getResponse()->getContent(), true)['id'];
            $uploadedFileName = json_decode($client->getResponse()->getContent(), true)['name'];

            $this->assertNotEquals($firstUploadedId, $uploadedFileId);

            echo "$uploadedFileId -> $uploadedFileName\n";
        }

    }

}
