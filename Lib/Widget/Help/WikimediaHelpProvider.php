<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Widget\Help;

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Form\Field\FieldInterface;
use Eulogix\Cool\Lib\Form\FormInterface;
use Eulogix\Cool\Lib\Traits\WidgetHolder;
use Eulogix\Cool\Lib\Widget\Event\WidgetEvent;
use Eulogix\Cool\Lib\Widget\WidgetInterface;
use Eulogix\Cool\Lib\Wiki\WikiMediaConnector;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class WikimediaHelpProvider implements WidgetHelpProviderInterface
{

    use WidgetHolder;

    /**
     * @var WikiMediaConnector
     */
    protected $wiki;

    public function __construct(WidgetInterface $widget, WikiMediaConnector $wiki) {
        $this->setWidget($widget);
        $this->wiki = $wiki;
        $helper = $this;
        $widget->getDispatcher()->addListener(WidgetInterface::EVENT_DEFINITION_REQUESTED, function(WidgetEvent $e) use ($helper) {
            $helper->decorateWidgetDefinition();
        });
    }

    /**
     * @param array $parameters
     * @return string
     */
    public function getHelp(array $parameters)
    {
        $widget = $this->getWidget();

        if($this->getWidget() instanceof FormInterface) {
            /**
             * @var FormInterface $widget
             */
            if(isset($parameters['fieldName'])) {
                $page = $this->wiki->getPage( $this->getProcessedWikiPageTitle( $widget->getWikiHelpPage() ) );
                $fieldName = $widget->getField($parameters['fieldName'])->getLabel();
                $fieldHelp = trim($page->getSection($fieldName));
                $fieldHelp = $this->wiki->parseAsHTML( $fieldHelp );
                return $fieldHelp ?? null;
            }
        }
    }

    public function decorateWidgetDefinition()
    {
        $widget = $this->getWidget();

        if($this->getWidget() instanceof FormInterface) {
            /**
             * @var FormInterface $widget
             */
            $page = $this->wiki->getPage( $this->getProcessedWikiPageTitle( $widget->getWikiHelpPage() ) );
            foreach($widget->getFields() as &$field) {
                /**
                 * @var FieldInterface $field
                 */
                $field->getParameters()->set(FieldInterface::PROP_HAS_HELP, $page->getSection($field->getLabel()) !== false );
            }
        }
    }

    /**
     * @param string $pageTitleTemplate
     * @throws \Exception
     */
    public function populateFormFieldsPage($pageTitleTemplate = null)
    {
        /**
         * @var FormInterface $form
         */
        $form = $this->getWidget();

        $pageTitle = $pageTitleTemplate ?? $form->getWikiHelpPage();

        $processedTitle = $this->getProcessedWikiPageTitle($pageTitle);

        if(!$this->wiki->isPageTitleValid($processedTitle))
            throw new \Exception("Page title $processedTitle is not valid");

        $page = $this->wiki->getPage( $processedTitle );

        if(!$page->exists()) {
            $page->setText("==Form docs==\n\n Blank form doc page for {$form->getTitle()}");
        }

        /**
         * @var FieldInterface $field
         */
        foreach($form->getFields() as $field) {
            $sectionName = $field->getLabel();
            if(!$page->getSection($sectionName))
                $page->newSection($sectionName, "Doc for {$field->getName()} ($sectionName)");
        }
    }

    /**
     * @param $twigPage
     * @return string
     */
    public function getProcessedWikiPageTitle($twigPage) {
        $twig = Cool::getInstance()->getFactory()->getTwig();
        $twigTemplate = $twig->createTemplate($twigPage);

        $processedPage = $twigTemplate->render([
            'locale' => $this->getWidget()->getTranslator()->getLocale(),
            'title' => $this->getWidget()->getTitle(),
            'coolTranslator' => Cool::getInstance()->getFactory()->getGlobalTranslator()
        ]);

        return $processedPage;
    }


}