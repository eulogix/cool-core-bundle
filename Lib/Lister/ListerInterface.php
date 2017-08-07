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

use Eulogix\Cool\Lib\DataSource\DSField;
use Eulogix\Cool\Lib\Widget\WidgetInterface;
use Eulogix\Cool\Lib\Widget\WidgetSlot;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

interface ListerInterface extends WidgetInterface {

    const ATTR_SHOW_TOOLS_COLUMN = "show_tools_column";
    const ATTR_HAS_VARIABLE_EDITOR = "has_variable_editor";
    const ATTR_ROW_EDIT_FUNCTION = "row_edit_function";
    const ATTR_INITIAL_SORT = "initial_sort";
    const ATTR_DELETE_MULTIPLE = "delete_multiple";
    const ATTR_FORBID_DELETE = "forbid_delete";
    const ATTR_EXPORT_XLSX = "export_xlsx";
    const ATTR_DELETED_ROWS = "deletedRows";
    const ATTR_SHOW_EDITOR_IN_PLACE = "show_editor_in_place";
    const ATTR_SHOW_TOOLBAR = "show_toolbar";
    const ATTR_PROPAGATED_FIELDS = "propagated_fields";
    const ATTR_INITIAL_SELECTION = "initial_selection";

    const ATTR_TIMELINE_COLUMNS = "timeline_columns";
    const ATTR_TIMELINE_GROUP_COLUMNS = "timeline_group_columns";

    const SORT_ASC = 'asc';
    const SORT_DESC = 'desc';

    const ROW_META_IDENTIFIER = '_meta';

    const ROW_META_ROWCLASS = 'rowClass'; //a class that is applied to the row
    const ROW_META_CELLCLASS = 'cellClass'; //a class applied to all cells in the row
    /**
    * adds a new column
    * 
    * @param string $columnName
    * @returns Column
    */
    public function addNewColumn($columnName);

    /**
    * adds a column
    *
    * @param Column $column
    * @returns Column
    */
    public function addColumn($column);

    /**
    * removes a column
    *
    * @param string $columnName
    * @return boolean
    */
    public function removeColumn($columnName);
    
    /**
    * retrieves a column, or FALSE if the column does not exist
    * 
    * @param string $columnName
    * @return Column|boolean
    */
    public function getColumn($columnName);

    /**
    *
    * @returns Column[]
    */
    public function getColumns();

    /**
    *
    * @returns string[]
    */
    public function getColumnNames();

    
    /**
    * @param WidgetSlot $slot
    */
    public function setFilterSlot( $slot );

    /**
     * this function returns the default serverId for the lister or, if a row is given, for the specified record
     * @return string
     */
    public function getDefaultEditorServerId();

    /**
     * this function returns the serverId for the lister or, if a row is given, for the specified record
     * @param array $editorWidgetParameters
     * @param array $rowData
     * @return string
     */
    public function getEditorServerId($editorWidgetParameters=null, $rowData=null);

    /**
     * if this function return true, the client will ask the server for the proper editor server Id for each record
     * @return bool
     */
    public function hasVariableEditorServerId();

    /**
     * @param bool $bool
     * @return mixed
     */
    public function setHasVariableEditorServerId($bool);

    /**
     * @return array
     */
    public function getInitialSort();

    /**
     * @param array $initialSort
     * @return $this
     */
    public function setInitialSort($initialSort);

    /**
    * Adds to the lister all the columns of the datasource
    * 
    * @returns ListerInterface
    */
    public function addDataSourceColumns();

    /**
     * returns the default column for a given ds field
     * @param DSField $dsField
     * @return Column
     */
    public function getDefaultColumnForDsField($dsField);

    /**
     * @return string|null
     */
    public function getDefaultFilterWidget();

    /**
     * @param boolean $showToolsColumn
     * @return $this
     */
    public function setShowToolsColumn($showToolsColumn);

    /**
     * @return boolean
     */
    public function getShowToolsColumn();

    /**
     * @param $rows
     * @return $this
     */
    public function processRows(&$rows);

    /**
     * @param array $row
     * @return array
     */
    public function getRowMeta(array $row);

    /**
     * instructs the lister to propagate the value of this column appending it to the requests
     * made to the editor widget, using an alias if provided
     * these methods are here, and not in the Column class because record fields may be propagated
     * even if the column is hidden, or not present at all
     *
     * @param string $columnName
     * @param string $as
     */
    public function propagateColumn($columnName, $as=null);

    /**
     * @param string $columnName
     * @return bool
     */
    public function isColumnPropagated($columnName);

    /**
     * @return array
     */
    public function getPropagatedColumns();
}
