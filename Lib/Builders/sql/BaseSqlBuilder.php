<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Builders\sql;

use Eulogix\Cool\Bundle\CoreBundle\Twig\CoolLoader;
use Eulogix\Cool\Lib\Cool;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class BaseSqlBuilder {

    /**
     * @var string
     */
    protected $schemaName;

    public function __construct($schemaName) {
        $this->schemaName = $schemaName;
    }

    /**
     * @return \Eulogix\Cool\Lib\Dictionary\Dictionary
     */
    protected function getDictionary() {
        return $this->getSchema()->getDictionary();
    }

    /**
     * @return \Eulogix\Cool\Lib\Database\Schema
     */
    protected function getSchema()
    {
        return Cool::getInstance()->getSchema($this->schemaName);
    }

    /**
     * @param string $rawSql
     * @return string
     */
    public function getDecoratedSQL($rawSql) {
        $sql ="SET lc_messages TO 'en_US.UTF-8';\n\n";

        if( !Cool::getInstance()->getSchema($this->schemaName)->isMultiTenant() ) {
            $sql.= "SET SCHEMA '{$this->schemaName}';\n";
        }
        return $sql.$rawSql;
    }

    /**
     * @param string $script
     * @param string $targetFile
     */
    public function outputScript($script, $targetFile) {
        file_put_contents($targetFile, $script);
    }

    /**
     * @param string $buffer
     * @param array $variables
     * @return string
     * @throws \Exception
     */
    public function processAsTwigTemplate($buffer, $variables = []) {
        $myTwig = new \Twig_Environment(new CoolLoader());

        $lexer = new \Twig_Lexer($myTwig, array(
            'tag_comment'   => array('{#', '#}'),
            'tag_block'     => array('{{%', '%}}'),
            'tag_variable'  => array('[[', ']]'), // was array('{{', '}}')
            'interpolation' => array('#{', '}'),
        ));

        $myTwig->setLexer($lexer);

        $buf = $myTwig->render( $buffer, $variables);

        return $buf;
    }

}