<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Tests\Template;

use Eulogix\Cool\Bundle\CoreBundle\Tests\Cases\baseTestCase;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Template\TwigTemplate;
use Eulogix\Lib\File\Proxy\SimpleFileProxy;
use Eulogix\Lib\File\ZipUtils;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class TemplateTest extends baseTestCase
{
    public function testTwigTemplates() {
        $twigTpl = new TwigTemplate();

        $twigTpl->setTemplateFile(SimpleFileProxy::fromFileSystem(__DIR__.'/res/simpleTemplate.html.twig'))
            ->setData([
                'simpleVar' => 'hello'
            ]);

        $htmlOutput = $twigTpl->getRenderedOutput();

        $this->assertEquals('fixedContenthello', $htmlOutput->getContent());

        $pdfOutput = $twigTpl->getRenderedOutput('pdf');

        $this->assertTrue($pdfOutput->getSize() > 0);
        $this->assertTrue($pdfOutput->getSize() != $htmlOutput->getSize());

        $zippedTemplate = ZipUtils::zipFolder(__DIR__.'/res/zippedTemplate');
        $renderer = Cool::getInstance()->getFactory()->getTemplateRendererFactory()->getRendererFor($zippedTemplate);
        $renderer->setData($twigTpl->getData());
        $pdfOutput = $renderer->getRenderedOutput('pdf');

        $zippedTemplate->clear();

        $this->assertTrue($pdfOutput->getSize() > 0);
        $this->assertTrue($pdfOutput->getSize() != $htmlOutput->getSize());
    }
}
