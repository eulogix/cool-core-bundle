<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Database\Propel\generator\builder;

use Eulogix\Cool\Lib\Database\Propel\generator\model\PropelBuilderTableProxy;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class PHP5ObjectBuilder extends \PHP5ObjectBuilder {

    /**
     * Adds class phpdoc comment and opening of class.
     *
     * @param string &$script The script will be modified in this method.
     */
    protected function addClassOpen(&$script) {
        parent::addClassOpen($script);

        $this->declareClass("Money\\Money");
        $this->declareClass("Money\\Currency");
    }

    /**
     * Adds a normal (non-temporal) getter method.
     *
     * @param string &$script The script will be modified in this method.
     * @param \Column $col     The current column.
     *
     * @see        parent::addColumnAccessors()
     */
    protected function addDefaultAccessor(&$script, \Column $col)
    {
        parent::addDefaultAccessor($script, $col);

        if($col->getType() == \PropelTypes::DECIMAL) {
            $this->addMoneyAccessor($script, $col);
        }

    }

    /**
     * @param string $script
     * @param \Column $col
     */
    protected function addMoneyAccessor(&$script, $col) {
        $this->addMoneyAccessorComment($script, $col);
        $this->addMoneyAccessorOpen($script, $col);
        $this->addMoneyAccessorBody($script, $col);
        $this->addDefaultAccessorClose($script, $col);
    }

    /**
     * Add the comment for a money accessor method (a getter)
     *
     * @param string &$script The script will be modified in this method.
     * @param \Column $col     The current column.
     *
     * @see        addMoneyAccessor()
     **/
    public function addMoneyAccessorComment(&$script, \Column $col)
    {
        $clo = strtolower($col->getName());

        $script .= "
    /**
     * Get the [$clo] column value as a Money object.
     * " . $col->getDescription() ."
     * @param string \$currency
     * @param int \$precision
     ";

        if ($col->isLazyLoad()) {
            $script .= "
     * @param PropelPDO \$con An optional PropelPDO connection to use for fetching this lazy-loaded column.";
        }
        $script .= "
     * @return Money
     */";
    }

    /**
     * Adds the function declaration for a default accessor
     *
     * @param string &$script The script will be modified in this method.
     * @param \Column $col     The current column.
     *
     * @see        addDefaultAccessor()
     **/
    private function addMoneyAccessorOpen(&$script, $col)
    {
        $cfc = $col->getPhpName();
        $visibility = $col->getAccessorVisibility();

        $script .= "
    " . $visibility . " function get{$cfc}AsMoney(\$currency = 'EUR', \$precision = 2";
        if ($col->isLazyLoad()) {
            $script .= ", PropelPDO \$con = null";
        }
        $script .= ")
    {";
    }

    /**
     * Adds the function body for a money accessor method
     *
     * @param string &$script The script will be modified in this method.
     * @param \Column $col     The current column.
     *
     * @see        addMoneyAccessor()
     **/
    protected function addMoneyAccessorBody(&$script, \Column $col)
    {
        $cfc = $col->getPhpName();
        $clo = strtolower($col->getName());
        if ($col->isLazyLoad()) {
            $script .= $this->getAccessorLazyLoadSnippet($col);
        }

        $script .= "

        return Money::fromDecimal(\$this->$clo, new Currency(\$currency), \$precision);";
    }

    /**
     * avoid model bloat by limiting fk methods and boilerplate in the core model
     *
     * @return PropelBuilderTableProxy
     */
    public function getTable()
    {
        $parentTable = parent::getTable();
        if($parentTable instanceof PropelBuilderTableProxy)
            return $parentTable;
        return new PropelBuilderTableProxy( $parentTable );
    }
}