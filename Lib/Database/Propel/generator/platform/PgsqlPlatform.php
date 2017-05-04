<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Database\Propel\generator\platform;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class PgsqlPlatform extends \PgsqlPlatform {

    /**
     * Trivial modifications that avoids spitting an error like
     * psql:/tmp/SQLlMdpMc:317: ERROR:  sequence "XXXX_id_seq" does not exist
     *
     * @param \Table $table
     * @return string
     */
    protected function getDropSequenceDDL(\Table $table)
    {
        if ($table->getIdMethod() == \IDMethod::NATIVE && $table->getIdMethodParameters() != null) {
            $pattern = "
DROP SEQUENCE IF EXISTS %s;
";

            return sprintf($pattern,
                $this->quoteIdentifier(strtolower($this->getSequenceName($table)))
            );
        }
    }

}