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

class QueryBuilder extends \QueryBuilder {

    /**
     * Adds the findPks method for this object.
     *
     * @param string &$script The script will be modified in this method.
     */
    protected function addFindPks(&$script)
    {
        parent::addFindPks($script);

        $this->declareClasses('PropelPDO', 'Propel');
        $table = $this->getTable();
        $pks = $table->getPrimaryKey();
        $count = count($pks);
        $class = $this->getStubObjectBuilder()->getClassname();
        $script .= "
    /**
     * Find objects by primary key while maintaining the original sort order of the keys
     * <code>";
        if ($count === 1) {
            $script .= "
     * \$objs = \$c->findPksKeepingKeyOrder(array(12, 56, 832), \$con);
     ";
        } else {
            $script .= "
     * \$objs = \$c->findPksKeepingKeyOrder(array(array(12, 56), array(832, 123), array(123, 456)), \$con);";
        }
        $script .= "
     * </code>
     * @param     array \$keys Primary keys to use for the query
     * @param     PropelPDO \$con an optional connection object
     *
     * @return {$class}[]
     */
    public function findPksKeepingKeyOrder(\$keys, \$con = null)
    {
        if (\$con === null) {
            \$con = Propel::getConnection(\$this->getDbName(), Propel::CONNECTION_READ);
        }
        \$ret = array();

        foreach(\$keys as \$key)
            \$ret[ \$key ] = \$this->findPk(\$key, \$con);

        return \$ret;
    }
";
    }

    /**
     * avoid model bloat by limiting fk methods and boilerplate in the core model
     *
     * @return PropelBuilderTableProxy
     */
    public function getTable()
    {
        $parentTable = parent::getTable();
        return new PropelBuilderTableProxy( $parentTable );
    }

}