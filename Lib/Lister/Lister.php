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

use Eulogix\Cool\Bundle\CoreBundle\CWidget\WidgetEditor\BaseConfigEditorForm;
use Eulogix\Cool\Bundle\CoreBundle\CWidget\WidgetEditor\WidgetEditor;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\DataSource\DataSourceInterface;
use Eulogix\Cool\Lib\DataSource\DSField;
use Eulogix\Cool\Lib\DataSource\DSRequest;
use Eulogix\Cool\Lib\DataSource\Renderer\ExcelRenderer;
use Eulogix\Cool\Lib\DataSource\Renderer\RendererInterface;
use Eulogix\Cool\Lib\Dojo\ListerStore;
use Eulogix\Cool\Lib\Dojo\XhrStoreRequest;
use Eulogix\Cool\Lib\File\FileProxyCollectionInterface;
use Eulogix\Cool\Lib\File\FileProxyInterface;
use Eulogix\Cool\Lib\File\FileRepositoryInterface;
use Eulogix\Cool\Lib\File\FileRepositoryPreviewProvider;
use Eulogix\Cool\Lib\File\SimpleFileProxy;
use Eulogix\Cool\Lib\Form\Form;
use Eulogix\Lib\Error\ErrorReport;
use Eulogix\Cool\Lib\Lister\Configurator\ListerConfigurator;
use Eulogix\Cool\Lib\Widget\Widget;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class Lister extends Widget implements ListerInterface {

    protected $type = "lister";
    
    /**
    * returns an array describing all the lister columns and settings. This array is used by the Js Lister component to render the lister on the client
    * @var Column[]
    */
    protected $columns = [];
    
    /**
     * @var array
     */
    protected $initialSort = [];

    /**
    * returns an array of parameters that influence the client component that is instantiated to render the lister
    * 
    */
    public function getClientParameters() {
        $p = parent::getClientParameters();
        if($eid = $this->getEditorServerId()) {
            $p['editorServerId'] = $eid;
        }
        return $p;
    }

    /**
     * @inheritdoc
     */
    public function getDefinition() {

        $this->attributes->set(self::ATTR_INITIAL_SORT, $this->getInitialSort());

        $d = parent::getDefinition();
         
        if(!empty($cols = $this->getColumnsDefinition())) {
            $d->setBlock('columns', $cols);
        }

        return $d;
    }

    /**
     * @return array
     */
    protected function getColumnsDefinition() {
        $colsDef = [];
        $columns = $this->getColumnsSorted();
        foreach($columns as $name => $col) {
            $colsDef[$name] = $col->getDefinition();
        }
        return $colsDef;
    }

    /**
     * @return Column[]
     */
    protected function getColumnsSorted() {
        $columns = $this->getColumns();
        uasort($columns, function($a, $b) {
                /** @var Column $a */
                /** @var Column $b */
                return $a->getSortOrder() > $b->getSortOrder() ? 1 : -1;
            });
        return $columns;
    }

    /**
     * @inheritdoc
     */
    public function build() {
        parent::build();
        $this->addDataSourceColumns();
        $this->setInitialSortFromDS();
        if($this->hasVariableEditorServerId()) {
            $this->attributes->set(self::ATTR_ROW_EDIT_FUNCTION, 'function(editorWidgetParameters, rowData){ widget.callAction("openVariableEditor", null, { editorWidgetParameters: JSON.stringify(editorWidgetParameters), rowData: JSON.stringify(rowData) }); };');
        }

        $this->attributes->set(self::ATTR_DELETE_MULTIPLE, @$this->getDataSource()->getMeta()[DataSourceInterface::META_CAN_DELETE_MULTIPLE] == true);
        $this->attributes->set(self::ATTR_EXPORT_XLSX, @$this->getDataSource()->getMeta()[DataSourceInterface::META_CAN_EXPORT_XLSX] == true);
        return $this;
    }

    public function onOpenVariableEditor() {
        $editorWidgetParameters = json_decode($this->getRequest()->get('editorWidgetParameters'), true);
        $rowData = json_decode($this->getRequest()->get('rowData'), true);
        $editorServerId = json_encode($this->getEditorServerId($editorWidgetParameters, $rowData));
        $this->addCommandJs("widget._openWidgetAsRowEditor($editorServerId, ".json_encode($editorWidgetParameters).");");
    }

    /**
    * @inheritdoc
    */
    public function addNewColumn($columnName) {
        $c = new Column($columnName);
        $c->setLabel( $this->getTranslator()->trans($columnName) );
        //a safe default that puts every column with a specified sort order in front of any default ordered column
        $c->setSortOrder( 1000 + count($this->getColumnNames())*100 );
        return $this->columns[$columnName] = $c;
    }

    /**
    * @inheritdoc
    */
    public function addColumn($column) {
        //a safe default that puts every column with a specified sort order in front of any default ordered column
        $column->setSortOrder( 1000 + count($this->getColumnNames())*100 );
        $this->columns[$column->getName()] = $column;
        return $column;
    }

    /**
    * @inheritdoc
    */
    public function removeColumn($columnName) {
        if(isset($this->columns[$columnName])) {
            unset($this->columns[$columnName]);
            return true;
        }
        return false;
    }
    
    /**
    * @inheritdoc
    */
    public function getColumn($columnName) {       
        if(isset($this->columns[$columnName])) {
            return $this->columns[$columnName];
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function getColumns() {
        return $this->columns;
    }

    /**
     * @inheritdoc
     */
    public function getColumnNames() {
        return array_keys($this->getColumns());
    }

    /**
     * @inheritdoc
     */
    public function propagateColumn($columnName, $as=null) {
        $propagatedCols = $this->getPropagatedColumns();
        $propagatedCols[$columnName] = ['as' => $as ? $as : $columnName];
        $this->attributes->set(self::ATTR_PROPAGATED_FIELDS, $propagatedCols);
    }

    /**
     * @inheritdoc
     */
    public function isColumnPropagated($columnName) {
        return isset($this->getPropagatedColumns()[$columnName]);
    }

    /**
     * @return array
     */
    public function getPropagatedColumns() {
        $ret =  $this->attributes->get(self::ATTR_PROPAGATED_FIELDS);
        return $ret ? $ret : [];
    }

    /**
     * @inheritdoc
     */
    public function getConfigurator() {
        $c = new ListerConfigurator( $this );
        return $c;
    }


    /**
     * @inheritdoc
     */
    public function setFilterSlot( $slot ) {
        $this->setSlot('filterSlot', $slot);
    }
    
    /**
    * @inheritdoc
    */
    public function addDataSourceColumns() {
        if( ($ds = $this->getDataSource()) && ($fields = $ds->getFields())) {
            $form = new Form();
            foreach($fields as $dsField) {

                //hack: TODO refactor this so that fieldFactory is a service
                $formField = $form->fieldFactory( $dsField->getControlType(), $dsField->getValueMap() );

                $column = $this->getDefaultColumnForDsField($dsField);
                $column->setControl($formField);

                $this->addColumn($column);
            }
        }
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultColumnForDsField($dsField) {
        $name = $dsField->getName();
        $c = new Column($name);
        $c->setLabel( $this->getTranslator()->trans($name) );

        $css = [];
        switch($dsField->getMacroType()) {
            case DSField::MACRO_TYPE_STRING  : {
                if(!$dsField->getValueMap())
                    $c->setMaxChars(Column::MAX_CHARACTERS);
                break;
            }
            case DSField::MACRO_TYPE_DATETIME : {
                $css[] = "text-align: right;"; break;
            }
            case DSField::MACRO_TYPE_NUMERIC : {
                //value mapped fields are usually represented as strings
                if(!$dsField->getValueMap())
                    $css[] = "text-align: right;";
                break;
            }
            case DSField::MACRO_TYPE_BOOLEAN : {
                $c->setCellTemplateJs('{{{ boolIcon _value}}}');
                $css[] = "text-align: center;";
                break;
            }
        }
        $c->setColumnStyleCss(implode(' ',$css));


        return $c;
    }

    /**
     * @inheritdoc
     */
    public function setShowToolsColumn($showToolsColumn)
    {
        $this->attributes->set(self::ATTR_SHOW_TOOLS_COLUMN, $showToolsColumn);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getShowToolsColumn()
    {
        return $this->attributes->get(self::ATTR_SHOW_TOOLS_COLUMN);
    }

    /**
     * @inheritdoc
     */
    public static function getClientWidget() {
        return 'cool/lister';
    }

    /**
     * @inheritdoc
     */
    public function getEditorServerId($editorWidgetParameters=null, $rowData=null) {
        return $this->getDefaultEditorServerId();
    }

    /**
     * @inheritdoc
     */
    public function getDefaultEditorServerId() {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function setHasVariableEditorServerId($bool)
    {
        $this->attributes->set(self::ATTR_HAS_VARIABLE_EDITOR, $bool);
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function hasVariableEditorServerId()
    {
        return $this->attributes->get(self::ATTR_HAS_VARIABLE_EDITOR);
    }

    /**
     * @return string|null
     */
    public function getDefaultFilterWidget()
    {
        return 'Eulogix\Cool\Lib\Lister\Filter\BaseFilterForm';
    }

    /**
     * @return array
     */
    public function getInitialSort()
    {
        return $this->initialSort;
    }

    /**
     * @param array $initialSort
     * @return $this
     */
    public function setInitialSort($initialSort)
    {
        $this->initialSort = $initialSort;
        return $this;
    }

    /* basic actions implementation */

    public function onDeleteRows() {
        if( $ids = $this->request->get('ids') ) {

            $deletedOk = $deletedError = [];
            $ids = explode(',',$ids);

            foreach($ids as $id) {
                $errors = $this->deleteRow($id);

                if(!$errors->hasErrors()) {
                    $deletedOk[] = $id;
                } else {
                    $deletedError[] = $id;
                    $this->mergeErrorReport($errors);
                }
            }

            if($deletedOk && !$deletedError)
                $this->addMessageInfo(count($deletedOk)." ITEMS DELETED ");
            elseif($deletedOk)
                $this->addMessageWarning(count($deletedOk)."/".(count($deletedError)+count($deletedOk))." ITEMS DELETED");
            else $this->addMessageInfo("NOTHING DONE");

            if($deletedOk || $deletedError) {
                $this->addCommandJs("widget.refreshAfterDelete();");
            }
        }
    }

    public function onDeleteRow() {
        if( $id = $this->request->get(DataSourceInterface::RECORD_IDENTIFIER) ) {

            $errors = $this->deleteRow($id);

            if(!$errors->hasErrors()) {
                $this->addMessageInfo("RECORD $id DELETED.");
                $this->addCommandJs("widget.refreshAfterDelete();");
            } else {
                $this->addMessageError("RECORD $id COULD NOT BE DELETED.");
                $this->mergeErrorReport($errors);
            }
        }
    }

    public function onExport() {

        Cool::getInstance()->freeSession();

        $format = $this->request->get("format");
        $raw = $this->request->has("raw");

        $f = new SimpleFileProxy();
        $cleanFileName = preg_replace('/[^a-z0-9-\[\]]/sim','',$this->getTitle());
        $f->setName( $cleanFileName.".".$format );

        $data = $this->getExportData();
        if(count($data) > 0 && ($renderer = $this->getRenderer($format)) ) {
            $f->setContent( $renderer->renderData($data, $raw, $this->getColumnsSorted()) );
        } else {
            $this->addMessageError("NOTHING TO EXPORT");
            return;
        }

        $this->downloadFile($f);
    }

    /**
     * @param string $format
     * @return null|RendererInterface
     */
    public function getRenderer($format) {
        switch($format) {
            case 'xlsx': return new ExcelRenderer();
        }
        return null;
    }

    /**
     * @return array
     */
    protected function getLastStoreRequest() {
        $ret = json_decode($this->getRequest()->get('_lastDojoStoreRequest'), true);
        return is_array($ret) ? $ret : [];
    }

    /**
     * @return array
     */
    protected function getFilterRawValues() {
        $lastStoreRequest = $this->getLastStoreRequest();
        return isset($lastStoreRequest['postData']['_filter_raw_values']) ? json_decode($lastStoreRequest['postData']['_filter_raw_values'],true) : [];
    }

    /**
     * @param string $name
     * @return mixed
     */
    protected function getFilterRawValue($name) {
        $values = $this->getFilterRawValues();
        return @$values[$name];
    }

    /**
     * Derives a valid DSRequest object by reusing the last dojo request of the lister
     * @return DSRequest
     */
    protected function getLastDSRequest() {

        $dojoStore = new ListerStore( $this );
        $lastStoreRequest = $this->getLastStoreRequest();
        $dojoRequest = XhrStoreRequest::fromGetAndPostArrays( $lastStoreRequest['getData'], $lastStoreRequest['postData']);
        $dsRequest = $dojoStore->getQueryDSRequest($dojoRequest);
        return $dsRequest;
    }

    /**
     * produces an array of rows which will be exported
     * @return array
     */
    protected function getExportData() {
        $dsRequest = $this->getLastDSRequest();
        $dsRequest->setIncludeMeta(false);
        $dsRequest->setStartRow(0);
        $dsRequest->setEndRow($this->getExportDataMaxRows()); //limit result to 10k rows. set to null to get all the rows

        if( $ds = $this->getDataSource() ) {
            $dsresponse = $ds->execute($dsRequest);
            if($dsresponse->getStatus() == $dsresponse::STATUS_TRANSACTION_SUCCESS) {
                return $dsresponse->getData();
            }
        }

        return [];
    }

    /**
     * produces an array of record ids that only contain the primary keys of the DS, useful for bulk operations
     * @param bool $onlyCurrentPage
     * @return array
     */
    public function getAllIds($onlyCurrentPage = false) {
        $ds = clone $this->getDataSource();

        $del = [];
        foreach($ds->getFields() as $field)
            if(!($field->isPrimaryKey() || $field->isPkInSource()))
                $del[] = $field->getName();
        foreach($del as $d)
            $ds->removeField($d);

        $dsRequest = $this->getLastDSRequest();
        if(!$onlyCurrentPage) {
            $dsRequest->setStartRow(0);
            $dsRequest->setEndRow(null);
        }
        $dsRequest->setIncludeMeta(false)
                  ->setIncludeDecodings(false);
        $dsresponse = $ds->execute($dsRequest);
        if($dsresponse->getStatus() == $dsresponse::STATUS_TRANSACTION_SUCCESS) {
            $ret = [];
            foreach( $dsresponse->getData() as $item)
                $ret[] = $item[$ds->getPrimaryKey()];
            return $ret;
        }
        return [];
    }

    /**
     * produces an array of items that are selected on the client
     * @return array
     */
    public function getSelectedIds() {
        $ret = json_decode($this->request->get('_recordIds'));
        return $ret ? $ret : [];
    }

    /**
     * @param string $recordId
     * @return errorReport
     */
    private function deleteRow($recordId) {

        if( $ds = $this->getDataSource() ) {

            $dsr = new DSRequest();

            $dsr->setOperationType($dsr::OPERATION_TYPE_REMOVE)
                ->setParameters([ DataSourceInterface::RECORD_IDENTIFIER => $recordId ]);

            $dsresponse = $ds->execute($dsr);

            switch($dsresponse->getStatus()) {

                case $dsresponse::STATUS_TRANSACTION_SUCCESS : {

                    $delRows = $this->getAttributes()->get(self::ATTR_DELETED_ROWS);
                    if(!$delRows) $delRows = [];
                    $delRows[] = $recordId;
                    $this->getAttributes()->set(self::ATTR_DELETED_ROWS, $delRows);

                    break;
                }
                case $dsresponse::STATUS_TRANSACTION_FAILED :
                case $dsresponse::STATUS_VALIDATION_ERROR : {

                    return $dsresponse->getErrorReport();

                }
            }
        }

        return new ErrorReport();
    }

    /**
     * @return int
     */
    protected function getExportDataMaxRows()
    {
        return 10000;
    }

    /**
     * @inheritdoc
     */
    public function processRows(&$rows) {
        foreach($rows as &$row) {
            $row = $this->addMetaToRow($row);
        }
        return $this;
    }

    /**
     * @param array $row
     * @return array
     */
    private function addMetaToRow( array $row ) {
        return array_merge_recursive($row, [self::ROW_META_IDENTIFIER => $this->getRowMeta($row)]);
    }

    /**
     * @param array $row
     * @return array
     */
    public function getRowMeta(array $row) {
        return [];
    }

    private $defaultColumnCounter = 0;
    private $columnsWithADefault = [];

    /**
     * @param string $name
     * @param string $width
     * @param string $cellTemplateJs
     * @return Column
     */
    protected function setUpDefaultColumn($name, $width=null, $cellTemplateJs=null) {
        $col = $this->getColumn($name);
        if(!$col)
            $col = $this->addNewColumn($name);

        $col->setSortOrder($this->defaultColumnCounter++);

        if($width) {
            $col->setWidth($width);
        }
        if($cellTemplateJs) {
            $col->setCellTemplateJs($cellTemplateJs);
        }

        $this->columnsWithADefault[$name] = true;

        return $col;
    }

    /**
     * removes all the columns that have not explicitly been given a default
     * @param bool $ignoreEditorToken
     */
    protected function removeUndefaultedColumns($ignoreEditorToken=false) {
        //if the lister is being instantiated by the editor, specifically asking for the default columns,
        //we want all the columns from the DS to be returned, so this method has to be inhibited
        if($this->parameters->has(BaseConfigEditorForm::WIDGET_EDITOR_TOKEN) && !$ignoreEditorToken)
            return;

        $cols = $this->getColumnNames();
        foreach($cols as $colName)
            if(!isset($this->columnsWithADefault[$colName]))
                $this->removeColumn($colName);
    }

    /**
     * helper used to populate columns that contain widgets manipulating lists of files
     *
     * @param FileRepositoryInterface $repo
     * @param FileProxyCollectionInterface $files
     * @param bool $withPreview
     * @return array
     */
    public function getRepositoryFileCollectionData($repo, FileProxyCollectionInterface $files, $withPreview=false) {
        $previewProvider = $withPreview ? new FileRepositoryPreviewProvider($repo) : null;

        $clientFiles = [];
        foreach($files->getIterator() as $f) {
            /** @var FileProxyInterface $f */

            $r = $f->getArray();

            if($withPreview && ($makeSurePreviewExists = $previewProvider->getOrCreateCachedPreviewIcon($f->getId(), 80))) {
                $previewIcon = $previewProvider->getUrlOfCachedPreviewIcon($f, 80);
                $r['iconSrc'] = $previewIcon;
            }

            $clientFiles[] = $r;
        }
        return [
            'files' => $clientFiles,
            'filesCount' => $files->count()
        ];
    }

    public function reloadRows() {
        $this->addCommandJs("widget.reloadRows(true);
                             var f = widget.getEditorWidget();
                             if(f) try {
                                f.reBind();
                             } catch(e){}");
    }

    /**
     * @return $this
     */
    protected function setInitialSortFromDS()
    {
        $sortArr = [];
        if( ($ds = $this->getDataSource()) && ($fields = $ds->getFields())) {
            foreach($fields as $dsField) {
                if($dsField->isPkInSource())
                    $sortArr[$dsField->getName()] = self::SORT_ASC;
            }
        }
        $this->setInitialSort($sortArr);
        return $this;
    }

}
