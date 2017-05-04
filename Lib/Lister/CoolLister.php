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

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Database\Schema;
use Eulogix\Cool\Lib\DataSource\CoolCrudDataSource;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CoolLister extends Lister {
    
    var $databaseName = "";
    var $tableName = "";

    var $instant = null;

    /**
     * initialize the lister with an initial set of parameters.
     * You should provide the lister with any parameter that may influence the build process directly in the constructor
     *
     * @param array $parameters
     * @throws \Exception
     * @internal param array $request
     */
    public function __construct($parameters = [])
    {
        parent::__construct($parameters);
        if(!$this->parameters->has('databaseName') || !$this->parameters->has('tableName')) {
            throw new \Exception("missing parameters");
        }
        $this->databaseName = $this->parameters->get('databaseName');
        $this->tableName = $this->parameters->get('tableName');

        $this->setDataSource( CoolCrudDataSource::fromSchemaAndTable( $this->databaseName, $this->tableName, $this->parameters->get('instant'))->build() );
    }

    /**
     * @inheritdoc
     */
    public function build() {
        parent::build();
        $this->addAction('newItem')->setOnClick("widget.openRowEditor();");
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        return "COOL_LISTER_".$this->databaseName."_".$this->tableName;
    }

    /**
    * returns the schema for this lister
    * @returns Schema
    */
    public function getCoolSchema() {
        return Cool::getInstance()->getSchema($this->databaseName);
    }

    /**
     * @inheritdoc
     */
    public function getDefaultEditorServerId() {
        if($db = $this->getCoolSchema()) {
            return $db->getDictionary()->getPropelTableMap($this->tableName)->getCoolDefaultEditor();
        }
        return parent::getEditorServerId();
    }
    
    /**
    * @inheritdoc
    */
    public function getTranslator() {
        $t = parent::getTranslator();
        //we add the common database domain to have a default set of translations for field names and database objects
        $t->addDomain( $this->getCoolSchema()->getTranslationDomain( $this->tableName ) );
        return $t;
    }

    /**
     * @return string|null
     */
    public function getDefaultFilterWidget()
    {
        return 'Eulogix\Cool\Lib\Lister\Filter\BaseFilterForm';
    }
}
