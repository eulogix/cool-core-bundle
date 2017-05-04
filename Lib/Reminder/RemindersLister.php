<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Reminder;

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Lister\Lister;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class RemindersLister extends Lister {

    /**
     * @inheritdoc
     */
    public function __construct($parameters = [])
    {
        parent::__construct($parameters);
        $manager = Cool::getInstance()->getFactory()->getRemindersManager();
        if($provider = $manager->getProvider($parameters['provider'])) {
            $this->setDataSource($provider->getDetailsDataSource());
        }
        $this->setShowToolsColumn(false);
    }

    /**
     * @inheritdoc
     */
    public function build() {
        parent::build();
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getDefaultEditorServerId() { }

    /**
     * @inheritdoc
     */
    public function getId() {
        return "COOL_REMINDERS_LISTER_".$this->getParameters()->get('provider');
    }

    /**
     * @inheritdoc
     */
    public function getTranslatorDomains() {
        $d = $this->getParameters()->get('translationDomain');
        return $d ? [$d] : parent::getTranslatorDomains();
    }

    public function reloadCounters() {
        $this->reloadRows();
        $this->addCommandJs("widget.remindersPanel.reloadData();");
    }

}