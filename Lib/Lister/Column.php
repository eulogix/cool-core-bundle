<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Lister;

use Symfony\Component\HttpFoundation\ParameterBag;
use Eulogix\Cool\Lib\Form\Field\Field;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class Column {

    const MAX_CHARACTERS = 50;
    const MAX_TOOLTIP_WIDTH = -1;

    /**
    * parameters bag
    * 
    * @var \Symfony\Component\HttpFoundation\ParameterBag
    */
    public $parameters;

    /**
     * @var string
     */
    private $name, $label, $width, $columnStyleCss;

    /**
     * server side template, currently unused
     * @var string
     */
    private $cellTemplate;

    /**
     * contains the handlebars template used to render the content of the cell
     * @var string
     */
    private $cellTemplateJs;

    /**
     * contains the dijit declarative markup used to instance the common dijit widget for that column
     * @see https://github.com/oria/gridx/wiki/How-to-show-widgets-in-gridx-cells%3F
     * @var string
     */
    private $dijitWidgetTemplate;

    /**
     * js function to populate the dijit template
     * @var string
     */
    private $setValueJs;

    /**
     * @var Field
     */
    private $control;

    /**
     * @var boolean
     */
    private $sortable, $editable, $summary, $fixedWidth = false;

    /**
     * @var integer
     */
    private $sortOrder, $maxChars;

    /**
     * @var string
     */
    protected $tooltipJsExpression, $tooltipUrlJsExpression, $tooltipDelay;

    /**
     * @var integer
     */
    protected $tooltipMaxWidth;


    /**
     * @param string $name
     */
    public function __construct($name)
    {
        $this->name = $name;
        $this->parameters = new ParameterBag();
    }

    /**
     * returns an array describing all the column attributes, this array will be used by the Js component to render the column
     * @return array
     */
    public function getDefinition() {
        $def = array(
            'label'=> $this->getLabel(),
            'width'=> $this->getWidth(),
            'fixedWidth'=> $this->getFixedWidth(),
            'cellTemplateJs' => $this->getCellTemplateJs(),
            'dijitWidgetTemplate' => $this->getDijitWidgetTemplate(),
            'setValueJs' => $this->getSetValueJs(),
            'columnStyleCss' => $this->getColumnStyleCss(),
            'editable' => $this->getEditable(),
            'sortable'=> $this->getSortable(),
            'sortOrder'=> $this->getSortOrder(),
            'maxChars'=> $this->getMaxChars()
        );

        if($this->getControl()) {
            $def['control'] = $this->getControl()->getDefinition();
        }

        if($this->parameters->count()>0) {
            $def['parameters'] = $this->parameters->all();
        }

        return $def;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $label
     * @return self
     */
    public function setLabel($label) {
        $this->label = $label;
        return $this;
    }

    /**
     * @return string
     */
    public function getLabel() {
        return $this->label;
    }

    /**
     * @param string $cellTemplate
     * @return self
     */
    public function setCellTemplate($cellTemplate)
    {
        $this->cellTemplate = $cellTemplate;
        return $this;
    }

    /**
     * @return string
     */
    public function getCellTemplate()
    {
        return $this->cellTemplate;
    }

    /**
     * @param string $cellTemplate
     * @return self
     */
    public function setCellTemplateJs($cellTemplate)
    {
        $this->cellTemplateJs = $cellTemplate;
        return $this;
    }

    /**
     * @return string
     */
    public function getCellTemplateJs()
    {
        return $this->cellTemplateJs;
    }

    /**
     * @return string
     */
    public function getDijitWidgetTemplate()
    {
        return $this->dijitWidgetTemplate;
    }

    /**
     * @param string $dijitWidgetTemplate
     * @return $this
     */
    public function setDijitWidgetTemplate($dijitWidgetTemplate)
    {
        $this->dijitWidgetTemplate = $dijitWidgetTemplate;
        return $this;
    }

    /**
     * builds the function setCellValue: function(gridData, storeData, cellWidget)
     * in the body of the js function you have access to the following items
     *
     * -
     *
     * cellWidget refers to the main cell widget, which contains as attach points any other
     * widget you may have instantiated in the cell template eg. cellWidget.myWidget...
     *
     * @see https://github.com/oria/gridx/wiki/How-to-show-widgets-in-gridx-cells%3F
     * @param string $setValueJs
     * @return self
     */
    public function setSetValueJs($setValueJs)
    {
        $this->setValueJs = $setValueJs;
        return $this;
    }

    /**
     * @return string
     */
    public function getSetValueJs()
    {
        $ret = $this->setValueJs;

        if($this->hasTooltip()) {
            $maxWidth = $this->getTooltipMaxWidth() ?? 'null';
            $jsContent = $this->getTooltipJsExpression() ?? 'null';
            $jsUrl = $this->getTooltipUrlJsExpression() ?? 'null';

            $ret .= "\nCOOL.getDialogManager().trackMouseOver(cellWidget.domNode);
                       COOL.getDialogManager().unbindTooltip(cellWidget.domNode);";

            if($this->getTooltipUrlJsExpression())
                 $ret .= "\nCOOL.getDialogManager().bindTooltip(cellWidget.domNode, null, {$maxWidth}, {$jsUrl});";
            else $ret .= "\nCOOL.getDialogManager().bindTooltip(cellWidget.domNode, {$jsContent}, {$maxWidth});";
        }

        if($ret && !$this->getDijitWidgetTemplate()) {
            $ret .= "\ncellWidget.domNode.innerHTML = staticTemplateOutput;";
        }

        return $ret;
    }

    /**
     * @param string $columnStyleCss
     * @return self
     */
    public function setColumnStyleCss($columnStyleCss)
    {
        $this->columnStyleCss = $columnStyleCss;
        return $this;
    }

    /**
     * @param string $columnStyleCss
     * @return self
     */
    public function addColumnStyleCss($columnStyleCss)
    {
        $this->setColumnStyleCss( $this->getColumnStyleCss().$columnStyleCss);
        return $this;
    }

    /**
     * @return string
     */
    public function getColumnStyleCss()
    {
        return $this->columnStyleCss;
    }

    /**
     * @param boolean $editable
     * @return self 
     */
    public function setEditable($editable)
    {
        $this->editable = $editable;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getEditable()
    {
        return $this->editable;
    }

    /**
     * @param boolean $sortable
     * @return self
     */
    public function setSortable($sortable)
    {
        $this->sortable = $sortable;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getSortable()
    {
        return $this->sortable;
    }

    /**
     * @param string $width
     * @return self
     */
    public function setWidth($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * @return string
     */
    public function getWidth()
    {
        return $this->width;
    }

    /**
     * @param boolean $fixedWidth
     * @return self
     */
    public function setFixedWidth($fixedWidth)
    {
        $this->fixedWidth = $fixedWidth;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getFixedWidth()
    {
        return $this->fixedWidth;
    }

    /**
     * @param boolean $summary
     * @return self
     */
    public function setHasSummary($summary)
    {
        $this->summary = $summary;
        return $this;
    }

    /**
     * @return boolean
     */
    public function getHasSummary()
    {
        return $this->summary;
    }

    /**
     * @param \Eulogix\Cool\Lib\Form\Field\Field $control
     * @return self
     */
    public function setControl($control)
    {
        $this->control = $control;
        return $this;
    }

    /**
     * @return \Eulogix\Cool\Lib\Form\Field\Field
     */
    public function getControl()
    {
        return $this->control;
    }

    /**
     * @param int $sortOrder
     * @return self
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;

        return $this;
    }

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @return int
     */
    public function getMaxChars()
    {
        return $this->maxChars;
    }

    /**
     * @param int $maxChars
     * @return $this
     */
    public function setMaxChars($maxChars)
    {
        $this->maxChars = $maxChars;
        if($maxChars) $this->truncate();
        return $this;
    }


    /**
     * @param string $jsContentExpression JS expression returning a string, will be eval'd in setValueJs body
     * @param string $jsUrlExpression JS expression returning a string, will be eval'd in setValueJs body
     * @param int $maxWidth
     * @param int $delay msec
     * @return $this
     */
    public function setTooltip($jsContentExpression, $jsUrlExpression = null, $maxWidth = 300, $delay = 200)
    {
        $this->setTooltipJsExpression($jsContentExpression);
        $this->setTooltipUrlJsExpression($jsUrlExpression);
        $this->setTooltipMaxWidth($maxWidth);
        $this->setTooltipDelay($delay);

        return $this;
    }

    /**
     * @return bool
     */
    public function hasTooltip() {
        return $this->getTooltipJsExpression() || $this->getTooltipUrlJsExpression();
    }

    /**
     * @return int
     */
    public function getTooltipMaxWidth()
    {
        return $this->tooltipMaxWidth;
    }

    /**
     * @param int $tooltipMaxWidth
     * @return $this
     */
    public function setTooltipMaxWidth($tooltipMaxWidth)
    {
        $this->tooltipMaxWidth = $tooltipMaxWidth;
        return $this;
    }

    /**
     * @return string
     */
    public function getTooltipJsExpression()
    {
        return $this->tooltipJsExpression;
    }

    /**
     * @param string $tooltipJsExpression
     * @return $this
     */
    public function setTooltipJsExpression($tooltipJsExpression)
    {
        $this->tooltipJsExpression = $tooltipJsExpression;

        return $this;
    }

    /**
     * @return string
     */
    public function getTooltipUrlJsExpression()
    {
        return $this->tooltipUrlJsExpression;
    }

    /**
     * @param string $tooltipUrlJsExpression
     * @return $this
     */
    public function setTooltipUrlJsExpression($tooltipUrlJsExpression)
    {
        $this->tooltipUrlJsExpression = $tooltipUrlJsExpression;
        return $this;
    }

    /**
     * @return string
     */
    public function getTooltipDelay()
    {
        return $this->tooltipDelay;
    }

    /**
     * @param string $tooltipDelay
     * @return $this
     */
    public function setTooltipDelay($tooltipDelay)
    {
        $this->tooltipDelay = $tooltipDelay;
        return $this;
    }

    //helpers

    /**
     * the column value must be built by the lister processing the rows, and it must contain an array of files
     * @param string $repositoryId
     * @return self
     */
    public function setUpAsRepositoryImageGallery($repositoryId) {
        $this->setDijitWidgetTemplate(
        "<div data-dojo-attach-point='repoGallery'
              data-dojo-type='cool/file/repoGallery'
              data-dojo-props=\"repositoryId: '{$repositoryId}'\"
              class='gridxHasGridCellValue'
              style='height: 50px'
          ></div>")
            ->setWidth(50);
        return $this;
    }

    /**
     * the column value must be built by the lister processing the rows, and it must contain an array of files
     * @param string $repositoryId
     * @return self
     */
    public function setUpAsRepositoryAttachmentButton($repositoryId) {
        $this->setDijitWidgetTemplate(
            "<div data-dojo-attach-point='repoButtonList'
              data-dojo-type='cool/file/repoButtonList'
              data-dojo-props=\"repositoryId: '{$repositoryId}'\"
              class='gridxHasGridCellValue'
              style='height: 50px'
          ></div>")
            ->setWidth(100);
        return $this;
    }

    /**
     * the column value is what comes from datasources that have 1:1 files
     * @param string $repositoryId
     * @return self
     */
    public function setUpAsRepositoryImageThumbnail($repositoryId) {
        $this->setDijitWidgetTemplate(
        "<div data-dojo-attach-point='thumb'
                  data-dojo-type='cool/file/repoThumbnail'
                  data-dojo-props=\"repositoryId: '{$repositoryId}'\"
                  class='gridxHasGridCellValue'
                  style='height: 50px'
              ></div>")
            ->setWidth(80);
        return $this;
    }

    /**
     * @return self
     */
    private function truncate()
    {
        $maxChars = $this->getMaxChars() ? $this->getMaxChars() : self::MAX_CHARACTERS;
        $maxTTWidth = $this->getTooltipMaxWidth() !== null ? $this->getTooltipMaxWidth() : self::MAX_TOOLTIP_WIDTH;
        $this->setDijitWidgetTemplate(
            "<div data-dojo-attach-point='truncator'
                  data-dojo-type='cool/renderers/truncator'
                  data-dojo-props=\"maxChars: {$maxChars}, maxTooltipWidth: {$maxTTWidth}\"
                  class='gridxHasGridCellValue'
              ></div>")
            ->setSetValueJs("
                cellWidget.truncator.set('value', staticTemplateOutput);
            ");
        return $this;
    }

}