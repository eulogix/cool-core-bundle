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

class RestFileRepoControllerTest extends WebTestCase
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

    private function getRepositoryId() {
        return "hams_templates";
    }

    public function testGetApiVersion()
    {
        $client = self::getClient();
        $client->request('GET', '/cool/api/filerepo/version');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('0.1', json_decode($client->getResponse()->getContent())->version);
    }

    public function testCreateFolder()
    {
        $client = self::getClient();

        $client->request(
            'POST',
            '/cool/api/filerepo/createFolder',
            [
                'repositoryId'  => $this->getRepositoryId(),
                'repositoryParameters'  => '[]',
                'folderPath'  => '/',

                'folderName'  => 'testfolder'
            ]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testUploadFile()
    {
        $client = self::getClient();

        $client->request(
            'POST',
            '/cool/api/filerepo/upload',
            [
                'repositoryId'  => $this->getRepositoryId(),
                'repositoryParameters'  => '[]',
                'filePath'  => '/testfolder',

                'fileName'  => basename($this->getMockFile(1)),
                'fileContent' => base64_encode(file_get_contents($this->getMockFile(1))),
            ]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        return json_decode($client->getResponse()->getContent(), true)['id'];
    }

    public function testDelete()
    {
        $client = self::getClient();

        $client->request(
            'POST',
            '/cool/api/filerepo/delete',
            [
                'repositoryId'  => $this->getRepositoryId(),
                'repositoryParameters'  => '[]',
                'filePath'  => '/testfolder/'.basename($this->getMockFile(1))
            ]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());

        $client->request(
            'POST',
            '/cool/api/filerepo/delete',
            [
                'repositoryId'  => $this->getRepositoryId(),
                'repositoryParameters'  => '[]',
                'filePath'  => '/testfolder'
            ]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
