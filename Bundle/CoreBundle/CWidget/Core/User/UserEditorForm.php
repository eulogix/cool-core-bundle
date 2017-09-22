<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\CWidget\Core\User;

use Eulogix\Cool\Lib\Cool;
use Eulogix\Lib\Database\Postgres\PgUtils;
use Eulogix\Cool\Lib\DataSource\SimpleValueMap;
use Eulogix\Cool\Lib\Form\CoolForm;
use Eulogix\Cool\Lib\Widget\WidgetSlot;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class UserEditorForm extends CoolForm {

    public function build() {
        $h = Cool::getInstance()->getContainer()->getParameter('security.role_hierarchy.roles');

        $this->addFieldMultiSelect('ROLES_ALL')
            ->setUseChosen(true)
            ->setValueMap(new SimpleValueMap(array_keys($h)));

        parent::build();

        $rolesPgArray = $this->getField('roles')->getValue();
        $this->addFieldHidden('roles')->setValue($rolesPgArray);

        $this->getField('ROLES_ALL')->setValue( PgUtils::fromPGArray($rolesPgArray) );

        return $this;
    }

    public function onSubmit() {

        $parameters = $this->request->all();
        $this->rawFill($parameters);
        $vl = $this->getField('ROLES_ALL')->getValue();

        $this->request->set('roles', PgUtils::toPGArray($vl) );

        parent::onSubmit();
    }

    public function addDependantWidgets($databaseName, $tableName) {
        //parent::addDependantWidgets();
        if($this->getDSRecord()->isNew())
            return $this;

        $filter = json_encode(['account_id'=> $this->getRecordId()]);

        $this->setSlot('groups', new WidgetSlot('Eulogix\Cool\Lib\Lister\CoolLister', [
                'databaseName' => 'core',
                'tableName' => 'core.account_group_ref',
                '_filter'=>$filter]), "Groups");

        $this->setSlot('settings', new WidgetSlot('Eulogix\Cool\Lib\Lister\CoolLister', [
                'databaseName' => 'core',
                'tableName' => 'core.account_setting',
                '_filter'=>$filter]), "Settings");

        $this->setSlot('profiles', new WidgetSlot('Eulogix\Cool\Lib\Lister\CoolLister', [
                'databaseName' => 'core',
                'tableName' => 'core.account_profile_ref',
                'caller' => 'ACCOUNT_FORM',
                '_filter'=>$filter]), "Profiles");

        return $this;
    }

}