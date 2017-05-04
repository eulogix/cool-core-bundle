<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\CWidget\Core\Files;

use Eulogix\Cool\Lib\DataSource\ValueMapInterface;
use Eulogix\Cool\Lib\File\FileProperty;
use Eulogix\Cool\Lib\File\FileRepositoryFactory;
use Eulogix\Cool\Lib\File\FileRepositoryInterface;
use Eulogix\Cool\Lib\Form\Field\FieldInterface;
use Eulogix\Cool\Lib\Form\Field\XhrPicker;
use Eulogix\Cool\Lib\Form\Form;
use Eulogix\Cool\Lib\Widget\Message;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class FilePropertiesForm extends Form {

    /**
     * @var FileRepositoryInterface
     */
    private $repo;

    /**
     * @var FileProperty[]
     */
    private $properties;

    public function build() {
        parent::build();

        $parameters = $this->parameters->all();
        $id = $parameters['repositoryId'];
        $this->repo = FileRepositoryFactory::fromId($id);
        $this->repo->setParameters($parameters);

        $filePaths = explode(';',$parameters['filePaths']);

        if(count($filePaths)>1)
            $this->addMessageWarning("Working on {1} files!",count($filePaths));

        $allProperties = [];
        foreach($filePaths as $fp)
            $allProperties[] = $this->repo->getAvailableFileProperties($fp);
        $this->properties = count($allProperties) == 1 ? array_pop($allProperties) : call_user_func_array('array_intersect_key',$allProperties);

        foreach($this->properties as $prop)
            $this->addField($prop->getName(), $this->fieldFactory($prop->getControlType(), $prop->getValueMap()));

        $this->rawFill( $this->repo->getMergedFileProperties($filePaths) );

        $this->addFieldSubmit('save');
        return $this;
    }

    public function onSubmit() {
        $parameters = $this->request->all();
        $filePaths = explode(';',$this->parameters->get('filePaths'));
        $this->rawFill( $parameters );
        $this->messages = [];

        if($this->validate( array_keys($parameters) ) ) {
            foreach($filePaths as $path)
                $this->repo->setFileProperties($path, array_diff_assoc($parameters,["save"=>'']));
            $this->addMessageInfo("SAVED");
        } else {
            $this->addMessage(Message::TYPE_ERROR, "NOT VALIDATED");
        }
    }

    public function getLayout() {
        $l = "<FIELDS>\n";
        foreach($this->properties as $prop) {
            $l.=$prop->getName().":250\n";
        }
        $l.= "</FIELDS>\n<FIELDS>save|align=center</FIELDS>";
        return $l;
    }

    /**
     * @inheritdoc
     */
    public function fieldFactory($fieldType, ValueMapInterface $valueMap=null) {
        if($fieldType == FieldInterface::TYPE_SELECT && $valueMap && $valueMap->getValuesNumber() > 50) {
            $field = new XhrPicker($this);
            $field->setValueMap($valueMap);
            return $field;
        }
        return parent::fieldFactory($fieldType, $valueMap);
    }

}