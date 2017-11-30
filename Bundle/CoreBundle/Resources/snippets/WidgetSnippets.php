<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\Resources\snippets;

use Eulogix\Cool\Lib\Widget\WidgetInterface;

use Eulogix\Cool\Lib\Annotation\SnippetMeta;

class WidgetSnippets
{
    /**
     * @SnippetMeta(category="widget_variable", contextIgnore={"widget"}, directInvocation="true", description="Get PARAMETER value")
     *
     * @param WidgetInterface $widget
     * @param string $parameterName the name of the parameter to fetch
     * @return string
     */
    public static function getParameterValue(WidgetInterface $widget, $parameterName)
    {
        return $widget->getParameters()->get($parameterName);
    }

    /**
     * @SnippetMeta(category="widget_variable", contextIgnore={"widget"}, directInvocation="true", description="Get ATTRIBUTE value")
     *
     * @param WidgetInterface $widget
     * @param string $attributeName the name of the attribute to fetch
     * @return string
     */
    public static function getAttributeValue(WidgetInterface $widget, $attributeName)
    {
        return $widget->getAttributes()->get($attributeName);
    }

    /**
     * @SnippetMeta(category="widget_action", contextIgnore={"widget"}, directInvocation="true", description="Output message")
     *
     * @param WidgetInterface $widget
     * @param string $messageType Can be info, warning or error. defaults to info
     * @param string $message the message
     */
    public static function outputMessage(WidgetInterface $widget, $messageType, $message)
    {
        switch (strtolower($messageType)) {
            case 'error' :
                $mt = \Eulogix\Cool\Lib\Widget\Message::TYPE_ERROR;
                break;
            case 'warning' :
                $mt = \Eulogix\Cool\Lib\Widget\Message::TYPE_WARNING;
                break;
            default :
                $mt = \Eulogix\Cool\Lib\Widget\Message::TYPE_INFO;
                break;
        }

        $widget->addMessage($mt, $message);
    }

    /**
     * @SnippetMeta(category="widget_action", contextIgnore={"widget"}, directInvocation="true", description="Remove action button")
     *
     * @param WidgetInterface $widget
     * @param string $actionName the name of the action button
     */
    public static function removeActionButton(WidgetInterface $widget, $actionName)
    {
        $widget->removeAction($actionName);
    }

    /**
     * @SnippetMeta(category="widget_action", contextIgnore={"widget"}, directInvocation="true", description="Set whole widget to Read Only")
     *
     * @param WidgetInterface $widget
     */
    public static function setReadOnly(WidgetInterface $widget)
    {
        $widget->setReadOnly(true);
    }

    /**
     * @SnippetMeta(category="widget_variable", contextIgnore={"widget"}, directInvocation="true", description="Get last called action NAME", longDescription="gets the name of the last called action (if any)")
     *
     * @param WidgetInterface $widget
     *
     * @return string
     */
    public static function getLastCalledActionName(WidgetInterface $widget)
    {
        return $widget->getLastCalledAction();
    }

    /**
     * @SnippetMeta(category="widget_action", contextIgnore={"widget"}, directInvocation="true", description="Set variation", longDescription="Sets the variation to be picked by the configurator")
     *
     * @param WidgetInterface $widget
     * @param string $variation The name of the variation
     */
    public static function setVariation(WidgetInterface $widget, $variation)
    {
        $widget->setCurrentVariation($variation);
    }

    /**
     * @SnippetMeta(category="widget_action", contextIgnore={"widget"}, directInvocation="true", description="Add ACTION", longDescription="Adds an action to the widget")
     *
     * @param WidgetInterface $widget
     * @param string $actionName The name of the action
     * @param string $icon (optional) an icon. specify the name of the icon from FUGUE
     * @param string $confirmationMessage (optional) a confirmation message that appears before submitting the action. Set to 1 for a translated message, or use a custom string
     * @param string $JSONParameters (optional) JSON object containing parameters which will be sent along with the action call
     *
     * @return bool
     */
    public static function addAction(
        WidgetInterface $widget,
        $actionName,
        $icon,
        $confirmationMessage,
        $JSONParameters
    ) {
        $action = $widget->addAction($actionName);

        $onClick = $JSONParameters ? "widget.callAction('$actionName', null, {$JSONParameters});" : "widget.callAction('$actionName');";

        if ($confirmationMessage) {
            $action->setConfirmedOnClick($onClick, $confirmationMessage == '1' ? null : $confirmationMessage);
        } else {
            $action->setOnClick($onClick);
        }

        if ($icon) {
            $action->setIcon("/bower_components/fugue/icons/{$icon}.png");
        }

        return true;
    }

    /**
     * @SnippetMeta(category="widget_action", contextIgnore={"widget"}, directInvocation="true", description="Workflows - open the pending task form of a running process ID", longDescription="If the logged user can claim or complete the pending task of the process, a popup form will be shown containing the task form")
     *
     * @param WidgetInterface $widget
     * @param string $processInstanceId The numeric ID of the process instance, or execution ID
     */
    public static function openThePendingTaskFormOfARunningProcessId(
        WidgetInterface $widget,
        $processInstanceId
    ) {
        $wfEngine = \Eulogix\Cool\Lib\Cool::getInstance()->getFactory()->getWorkflowEngine();

        $process = $wfEngine->getClient()->getProcessInstance($processInstanceId);

        $pi = new \Eulogix\Lib\Activiti\om\ProcessInstance($process, $wfEngine->getClient());

        $wfEngine->popupTaskFormForCurrentUser($pi, $widget);
    }

    /**
     * @SnippetMeta(category="widget_variable", contextIgnore={"widget"}, directInvocation="true", description="Get ACTION request parameter", longDescription="Returns a parameter in the REQUEST, used when executing actions")
     *
     * @param WidgetInterface $widget
     * @param string $parameterName the name of the parameter to fetch
     *
     * @return string
     */
    public static function getActionRequestParameter(WidgetInterface $widget, $parameterName)
    {
        return $widget->request->get($parameterName);
    }

    /**
     * @SnippetMeta(category="widget_action", contextIgnore={"widget"}, directInvocation="true", description="Rebuild", longDescription="Rebuilds the widget")
     *
     * @param WidgetInterface $widget
     * @param string $sorthack put here the name of the last processed variable to ensure that this snippets gets executed last
     */
    public static function rebuild(WidgetInterface $widget, $sorthack)
    {
        $widget->reBuild();
    }
}