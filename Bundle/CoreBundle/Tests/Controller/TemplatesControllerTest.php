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

class TemplatesControllerTest extends WebTestCase
{

    private static function getClient() {
        $client = static::createClient([], [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW'   => 'demo',
        ]);
        return $client;
    }

    private function getRepositoryId() {
        return "hams_templates";
    }

    public function testGetApiVersion()
    {
        $client = self::getClient();
        $client->request('GET', '/cool/api/templates/version');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
        $this->assertEquals('0.1', json_decode($client->getResponse()->getContent())->version);
    }

    public function testRender()
    {
        $client = self::getClient();

        $client->request(
            'POST',
            '/cool/api/templates/render',
            [
                'repositoryId'  => $this->getRepositoryId(),
                'repositoryParameters'  => '{}',
                'templatePath'  => '/simpleTemplate.html.twig',
                'templateData'  => '{"simpleVar":"hello2"}',
                'outputFormat'  => 'pdf',
                'rendererParameters'  => '{}',
            ]
        );

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
