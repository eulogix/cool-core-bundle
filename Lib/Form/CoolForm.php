<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Form;

use Eulogix\Cool\Lib\Cool;


use Eulogix\Cool\Lib\DataSource\CoolCrudDataSource;
use Eulogix\Cool\Lib\DataSource\CoolDataSource;

class CoolForm extends DSCRUDForm  {
    
    var $databaseName = "";
    var $tableName = "";

    /**
     * initialize the form with an initial set of parameters.
     * You should provide the lister with any parameter that may influence the build process directly in the constructor
     * CoolForm requires at least databaseName and tableName
     *
     * @param array $parameters
     * @throws \Exception
     */
    public function __construct($parameters = [])
    {
        parent::__construct($parameters);

        $paramDb = $this->parameters->get('databaseName');
        $paramTb = $this->parameters->get('tableName');

        if( (!$this->databaseName && !$paramDb) ||
            (!$this->tableName && !$paramTb) ) {
            throw new \Exception("missing parameters");
        }

        if($paramDb) $this->databaseName = $paramDb;
        if($paramTb) $this->tableName = $paramTb;

        $this->setDataSource( CoolCrudDataSource::fromSchemaAndTable( $this->databaseName, $this->tableName)->build() );
    }
    
    /**
    * @inheritdoc
    */
    public function getId() {
        return "COOL_CRUD_".$this->databaseName."_".$this->tableName;
    }
    
    /**
    * builds the form adding all the fields from the source table
    * @inheritdoc
    */
    public function build() {
        parent::build();
        $this->addDependantWidgets($this->databaseName, $this->tableName);
        return $this;
    }     

    /**
    * adds as slots all the dependant widgets of this table
    * 
    */
    public function addDependantWidgets($databaseName, $tableName) {
        if(($db = Cool::getInstance()->getSchema($databaseName)) && !$this->getDSRecord()->isNew()) {
            $widgets = $db->getDictionary()->getPropelTableMap($tableName)->getDependantWidgets();
            foreach($widgets as $widget) {
                foreach($widget['filter'] as $foreign=>&$local) {
                    $local = $this->getDSRecord()->get($local);                    
                }
                $this->setSlot($widget['parameters']['tableName'], new $widget['slot']($widget['widget'], array_merge($widget['parameters'], ['_filter'=>json_encode($widget['filter'])])), $widget['group']);
            }          
        }
    }
    
    /**
    * returns the database for this form
    * @returns \Eulogix\Cool\Lib\Database\Schema
    */
    public function getSchema() {
        return Cool::getInstance()->getSchema($this->databaseName);
    }
    
    /**
    * @inheritdoc
    */
    public function getTranslator() {
        if(!$this->translator) {
            $t = parent::getTranslator();
            //we add the common database domain to have a default set of translations for field names and database objects
            //and we do that only on the first call
            $t->addDomain( $this->getSchema()->getTranslationDomain( $this->tableName ) );
        }
        return parent::getTranslator();
    }
    
}