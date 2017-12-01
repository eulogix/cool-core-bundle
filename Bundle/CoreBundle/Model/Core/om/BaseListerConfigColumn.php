<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core\om;

use \BasePeer;
use \Criteria;
use \Exception;
use \PDO;
use \Persistent;
use \Propel;
use \PropelException;
use \PropelPDO;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfig;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfigColumn;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfigColumnPeer;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfigColumnQuery;
use Eulogix\Cool\Bundle\CoreBundle\Model\Core\ListerConfigQuery;
use Eulogix\Cool\Lib\Database\Propel\CoolPropelObject;

abstract class BaseListerConfigColumn extends CoolPropelObject implements Persistent
{
    /**
     * Peer class name
     */
    const PEER = 'Eulogix\\Cool\\Bundle\\CoreBundle\\Model\\Core\\ListerConfigColumnPeer';

    /**
     * The Peer class.
     * Instance provides a convenient way of calling static methods on a class
     * that calling code may not be able to identify.
     * @var        ListerConfigColumnPeer
     */
    protected static $peer;

    /**
     * The flag var to prevent infinite loop in deep copy
     * @var       boolean
     */
    protected $startCopy = false;

    /**
     * The value for the lister_config_column_id field.
     * @var        int
     */
    protected $lister_config_column_id;

    /**
     * The value for the lister_config_id field.
     * @var        int
     */
    protected $lister_config_id;

    /**
     * The value for the name field.
     * @var        string
     */
    protected $name;

    /**
     * The value for the sortable_flag field.
     * @var        boolean
     */
    protected $sortable_flag;

    /**
     * The value for the editable_flag field.
     * @var        boolean
     */
    protected $editable_flag;

    /**
     * The value for the hidden_flag field.
     * @var        boolean
     */
    protected $hidden_flag;

    /**
     * The value for the show_summary_flag field.
     * @var        boolean
     */
    protected $show_summary_flag;

    /**
     * The value for the width field.
     * @var        string
     */
    protected $width;

    /**
     * The value for the cell_template field.
     * @var        string
     */
    protected $cell_template;

    /**
     * The value for the cell_template_js field.
     * @var        string
     */
    protected $cell_template_js;

    /**
     * The value for the dijit_widget_template field.
     * @var        string
     */
    protected $dijit_widget_template;

    /**
     * The value for the dijit_widget_set_value_js field.
     * @var        string
     */
    protected $dijit_widget_set_value_js;

    /**
     * The value for the column_style_css field.
     * @var        string
     */
    protected $column_style_css;

    /**
     * The value for the sort_order field.
     * @var        int
     */
    protected $sort_order;

    /**
     * The value for the sortby_order field.
     * @var        int
     */
    protected $sortby_order;

    /**
     * The value for the sortby_direction field.
     * @var        string
     */
    protected $sortby_direction;

    /**
     * The value for the truncate_chars field.
     * @var        int
     */
    protected $truncate_chars;

    /**
     * The value for the tooltip_js_expression field.
     * @var        string
     */
    protected $tooltip_js_expression;

    /**
     * The value for the tooltip_url_js_expression field.
     * @var        string
     */
    protected $tooltip_url_js_expression;

    /**
     * The value for the tooltip_max_width field.
     * @var        int
     */
    protected $tooltip_max_width;

    /**
     * The value for the tooltip_delay_msec field.
     * @var        int
     */
    protected $tooltip_delay_msec;

    /**
     * @var        ListerConfig
     */
    protected $aListerConfig;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInSave = false;

    /**
     * Flag to prevent endless validation loop, if this object is referenced
     * by another object which falls in this transaction.
     * @var        boolean
     */
    protected $alreadyInValidation = false;

    /**
     * Flag to prevent endless clearAllReferences($deep=true) loop, if this object is referenced
     * @var        boolean
     */
    protected $alreadyInClearAllReferencesDeep = false;

    /**
     * Get the [lister_config_column_id] column value.
     *
     * @return int
     */
    public function getListerConfigColumnId()
    {

        return $this->lister_config_column_id;
    }

    /**
     * Get the [lister_config_id] column value.
     *
     * @return int
     */
    public function getListerConfigId()
    {

        return $this->lister_config_id;
    }

    /**
     * Get the [name] column value.
     *
     * @return string
     */
    public function getName()
    {

        return $this->name;
    }

    /**
     * Get the [sortable_flag] column value.
     *
     * @return boolean
     */
    public function getSortableFlag()
    {

        return $this->sortable_flag;
    }

    /**
     * Get the [editable_flag] column value.
     *
     * @return boolean
     */
    public function getEditableFlag()
    {

        return $this->editable_flag;
    }

    /**
     * Get the [hidden_flag] column value.
     *
     * @return boolean
     */
    public function getHiddenFlag()
    {

        return $this->hidden_flag;
    }

    /**
     * Get the [show_summary_flag] column value.
     *
     * @return boolean
     */
    public function getShowSummaryFlag()
    {

        return $this->show_summary_flag;
    }

    /**
     * Get the [width] column value.
     *
     * @return string
     */
    public function getWidth()
    {

        return $this->width;
    }

    /**
     * Get the [cell_template] column value.
     *
     * @return string
     */
    public function getCellTemplate()
    {

        return $this->cell_template;
    }

    /**
     * Get the [cell_template_js] column value.
     *
     * @return string
     */
    public function getCellTemplateJs()
    {

        return $this->cell_template_js;
    }

    /**
     * Get the [dijit_widget_template] column value.
     *
     * @return string
     */
    public function getDijitWidgetTemplate()
    {

        return $this->dijit_widget_template;
    }

    /**
     * Get the [dijit_widget_set_value_js] column value.
     *
     * @return string
     */
    public function getDijitWidgetSetValueJs()
    {

        return $this->dijit_widget_set_value_js;
    }

    /**
     * Get the [column_style_css] column value.
     *
     * @return string
     */
    public function getColumnStyleCss()
    {

        return $this->column_style_css;
    }

    /**
     * Get the [sort_order] column value.
     *
     * @return int
     */
    public function getSortOrder()
    {

        return $this->sort_order;
    }

    /**
     * Get the [sortby_order] column value.
     *
     * @return int
     */
    public function getSortbyOrder()
    {

        return $this->sortby_order;
    }

    /**
     * Get the [sortby_direction] column value.
     *
     * @return string
     */
    public function getSortbyDirection()
    {

        return $this->sortby_direction;
    }

    /**
     * Get the [truncate_chars] column value.
     *
     * @return int
     */
    public function getTruncateChars()
    {

        return $this->truncate_chars;
    }

    /**
     * Get the [tooltip_js_expression] column value.
     *
     * @return string
     */
    public function getTooltipJsExpression()
    {

        return $this->tooltip_js_expression;
    }

    /**
     * Get the [tooltip_url_js_expression] column value.
     *
     * @return string
     */
    public function getTooltipUrlJsExpression()
    {

        return $this->tooltip_url_js_expression;
    }

    /**
     * Get the [tooltip_max_width] column value.
     *
     * @return int
     */
    public function getTooltipMaxWidth()
    {

        return $this->tooltip_max_width;
    }

    /**
     * Get the [tooltip_delay_msec] column value.
     *
     * @return int
     */
    public function getTooltipDelayMsec()
    {

        return $this->tooltip_delay_msec;
    }

    /**
     * Set the value of [lister_config_column_id] column.
     *
     * @param  int $v new value
     * @return ListerConfigColumn The current object (for fluent API support)
     */
    public function setListerConfigColumnId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->lister_config_column_id !== $v) {
            $this->lister_config_column_id = $v;
            $this->modifiedColumns[] = ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID;
        }


        return $this;
    } // setListerConfigColumnId()

    /**
     * Set the value of [lister_config_id] column.
     *
     * @param  int $v new value
     * @return ListerConfigColumn The current object (for fluent API support)
     */
    public function setListerConfigId($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->lister_config_id !== $v) {
            $this->lister_config_id = $v;
            $this->modifiedColumns[] = ListerConfigColumnPeer::LISTER_CONFIG_ID;
        }

        if ($this->aListerConfig !== null && $this->aListerConfig->getListerConfigId() !== $v) {
            $this->aListerConfig = null;
        }


        return $this;
    } // setListerConfigId()

    /**
     * Set the value of [name] column.
     *
     * @param  string $v new value
     * @return ListerConfigColumn The current object (for fluent API support)
     */
    public function setName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->name !== $v) {
            $this->name = $v;
            $this->modifiedColumns[] = ListerConfigColumnPeer::NAME;
        }


        return $this;
    } // setName()

    /**
     * Sets the value of the [sortable_flag] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return ListerConfigColumn The current object (for fluent API support)
     */
    public function setSortableFlag($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->sortable_flag !== $v) {
            $this->sortable_flag = $v;
            $this->modifiedColumns[] = ListerConfigColumnPeer::SORTABLE_FLAG;
        }


        return $this;
    } // setSortableFlag()

    /**
     * Sets the value of the [editable_flag] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return ListerConfigColumn The current object (for fluent API support)
     */
    public function setEditableFlag($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->editable_flag !== $v) {
            $this->editable_flag = $v;
            $this->modifiedColumns[] = ListerConfigColumnPeer::EDITABLE_FLAG;
        }


        return $this;
    } // setEditableFlag()

    /**
     * Sets the value of the [hidden_flag] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return ListerConfigColumn The current object (for fluent API support)
     */
    public function setHiddenFlag($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->hidden_flag !== $v) {
            $this->hidden_flag = $v;
            $this->modifiedColumns[] = ListerConfigColumnPeer::HIDDEN_FLAG;
        }


        return $this;
    } // setHiddenFlag()

    /**
     * Sets the value of the [show_summary_flag] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param boolean|integer|string $v The new value
     * @return ListerConfigColumn The current object (for fluent API support)
     */
    public function setShowSummaryFlag($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->show_summary_flag !== $v) {
            $this->show_summary_flag = $v;
            $this->modifiedColumns[] = ListerConfigColumnPeer::SHOW_SUMMARY_FLAG;
        }


        return $this;
    } // setShowSummaryFlag()

    /**
     * Set the value of [width] column.
     *
     * @param  string $v new value
     * @return ListerConfigColumn The current object (for fluent API support)
     */
    public function setWidth($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->width !== $v) {
            $this->width = $v;
            $this->modifiedColumns[] = ListerConfigColumnPeer::WIDTH;
        }


        return $this;
    } // setWidth()

    /**
     * Set the value of [cell_template] column.
     *
     * @param  string $v new value
     * @return ListerConfigColumn The current object (for fluent API support)
     */
    public function setCellTemplate($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->cell_template !== $v) {
            $this->cell_template = $v;
            $this->modifiedColumns[] = ListerConfigColumnPeer::CELL_TEMPLATE;
        }


        return $this;
    } // setCellTemplate()

    /**
     * Set the value of [cell_template_js] column.
     *
     * @param  string $v new value
     * @return ListerConfigColumn The current object (for fluent API support)
     */
    public function setCellTemplateJs($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->cell_template_js !== $v) {
            $this->cell_template_js = $v;
            $this->modifiedColumns[] = ListerConfigColumnPeer::CELL_TEMPLATE_JS;
        }


        return $this;
    } // setCellTemplateJs()

    /**
     * Set the value of [dijit_widget_template] column.
     *
     * @param  string $v new value
     * @return ListerConfigColumn The current object (for fluent API support)
     */
    public function setDijitWidgetTemplate($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->dijit_widget_template !== $v) {
            $this->dijit_widget_template = $v;
            $this->modifiedColumns[] = ListerConfigColumnPeer::DIJIT_WIDGET_TEMPLATE;
        }


        return $this;
    } // setDijitWidgetTemplate()

    /**
     * Set the value of [dijit_widget_set_value_js] column.
     *
     * @param  string $v new value
     * @return ListerConfigColumn The current object (for fluent API support)
     */
    public function setDijitWidgetSetValueJs($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->dijit_widget_set_value_js !== $v) {
            $this->dijit_widget_set_value_js = $v;
            $this->modifiedColumns[] = ListerConfigColumnPeer::DIJIT_WIDGET_SET_VALUE_JS;
        }


        return $this;
    } // setDijitWidgetSetValueJs()

    /**
     * Set the value of [column_style_css] column.
     *
     * @param  string $v new value
     * @return ListerConfigColumn The current object (for fluent API support)
     */
    public function setColumnStyleCss($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->column_style_css !== $v) {
            $this->column_style_css = $v;
            $this->modifiedColumns[] = ListerConfigColumnPeer::COLUMN_STYLE_CSS;
        }


        return $this;
    } // setColumnStyleCss()

    /**
     * Set the value of [sort_order] column.
     *
     * @param  int $v new value
     * @return ListerConfigColumn The current object (for fluent API support)
     */
    public function setSortOrder($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->sort_order !== $v) {
            $this->sort_order = $v;
            $this->modifiedColumns[] = ListerConfigColumnPeer::SORT_ORDER;
        }


        return $this;
    } // setSortOrder()

    /**
     * Set the value of [sortby_order] column.
     *
     * @param  int $v new value
     * @return ListerConfigColumn The current object (for fluent API support)
     */
    public function setSortbyOrder($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->sortby_order !== $v) {
            $this->sortby_order = $v;
            $this->modifiedColumns[] = ListerConfigColumnPeer::SORTBY_ORDER;
        }


        return $this;
    } // setSortbyOrder()

    /**
     * Set the value of [sortby_direction] column.
     *
     * @param  string $v new value
     * @return ListerConfigColumn The current object (for fluent API support)
     */
    public function setSortbyDirection($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->sortby_direction !== $v) {
            $this->sortby_direction = $v;
            $this->modifiedColumns[] = ListerConfigColumnPeer::SORTBY_DIRECTION;
        }


        return $this;
    } // setSortbyDirection()

    /**
     * Set the value of [truncate_chars] column.
     *
     * @param  int $v new value
     * @return ListerConfigColumn The current object (for fluent API support)
     */
    public function setTruncateChars($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->truncate_chars !== $v) {
            $this->truncate_chars = $v;
            $this->modifiedColumns[] = ListerConfigColumnPeer::TRUNCATE_CHARS;
        }


        return $this;
    } // setTruncateChars()

    /**
     * Set the value of [tooltip_js_expression] column.
     *
     * @param  string $v new value
     * @return ListerConfigColumn The current object (for fluent API support)
     */
    public function setTooltipJsExpression($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->tooltip_js_expression !== $v) {
            $this->tooltip_js_expression = $v;
            $this->modifiedColumns[] = ListerConfigColumnPeer::TOOLTIP_JS_EXPRESSION;
        }


        return $this;
    } // setTooltipJsExpression()

    /**
     * Set the value of [tooltip_url_js_expression] column.
     *
     * @param  string $v new value
     * @return ListerConfigColumn The current object (for fluent API support)
     */
    public function setTooltipUrlJsExpression($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->tooltip_url_js_expression !== $v) {
            $this->tooltip_url_js_expression = $v;
            $this->modifiedColumns[] = ListerConfigColumnPeer::TOOLTIP_URL_JS_EXPRESSION;
        }


        return $this;
    } // setTooltipUrlJsExpression()

    /**
     * Set the value of [tooltip_max_width] column.
     *
     * @param  int $v new value
     * @return ListerConfigColumn The current object (for fluent API support)
     */
    public function setTooltipMaxWidth($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->tooltip_max_width !== $v) {
            $this->tooltip_max_width = $v;
            $this->modifiedColumns[] = ListerConfigColumnPeer::TOOLTIP_MAX_WIDTH;
        }


        return $this;
    } // setTooltipMaxWidth()

    /**
     * Set the value of [tooltip_delay_msec] column.
     *
     * @param  int $v new value
     * @return ListerConfigColumn The current object (for fluent API support)
     */
    public function setTooltipDelayMsec($v)
    {
        if ($v !== null && is_numeric($v)) {
            $v = (int) $v;
        }

        if ($this->tooltip_delay_msec !== $v) {
            $this->tooltip_delay_msec = $v;
            $this->modifiedColumns[] = ListerConfigColumnPeer::TOOLTIP_DELAY_MSEC;
        }


        return $this;
    } // setTooltipDelayMsec()

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return boolean Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues()
    {
        // otherwise, everything was equal, so return true
        return true;
    } // hasOnlyDefaultValues()

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array $row The row returned by PDOStatement->fetch(PDO::FETCH_NUM)
     * @param int $startcol 0-based offset column which indicates which resultset column to start with.
     * @param boolean $rehydrate Whether this object is being re-hydrated from the database.
     * @return int             next starting column
     * @throws PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate($row, $startcol = 0, $rehydrate = false)
    {
        try {

            $this->lister_config_column_id = ($row[$startcol + 0] !== null) ? (int) $row[$startcol + 0] : null;
            $this->lister_config_id = ($row[$startcol + 1] !== null) ? (int) $row[$startcol + 1] : null;
            $this->name = ($row[$startcol + 2] !== null) ? (string) $row[$startcol + 2] : null;
            $this->sortable_flag = ($row[$startcol + 3] !== null) ? (boolean) $row[$startcol + 3] : null;
            $this->editable_flag = ($row[$startcol + 4] !== null) ? (boolean) $row[$startcol + 4] : null;
            $this->hidden_flag = ($row[$startcol + 5] !== null) ? (boolean) $row[$startcol + 5] : null;
            $this->show_summary_flag = ($row[$startcol + 6] !== null) ? (boolean) $row[$startcol + 6] : null;
            $this->width = ($row[$startcol + 7] !== null) ? (string) $row[$startcol + 7] : null;
            $this->cell_template = ($row[$startcol + 8] !== null) ? (string) $row[$startcol + 8] : null;
            $this->cell_template_js = ($row[$startcol + 9] !== null) ? (string) $row[$startcol + 9] : null;
            $this->dijit_widget_template = ($row[$startcol + 10] !== null) ? (string) $row[$startcol + 10] : null;
            $this->dijit_widget_set_value_js = ($row[$startcol + 11] !== null) ? (string) $row[$startcol + 11] : null;
            $this->column_style_css = ($row[$startcol + 12] !== null) ? (string) $row[$startcol + 12] : null;
            $this->sort_order = ($row[$startcol + 13] !== null) ? (int) $row[$startcol + 13] : null;
            $this->sortby_order = ($row[$startcol + 14] !== null) ? (int) $row[$startcol + 14] : null;
            $this->sortby_direction = ($row[$startcol + 15] !== null) ? (string) $row[$startcol + 15] : null;
            $this->truncate_chars = ($row[$startcol + 16] !== null) ? (int) $row[$startcol + 16] : null;
            $this->tooltip_js_expression = ($row[$startcol + 17] !== null) ? (string) $row[$startcol + 17] : null;
            $this->tooltip_url_js_expression = ($row[$startcol + 18] !== null) ? (string) $row[$startcol + 18] : null;
            $this->tooltip_max_width = ($row[$startcol + 19] !== null) ? (int) $row[$startcol + 19] : null;
            $this->tooltip_delay_msec = ($row[$startcol + 20] !== null) ? (int) $row[$startcol + 20] : null;
            $this->resetModified();

            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }
            $this->postHydrate($row, $startcol, $rehydrate);

            return $startcol + 21; // 21 = ListerConfigColumnPeer::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException("Error populating ListerConfigColumn object", $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws PropelException
     */
    public function ensureConsistency()
    {

        if ($this->aListerConfig !== null && $this->lister_config_id !== $this->aListerConfig->getListerConfigId()) {
            $this->aListerConfig = null;
        }
    } // ensureConsistency

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param boolean $deep (optional) Whether to also de-associated any related objects.
     * @param PropelPDO $con (optional) The PropelPDO connection to use.
     * @return void
     * @throws PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload($deep = false, PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getConnection(ListerConfigColumnPeer::DATABASE_NAME, Propel::CONNECTION_READ);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $stmt = ListerConfigColumnPeer::doSelectStmt($this->buildPkeyCriteria(), $con);
        $row = $stmt->fetch(PDO::FETCH_NUM);
        $stmt->closeCursor();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aListerConfig = null;
        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param PropelPDO $con
     * @return void
     * @throws PropelException
     * @throws Exception
     * @see        BaseObject::setDeleted()
     * @see        BaseObject::isDeleted()
     */
    public function delete(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(ListerConfigColumnPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        try {
            $deleteQuery = ListerConfigColumnQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $con->commit();
                $this->setDeleted(true);
            } else {
                $con->commit();
            }
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @throws Exception
     * @see        doSave()
     */
    public function save(PropelPDO $con = null)
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($con === null) {
            $con = Propel::getConnection(ListerConfigColumnPeer::DATABASE_NAME, Propel::CONNECTION_WRITE);
        }

        $con->beginTransaction();
        $isInsert = $this->isNew();
        try {
            $ret = $this->preSave($con);
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                ListerConfigColumnPeer::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }
            $con->commit();

            return $affectedRows;
        } catch (Exception $e) {
            $con->rollBack();
            throw $e;
        }
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param PropelPDO $con
     * @return int             The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws PropelException
     * @see        save()
     */
    protected function doSave(PropelPDO $con)
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aListerConfig !== null) {
                if ($this->aListerConfig->isModified() || $this->aListerConfig->isNew()) {
                    $affectedRows += $this->aListerConfig->save($con);
                }
                $this->setListerConfig($this->aListerConfig);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                } else {
                    $this->doUpdate($con);
                }
                $affectedRows += 1;
                $this->resetModified();
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    } // doSave()

    /**
     * Insert the row in the database.
     *
     * @param PropelPDO $con
     *
     * @throws PropelException
     * @see        doSave()
     */
    protected function doInsert(PropelPDO $con)
    {
        $modifiedColumns = array();
        $index = 0;

        $this->modifiedColumns[] = ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID;
        if (null !== $this->lister_config_column_id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID . ')');
        }
        if (null === $this->lister_config_column_id) {
            try {
                $stmt = $con->query("SELECT nextval('core.lister_config_column_lister_config_column_id_seq')");
                $row = $stmt->fetch(PDO::FETCH_NUM);
                $this->lister_config_column_id = $row[0];
            } catch (Exception $e) {
                throw new PropelException('Unable to get sequence id.', $e);
            }
        }


         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID)) {
            $modifiedColumns[':p' . $index++]  = 'lister_config_column_id';
        }
        if ($this->isColumnModified(ListerConfigColumnPeer::LISTER_CONFIG_ID)) {
            $modifiedColumns[':p' . $index++]  = 'lister_config_id';
        }
        if ($this->isColumnModified(ListerConfigColumnPeer::NAME)) {
            $modifiedColumns[':p' . $index++]  = 'name';
        }
        if ($this->isColumnModified(ListerConfigColumnPeer::SORTABLE_FLAG)) {
            $modifiedColumns[':p' . $index++]  = 'sortable_flag';
        }
        if ($this->isColumnModified(ListerConfigColumnPeer::EDITABLE_FLAG)) {
            $modifiedColumns[':p' . $index++]  = 'editable_flag';
        }
        if ($this->isColumnModified(ListerConfigColumnPeer::HIDDEN_FLAG)) {
            $modifiedColumns[':p' . $index++]  = 'hidden_flag';
        }
        if ($this->isColumnModified(ListerConfigColumnPeer::SHOW_SUMMARY_FLAG)) {
            $modifiedColumns[':p' . $index++]  = 'show_summary_flag';
        }
        if ($this->isColumnModified(ListerConfigColumnPeer::WIDTH)) {
            $modifiedColumns[':p' . $index++]  = 'width';
        }
        if ($this->isColumnModified(ListerConfigColumnPeer::CELL_TEMPLATE)) {
            $modifiedColumns[':p' . $index++]  = 'cell_template';
        }
        if ($this->isColumnModified(ListerConfigColumnPeer::CELL_TEMPLATE_JS)) {
            $modifiedColumns[':p' . $index++]  = 'cell_template_js';
        }
        if ($this->isColumnModified(ListerConfigColumnPeer::DIJIT_WIDGET_TEMPLATE)) {
            $modifiedColumns[':p' . $index++]  = 'dijit_widget_template';
        }
        if ($this->isColumnModified(ListerConfigColumnPeer::DIJIT_WIDGET_SET_VALUE_JS)) {
            $modifiedColumns[':p' . $index++]  = 'dijit_widget_set_value_js';
        }
        if ($this->isColumnModified(ListerConfigColumnPeer::COLUMN_STYLE_CSS)) {
            $modifiedColumns[':p' . $index++]  = 'column_style_css';
        }
        if ($this->isColumnModified(ListerConfigColumnPeer::SORT_ORDER)) {
            $modifiedColumns[':p' . $index++]  = 'sort_order';
        }
        if ($this->isColumnModified(ListerConfigColumnPeer::SORTBY_ORDER)) {
            $modifiedColumns[':p' . $index++]  = 'sortby_order';
        }
        if ($this->isColumnModified(ListerConfigColumnPeer::SORTBY_DIRECTION)) {
            $modifiedColumns[':p' . $index++]  = 'sortby_direction';
        }
        if ($this->isColumnModified(ListerConfigColumnPeer::TRUNCATE_CHARS)) {
            $modifiedColumns[':p' . $index++]  = 'truncate_chars';
        }
        if ($this->isColumnModified(ListerConfigColumnPeer::TOOLTIP_JS_EXPRESSION)) {
            $modifiedColumns[':p' . $index++]  = 'tooltip_js_expression';
        }
        if ($this->isColumnModified(ListerConfigColumnPeer::TOOLTIP_URL_JS_EXPRESSION)) {
            $modifiedColumns[':p' . $index++]  = 'tooltip_url_js_expression';
        }
        if ($this->isColumnModified(ListerConfigColumnPeer::TOOLTIP_MAX_WIDTH)) {
            $modifiedColumns[':p' . $index++]  = 'tooltip_max_width';
        }
        if ($this->isColumnModified(ListerConfigColumnPeer::TOOLTIP_DELAY_MSEC)) {
            $modifiedColumns[':p' . $index++]  = 'tooltip_delay_msec';
        }

        $sql = sprintf(
            'INSERT INTO core.lister_config_column (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'lister_config_column_id':
                        $stmt->bindValue($identifier, $this->lister_config_column_id, PDO::PARAM_INT);
                        break;
                    case 'lister_config_id':
                        $stmt->bindValue($identifier, $this->lister_config_id, PDO::PARAM_INT);
                        break;
                    case 'name':
                        $stmt->bindValue($identifier, $this->name, PDO::PARAM_STR);
                        break;
                    case 'sortable_flag':
                        $stmt->bindValue($identifier, $this->sortable_flag, PDO::PARAM_BOOL);
                        break;
                    case 'editable_flag':
                        $stmt->bindValue($identifier, $this->editable_flag, PDO::PARAM_BOOL);
                        break;
                    case 'hidden_flag':
                        $stmt->bindValue($identifier, $this->hidden_flag, PDO::PARAM_BOOL);
                        break;
                    case 'show_summary_flag':
                        $stmt->bindValue($identifier, $this->show_summary_flag, PDO::PARAM_BOOL);
                        break;
                    case 'width':
                        $stmt->bindValue($identifier, $this->width, PDO::PARAM_STR);
                        break;
                    case 'cell_template':
                        $stmt->bindValue($identifier, $this->cell_template, PDO::PARAM_STR);
                        break;
                    case 'cell_template_js':
                        $stmt->bindValue($identifier, $this->cell_template_js, PDO::PARAM_STR);
                        break;
                    case 'dijit_widget_template':
                        $stmt->bindValue($identifier, $this->dijit_widget_template, PDO::PARAM_STR);
                        break;
                    case 'dijit_widget_set_value_js':
                        $stmt->bindValue($identifier, $this->dijit_widget_set_value_js, PDO::PARAM_STR);
                        break;
                    case 'column_style_css':
                        $stmt->bindValue($identifier, $this->column_style_css, PDO::PARAM_STR);
                        break;
                    case 'sort_order':
                        $stmt->bindValue($identifier, $this->sort_order, PDO::PARAM_INT);
                        break;
                    case 'sortby_order':
                        $stmt->bindValue($identifier, $this->sortby_order, PDO::PARAM_INT);
                        break;
                    case 'sortby_direction':
                        $stmt->bindValue($identifier, $this->sortby_direction, PDO::PARAM_STR);
                        break;
                    case 'truncate_chars':
                        $stmt->bindValue($identifier, $this->truncate_chars, PDO::PARAM_INT);
                        break;
                    case 'tooltip_js_expression':
                        $stmt->bindValue($identifier, $this->tooltip_js_expression, PDO::PARAM_STR);
                        break;
                    case 'tooltip_url_js_expression':
                        $stmt->bindValue($identifier, $this->tooltip_url_js_expression, PDO::PARAM_STR);
                        break;
                    case 'tooltip_max_width':
                        $stmt->bindValue($identifier, $this->tooltip_max_width, PDO::PARAM_INT);
                        break;
                    case 'tooltip_delay_msec':
                        $stmt->bindValue($identifier, $this->tooltip_delay_msec, PDO::PARAM_INT);
                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), $e);
        }

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param PropelPDO $con
     *
     * @see        doSave()
     */
    protected function doUpdate(PropelPDO $con)
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();
        BasePeer::doUpdate($selectCriteria, $valuesCriteria, $con);
    }

    /**
     * Array of ValidationFailed objects.
     * @var        array ValidationFailed[]
     */
    protected $validationFailures = array();

    /**
     * Gets any ValidationFailed objects that resulted from last call to validate().
     *
     *
     * @return array ValidationFailed[]
     * @see        validate()
     */
    public function getValidationFailures()
    {
        return $this->validationFailures;
    }

    /**
     * Validates the objects modified field values and all objects related to this table.
     *
     * If $columns is either a column name or an array of column names
     * only those columns are validated.
     *
     * @param mixed $columns Column name or an array of column names.
     * @return boolean Whether all columns pass validation.
     * @see        doValidate()
     * @see        getValidationFailures()
     */
    public function validate($columns = null)
    {
        $res = $this->doValidate($columns);
        if ($res === true) {
            $this->validationFailures = array();

            return true;
        }

        $this->validationFailures = $res;

        return false;
    }

    /**
     * This function performs the validation work for complex object models.
     *
     * In addition to checking the current object, all related objects will
     * also be validated.  If all pass then <code>true</code> is returned; otherwise
     * an aggregated array of ValidationFailed objects will be returned.
     *
     * @param array $columns Array of column names to validate.
     * @return mixed <code>true</code> if all validations pass; array of <code>ValidationFailed</code> objects otherwise.
     */
    protected function doValidate($columns = null)
    {
        if (!$this->alreadyInValidation) {
            $this->alreadyInValidation = true;
            $retval = null;

            $failureMap = array();


            // We call the validate method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aListerConfig !== null) {
                if (!$this->aListerConfig->validate($columns)) {
                    $failureMap = array_merge($failureMap, $this->aListerConfig->getValidationFailures());
                }
            }


            if (($retval = ListerConfigColumnPeer::doValidate($this, $columns)) !== true) {
                $failureMap = array_merge($failureMap, $retval);
            }



            $this->alreadyInValidation = false;
        }

        return (!empty($failureMap) ? $failureMap : true);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param string $name name
     * @param string $type The type of fieldname the $name is of:
     *               one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *               BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *               Defaults to BasePeer::TYPE_PHPNAME
     * @return mixed Value of field.
     */
    public function getByName($name, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = ListerConfigColumnPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition($pos)
    {
        switch ($pos) {
            case 0:
                return $this->getListerConfigColumnId();
                break;
            case 1:
                return $this->getListerConfigId();
                break;
            case 2:
                return $this->getName();
                break;
            case 3:
                return $this->getSortableFlag();
                break;
            case 4:
                return $this->getEditableFlag();
                break;
            case 5:
                return $this->getHiddenFlag();
                break;
            case 6:
                return $this->getShowSummaryFlag();
                break;
            case 7:
                return $this->getWidth();
                break;
            case 8:
                return $this->getCellTemplate();
                break;
            case 9:
                return $this->getCellTemplateJs();
                break;
            case 10:
                return $this->getDijitWidgetTemplate();
                break;
            case 11:
                return $this->getDijitWidgetSetValueJs();
                break;
            case 12:
                return $this->getColumnStyleCss();
                break;
            case 13:
                return $this->getSortOrder();
                break;
            case 14:
                return $this->getSortbyOrder();
                break;
            case 15:
                return $this->getSortbyDirection();
                break;
            case 16:
                return $this->getTruncateChars();
                break;
            case 17:
                return $this->getTooltipJsExpression();
                break;
            case 18:
                return $this->getTooltipUrlJsExpression();
                break;
            case 19:
                return $this->getTooltipMaxWidth();
                break;
            case 20:
                return $this->getTooltipDelayMsec();
                break;
            default:
                return null;
                break;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param     string  $keyType (optional) One of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     *                    BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                    Defaults to BasePeer::TYPE_PHPNAME.
     * @param     boolean $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to true.
     * @param     array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param     boolean $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array an associative array containing the field names (as keys) and field values
     */
    public function toArray($keyType = BasePeer::TYPE_PHPNAME, $includeLazyLoadColumns = true, $alreadyDumpedObjects = array(), $includeForeignObjects = false)
    {
        if (isset($alreadyDumpedObjects['ListerConfigColumn'][$this->getPrimaryKey()])) {
            return '*RECURSION*';
        }
        $alreadyDumpedObjects['ListerConfigColumn'][$this->getPrimaryKey()] = true;
        $keys = ListerConfigColumnPeer::getFieldNames($keyType);
        $result = array(
            $keys[0] => $this->getListerConfigColumnId(),
            $keys[1] => $this->getListerConfigId(),
            $keys[2] => $this->getName(),
            $keys[3] => $this->getSortableFlag(),
            $keys[4] => $this->getEditableFlag(),
            $keys[5] => $this->getHiddenFlag(),
            $keys[6] => $this->getShowSummaryFlag(),
            $keys[7] => $this->getWidth(),
            $keys[8] => $this->getCellTemplate(),
            $keys[9] => $this->getCellTemplateJs(),
            $keys[10] => $this->getDijitWidgetTemplate(),
            $keys[11] => $this->getDijitWidgetSetValueJs(),
            $keys[12] => $this->getColumnStyleCss(),
            $keys[13] => $this->getSortOrder(),
            $keys[14] => $this->getSortbyOrder(),
            $keys[15] => $this->getSortbyDirection(),
            $keys[16] => $this->getTruncateChars(),
            $keys[17] => $this->getTooltipJsExpression(),
            $keys[18] => $this->getTooltipUrlJsExpression(),
            $keys[19] => $this->getTooltipMaxWidth(),
            $keys[20] => $this->getTooltipDelayMsec(),
        );
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aListerConfig) {
                $result['ListerConfig'] = $this->aListerConfig->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param string $name peer name
     * @param mixed $value field value
     * @param string $type The type of fieldname the $name is of:
     *                     one of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME
     *                     BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     *                     Defaults to BasePeer::TYPE_PHPNAME
     * @return void
     */
    public function setByName($name, $value, $type = BasePeer::TYPE_PHPNAME)
    {
        $pos = ListerConfigColumnPeer::translateFieldName($name, $type, BasePeer::TYPE_NUM);

        $this->setByPosition($pos, $value);
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @param mixed $value field value
     * @return void
     */
    public function setByPosition($pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setListerConfigColumnId($value);
                break;
            case 1:
                $this->setListerConfigId($value);
                break;
            case 2:
                $this->setName($value);
                break;
            case 3:
                $this->setSortableFlag($value);
                break;
            case 4:
                $this->setEditableFlag($value);
                break;
            case 5:
                $this->setHiddenFlag($value);
                break;
            case 6:
                $this->setShowSummaryFlag($value);
                break;
            case 7:
                $this->setWidth($value);
                break;
            case 8:
                $this->setCellTemplate($value);
                break;
            case 9:
                $this->setCellTemplateJs($value);
                break;
            case 10:
                $this->setDijitWidgetTemplate($value);
                break;
            case 11:
                $this->setDijitWidgetSetValueJs($value);
                break;
            case 12:
                $this->setColumnStyleCss($value);
                break;
            case 13:
                $this->setSortOrder($value);
                break;
            case 14:
                $this->setSortbyOrder($value);
                break;
            case 15:
                $this->setSortbyDirection($value);
                break;
            case 16:
                $this->setTruncateChars($value);
                break;
            case 17:
                $this->setTooltipJsExpression($value);
                break;
            case 18:
                $this->setTooltipUrlJsExpression($value);
                break;
            case 19:
                $this->setTooltipMaxWidth($value);
                break;
            case 20:
                $this->setTooltipDelayMsec($value);
                break;
        } // switch()
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants BasePeer::TYPE_PHPNAME, BasePeer::TYPE_STUDLYPHPNAME,
     * BasePeer::TYPE_COLNAME, BasePeer::TYPE_FIELDNAME, BasePeer::TYPE_NUM.
     * The default key type is the column's BasePeer::TYPE_PHPNAME
     *
     * @param array  $arr     An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     * @return void
     */
    public function fromArray($arr, $keyType = BasePeer::TYPE_PHPNAME)
    {
        $keys = ListerConfigColumnPeer::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) $this->setListerConfigColumnId($arr[$keys[0]]);
        if (array_key_exists($keys[1], $arr)) $this->setListerConfigId($arr[$keys[1]]);
        if (array_key_exists($keys[2], $arr)) $this->setName($arr[$keys[2]]);
        if (array_key_exists($keys[3], $arr)) $this->setSortableFlag($arr[$keys[3]]);
        if (array_key_exists($keys[4], $arr)) $this->setEditableFlag($arr[$keys[4]]);
        if (array_key_exists($keys[5], $arr)) $this->setHiddenFlag($arr[$keys[5]]);
        if (array_key_exists($keys[6], $arr)) $this->setShowSummaryFlag($arr[$keys[6]]);
        if (array_key_exists($keys[7], $arr)) $this->setWidth($arr[$keys[7]]);
        if (array_key_exists($keys[8], $arr)) $this->setCellTemplate($arr[$keys[8]]);
        if (array_key_exists($keys[9], $arr)) $this->setCellTemplateJs($arr[$keys[9]]);
        if (array_key_exists($keys[10], $arr)) $this->setDijitWidgetTemplate($arr[$keys[10]]);
        if (array_key_exists($keys[11], $arr)) $this->setDijitWidgetSetValueJs($arr[$keys[11]]);
        if (array_key_exists($keys[12], $arr)) $this->setColumnStyleCss($arr[$keys[12]]);
        if (array_key_exists($keys[13], $arr)) $this->setSortOrder($arr[$keys[13]]);
        if (array_key_exists($keys[14], $arr)) $this->setSortbyOrder($arr[$keys[14]]);
        if (array_key_exists($keys[15], $arr)) $this->setSortbyDirection($arr[$keys[15]]);
        if (array_key_exists($keys[16], $arr)) $this->setTruncateChars($arr[$keys[16]]);
        if (array_key_exists($keys[17], $arr)) $this->setTooltipJsExpression($arr[$keys[17]]);
        if (array_key_exists($keys[18], $arr)) $this->setTooltipUrlJsExpression($arr[$keys[18]]);
        if (array_key_exists($keys[19], $arr)) $this->setTooltipMaxWidth($arr[$keys[19]]);
        if (array_key_exists($keys[20], $arr)) $this->setTooltipDelayMsec($arr[$keys[20]]);
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria()
    {
        $criteria = new Criteria(ListerConfigColumnPeer::DATABASE_NAME);

        if ($this->isColumnModified(ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID)) $criteria->add(ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID, $this->lister_config_column_id);
        if ($this->isColumnModified(ListerConfigColumnPeer::LISTER_CONFIG_ID)) $criteria->add(ListerConfigColumnPeer::LISTER_CONFIG_ID, $this->lister_config_id);
        if ($this->isColumnModified(ListerConfigColumnPeer::NAME)) $criteria->add(ListerConfigColumnPeer::NAME, $this->name);
        if ($this->isColumnModified(ListerConfigColumnPeer::SORTABLE_FLAG)) $criteria->add(ListerConfigColumnPeer::SORTABLE_FLAG, $this->sortable_flag);
        if ($this->isColumnModified(ListerConfigColumnPeer::EDITABLE_FLAG)) $criteria->add(ListerConfigColumnPeer::EDITABLE_FLAG, $this->editable_flag);
        if ($this->isColumnModified(ListerConfigColumnPeer::HIDDEN_FLAG)) $criteria->add(ListerConfigColumnPeer::HIDDEN_FLAG, $this->hidden_flag);
        if ($this->isColumnModified(ListerConfigColumnPeer::SHOW_SUMMARY_FLAG)) $criteria->add(ListerConfigColumnPeer::SHOW_SUMMARY_FLAG, $this->show_summary_flag);
        if ($this->isColumnModified(ListerConfigColumnPeer::WIDTH)) $criteria->add(ListerConfigColumnPeer::WIDTH, $this->width);
        if ($this->isColumnModified(ListerConfigColumnPeer::CELL_TEMPLATE)) $criteria->add(ListerConfigColumnPeer::CELL_TEMPLATE, $this->cell_template);
        if ($this->isColumnModified(ListerConfigColumnPeer::CELL_TEMPLATE_JS)) $criteria->add(ListerConfigColumnPeer::CELL_TEMPLATE_JS, $this->cell_template_js);
        if ($this->isColumnModified(ListerConfigColumnPeer::DIJIT_WIDGET_TEMPLATE)) $criteria->add(ListerConfigColumnPeer::DIJIT_WIDGET_TEMPLATE, $this->dijit_widget_template);
        if ($this->isColumnModified(ListerConfigColumnPeer::DIJIT_WIDGET_SET_VALUE_JS)) $criteria->add(ListerConfigColumnPeer::DIJIT_WIDGET_SET_VALUE_JS, $this->dijit_widget_set_value_js);
        if ($this->isColumnModified(ListerConfigColumnPeer::COLUMN_STYLE_CSS)) $criteria->add(ListerConfigColumnPeer::COLUMN_STYLE_CSS, $this->column_style_css);
        if ($this->isColumnModified(ListerConfigColumnPeer::SORT_ORDER)) $criteria->add(ListerConfigColumnPeer::SORT_ORDER, $this->sort_order);
        if ($this->isColumnModified(ListerConfigColumnPeer::SORTBY_ORDER)) $criteria->add(ListerConfigColumnPeer::SORTBY_ORDER, $this->sortby_order);
        if ($this->isColumnModified(ListerConfigColumnPeer::SORTBY_DIRECTION)) $criteria->add(ListerConfigColumnPeer::SORTBY_DIRECTION, $this->sortby_direction);
        if ($this->isColumnModified(ListerConfigColumnPeer::TRUNCATE_CHARS)) $criteria->add(ListerConfigColumnPeer::TRUNCATE_CHARS, $this->truncate_chars);
        if ($this->isColumnModified(ListerConfigColumnPeer::TOOLTIP_JS_EXPRESSION)) $criteria->add(ListerConfigColumnPeer::TOOLTIP_JS_EXPRESSION, $this->tooltip_js_expression);
        if ($this->isColumnModified(ListerConfigColumnPeer::TOOLTIP_URL_JS_EXPRESSION)) $criteria->add(ListerConfigColumnPeer::TOOLTIP_URL_JS_EXPRESSION, $this->tooltip_url_js_expression);
        if ($this->isColumnModified(ListerConfigColumnPeer::TOOLTIP_MAX_WIDTH)) $criteria->add(ListerConfigColumnPeer::TOOLTIP_MAX_WIDTH, $this->tooltip_max_width);
        if ($this->isColumnModified(ListerConfigColumnPeer::TOOLTIP_DELAY_MSEC)) $criteria->add(ListerConfigColumnPeer::TOOLTIP_DELAY_MSEC, $this->tooltip_delay_msec);

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether or not they have been modified.
     *
     * @return Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria()
    {
        $criteria = new Criteria(ListerConfigColumnPeer::DATABASE_NAME);
        $criteria->add(ListerConfigColumnPeer::LISTER_CONFIG_COLUMN_ID, $this->lister_config_column_id);

        return $criteria;
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getListerConfigColumnId();
    }

    /**
     * Generic method to set the primary key (lister_config_column_id column).
     *
     * @param  int $key Primary key.
     * @return void
     */
    public function setPrimaryKey($key)
    {
        $this->setListerConfigColumnId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     * @return boolean
     */
    public function isPrimaryKeyNull()
    {

        return null === $this->getListerConfigColumnId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of ListerConfigColumn (or compatible) type.
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param boolean $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws PropelException
     */
    public function copyInto($copyObj, $deepCopy = false, $makeNew = true)
    {
        $copyObj->setListerConfigId($this->getListerConfigId());
        $copyObj->setName($this->getName());
        $copyObj->setSortableFlag($this->getSortableFlag());
        $copyObj->setEditableFlag($this->getEditableFlag());
        $copyObj->setHiddenFlag($this->getHiddenFlag());
        $copyObj->setShowSummaryFlag($this->getShowSummaryFlag());
        $copyObj->setWidth($this->getWidth());
        $copyObj->setCellTemplate($this->getCellTemplate());
        $copyObj->setCellTemplateJs($this->getCellTemplateJs());
        $copyObj->setDijitWidgetTemplate($this->getDijitWidgetTemplate());
        $copyObj->setDijitWidgetSetValueJs($this->getDijitWidgetSetValueJs());
        $copyObj->setColumnStyleCss($this->getColumnStyleCss());
        $copyObj->setSortOrder($this->getSortOrder());
        $copyObj->setSortbyOrder($this->getSortbyOrder());
        $copyObj->setSortbyDirection($this->getSortbyDirection());
        $copyObj->setTruncateChars($this->getTruncateChars());
        $copyObj->setTooltipJsExpression($this->getTooltipJsExpression());
        $copyObj->setTooltipUrlJsExpression($this->getTooltipUrlJsExpression());
        $copyObj->setTooltipMaxWidth($this->getTooltipMaxWidth());
        $copyObj->setTooltipDelayMsec($this->getTooltipDelayMsec());

        if ($deepCopy && !$this->startCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);
            // store object hash to prevent cycle
            $this->startCopy = true;

            //unflag object copy
            $this->startCopy = false;
        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setListerConfigColumnId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param boolean $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return ListerConfigColumn Clone of current object.
     * @throws PropelException
     */
    public function copy($deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Returns a peer instance associated with this om.
     *
     * Since Peer classes are not to have any instance attributes, this method returns the
     * same instance for all member of this class. The method could therefore
     * be static, but this would prevent one from overriding the behavior.
     *
     * @return ListerConfigColumnPeer
     */
    public function getPeer()
    {
        if (self::$peer === null) {
            self::$peer = new ListerConfigColumnPeer();
        }

        return self::$peer;
    }

    /**
     * Declares an association between this object and a ListerConfig object.
     *
     * @param                  ListerConfig $v
     * @return ListerConfigColumn The current object (for fluent API support)
     * @throws PropelException
     */
    public function setListerConfig(ListerConfig $v = null)
    {
        if ($v === null) {
            $this->setListerConfigId(NULL);
        } else {
            $this->setListerConfigId($v->getListerConfigId());
        }

        $this->aListerConfig = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ListerConfig object, it will not be re-added.
        if ($v !== null) {
            $v->addListerConfigColumn($this);
        }


        return $this;
    }


    /**
     * Get the associated ListerConfig object
     *
     * @param PropelPDO $con Optional Connection object.
     * @param $doQuery Executes a query to get the object if required
     * @return ListerConfig The associated ListerConfig object.
     * @throws PropelException
     */
    public function getListerConfig(PropelPDO $con = null, $doQuery = true)
    {
        if ($this->aListerConfig === null && ($this->lister_config_id !== null) && $doQuery) {
            $this->aListerConfig = ListerConfigQuery::create()->findPk($this->lister_config_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aListerConfig->addListerConfigColumns($this);
             */
        }

        return $this->aListerConfig;
    }

    /**
     * Clears the current object and sets all attributes to their default values
     */
    public function clear()
    {
        $this->lister_config_column_id = null;
        $this->lister_config_id = null;
        $this->name = null;
        $this->sortable_flag = null;
        $this->editable_flag = null;
        $this->hidden_flag = null;
        $this->show_summary_flag = null;
        $this->width = null;
        $this->cell_template = null;
        $this->cell_template_js = null;
        $this->dijit_widget_template = null;
        $this->dijit_widget_set_value_js = null;
        $this->column_style_css = null;
        $this->sort_order = null;
        $this->sortby_order = null;
        $this->sortby_direction = null;
        $this->truncate_chars = null;
        $this->tooltip_js_expression = null;
        $this->tooltip_url_js_expression = null;
        $this->tooltip_max_width = null;
        $this->tooltip_delay_msec = null;
        $this->alreadyInSave = false;
        $this->alreadyInValidation = false;
        $this->alreadyInClearAllReferencesDeep = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);
    }

    /**
     * Resets all references to other model objects or collections of model objects.
     *
     * This method is a user-space workaround for PHP's inability to garbage collect
     * objects with circular references (even in PHP 5.3). This is currently necessary
     * when using Propel in certain daemon or large-volume/high-memory operations.
     *
     * @param boolean $deep Whether to also clear the references on all referrer objects.
     */
    public function clearAllReferences($deep = false)
    {
        if ($deep && !$this->alreadyInClearAllReferencesDeep) {
            $this->alreadyInClearAllReferencesDeep = true;
            if ($this->aListerConfig instanceof Persistent) {
              $this->aListerConfig->clearAllReferences($deep);
            }

            $this->alreadyInClearAllReferencesDeep = false;
        } // if ($deep)

        $this->aListerConfig = null;
    }

    /**
     * return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(ListerConfigColumnPeer::DEFAULT_STRING_FORMAT);
    }

    /**
     * return true is the object is in saving state
     *
     * @return boolean
     */
    public function isAlreadyInSave()
    {
        return $this->alreadyInSave;
    }

}
