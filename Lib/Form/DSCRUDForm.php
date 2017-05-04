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
use Eulogix\Cool\Lib\Form\Event\FormEvent;
use Eulogix\Lib\Validation\ConstraintBuilder;
use Eulogix\Cool\Lib\Widget\Message;

use Eulogix\Cool\Lib\DataSource\DSRequest;
use Eulogix\Cool\Lib\DataSource\DSRecord;
use Eulogix\Cool\Lib\DataSource\DataSourceInterface as D;

class DSCRUDForm extends Form  {

    const PARAM_DS_UID = "_ds_id";

    const EVENT_RECORD_SAVED = "record_saved";

    /**
    * @var DSRecord
    */
    private $record;

    public function __construct($parameters = [])
    {
        parent::__construct($parameters);

        if($dsId = $this->parameters->get(self::PARAM_DS_UID)) {
            if( !$this->getDataSource() &&
                ($ds = Cool::getInstance()->getFactory()->getDataSourceManager()->getDataSource($dsId))    ) {
                    $this->setDataSource($ds);
            }
            //we don't want to propagate that
            $this->parameters->remove(self::PARAM_DS_UID);
        }

    }

    public function clear() {
        $this->setDSRecord(null);
        return parent::clear();
    }

    /**
    * @inheritdoc
    */
    public function build() {
        parent::build();

        $this->addDataSourceFields();
        
        $record = $this->getDSRecord();
        if($record && !$record->isNew()) {
            $this->fill( $record->getValues() );
        }

        if(!$this->getReadOnly())
            $this->addFieldSubmit("save");

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addDataSourceFields($dataSource=null) {

        parent::addDataSourceFields($dataSource);

        /**
         * filter fields are always hidden
         */
        if(/*$this->getDSRecord()->isNew() && */$this->parameters->has('_filter')) {
            if( $fields = json_decode($this->parameters->get('_filter')) ) {
                foreach($fields as $f=>$v) {
                    /*if($field = $this->getField($f)) {
                        $field->setValue($v);
                        $field->setReadOnly(true);
                    }*/
                    $this->addFieldHidden($f)->setValue($v);
                }
            }
        }

        $fieldNames = $this->getFieldNames();
        foreach($fieldNames as $fieldName) {
            if($dv = $this->getDefaultValueFor($fieldName))
                $this->getField($fieldName)->setRawValue( $dv );
        }

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function addDataSourceField($fieldName, $dataSource=null)
    {
        $ds = $dataSource ? $dataSource : $this->getDataSource();
        if( $ds &&
            ($dsField = $ds->getField($fieldName)) &&
            ($formField = parent::addDataSourceField($fieldName, $dataSource)) ) {

            if($formField->getType()==$formField::TYPE_FILE && ($repo = $dsField->getFileRepository())) {
                $formField->setFileRepository( $repo );
            }

        } else throw new \Exception("Bad field name $fieldName or missing dataSource");

        return $formField;
    }

    /**
    * returns the datasource record that is linked to the form, if a PK is provided it is retrieved, otherwise it is created new
    * @return DSRecord
    */
    public function getDSRecord() {
        if($this->record)
            return $this->record;

        if($ds = $this->getDataSource()) {
            if($this->getRecordId()) {
                $dsr = new DSRequest();

                $dsr->setOperationType($dsr::OPERATION_TYPE_FETCH);
                if($this->getRecordId())
                    $dsr->setParameters( [D::RECORD_IDENTIFIER => $this->getRecordIdForDSR() ]);

                $dsresponse = $ds->execute($dsr);
                $this->record = $dsresponse->getDSRecord();

            } else $this->record = new DSRecord($ds);

            return $this->record;

        } else return new DSRecord(); //return an empty temporary record, no ds is set yet
    }
    
    /**
    * sets the record object
    * @param DSRecord $record
    * @return FormInterface
    */
    public function setDSRecord($record) {
        $this->record = $record;
        return $this;
    }
    
    /**
    * explicitly sets the record Id, which will be used to try to retrieve the DS Record and passed around in requests to the client
    * 
    * @param string $id
    */
    public function setRecordId($id) {
        if($this->parameters->get(D::RECORD_IDENTIFIER) != $id) {
            $this->setDSRecord(null);
        }
        $this->parameters->set(D::RECORD_IDENTIFIER, $id);
    }
    
    /**
    * returns the pk of the record currently edited
    * @return mixed
    */
    public function getRecordId() {
        return $this->parameters->get(D::RECORD_IDENTIFIER);
    }

    /**
     * override this if the recordid which comes from the request is incompatible with the datasource
     * (usually when the lister is fed by a different DS)
     * @return mixed
     */
    public function getRecordIdForDSR() {
        return $this->getRecordId();
    }

    /**
     * override this if the recordid which comes from the datasourceis incompatible with the widget originating the request
     * (usually when the lister is fed by a different DS)
     * @return mixed
     */
    public function getRecordIdFromDSR( $dsRecordId ) {
        return $dsRecordId;
    }

    /**
     * this function determines whether there is a default value for a given field.
     * Currently just a wrapper for a request lookup
     *
     * @param $fieldName
     * @return mixed
     */
    public function getDefaultValueFor($fieldName) {

        if($param = $this->parameters->get( $fieldName ))
            return $param;

        return false;
    }

    /**
    * submits the form values to the datasource in order to update or create a record
    * @return boolean TRUE for success, FALSE otherwise
    */
    public function updateOrCreateRecord() {
        //CRUD operation is delegated to the dataSource
        $record = $this->getDSRecord();
        if($ds = $this->getDataSource()) {

            $dsr = new DSRequest();

            $requestValues = $this->getValues(array_keys( $this->request->all() ));

            $dsrParams = $this->parameters->all();
            if($this->getRecordId())
                $dsrParams[ D::RECORD_IDENTIFIER ] = $this->getRecordIdForDSR();

            $dsr->setOperationType( $record->isNew() ? $dsr::OPERATION_TYPE_ADD : $dsr::OPERATION_TYPE_UPDATE )
                ->setOldValues( $record->getValues() )
                ->setValues( $requestValues )
                ->setParameters( $dsrParams );
            
            $dsresponse = $ds->execute($dsr);
            
            switch($dsresponse->getStatus()) {
                
                case $dsresponse::STATUS_TRANSACTION_SUCCESS : {
                    
                    $this->clear()->build();
                    $this->setDSRecord(null);
                    $this->setRecordId( $this->getRecordIdFromDSR( $dsresponse->getAttribute(D::RECORD_IDENTIFIER) ) );
                    $this->fill( $this->getDSRecord()->getValues() );
                    $this->addEvent("recordSaved");
                    $this->dispatcher->dispatch(self::EVENT_RECORD_SAVED, new FormEvent($this));

                    $this->addMessage(Message::TYPE_INFO,"SAVED");    
                    
                    return true;
                    
                    break;
                }
                case $dsresponse::STATUS_TRANSACTION_FAILED : 
                case $dsresponse::STATUS_VALIDATION_ERROR : {
                    $this->addMessage(Message::TYPE_ERROR, "DATASOURCE_ERROR");
                    //something went wrong, we propagate the datasource errors to the relevant form fields
                    $this->mergeErrorReport($dsresponse->getErrorReport());
                    break;                      
                }
            }
            
        } else {
            $this->addMessage(Message::TYPE_ERROR, "NO_DATASOURCE");
        }    
        
        return false;
    }
    
    /**
    * by default we try to save or create the record
    */
    public function onSubmit() {     
        
        $parameters = $this->request->all();
        $this->rawFill( $parameters );

        if($this->validate( array_keys($parameters) ) ) {
            if( $this->updateOrCreateRecord() ) {
                //we don't call rebuild as it would clear the form, including messages and commands
                //this way we ensure that dependant widgets get shown on record insertion
                $this->build()->configure();
            }
        } else {
            $this->addMessage(Message::TYPE_ERROR, "NOT VALIDATED");
        }
    }

}