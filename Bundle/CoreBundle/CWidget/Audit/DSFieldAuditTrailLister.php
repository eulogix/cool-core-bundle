<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Bundle\CoreBundle\CWidget\Audit;

use Eulogix\Cool\Lib\Audit\AuditSchema;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\DataSource\Classes\Audit\DSFieldAuditTrailDataSource;
use Eulogix\Cool\Lib\DataSource\CoolCrudDataSource;
use Eulogix\Cool\Lib\DataSource\DataSourceInterface;
use Eulogix\Cool\Lib\Lister\Lister;
use Eulogix\Cool\Lib\Widget\WidgetInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class DSFieldAuditTrailLister extends Lister {

    /**
     * @var WidgetInterface
     */
    protected $sourceWidget;

    /**
     * @var CoolCrudDataSource
     */
    protected $sourceDS;

    public function __construct($parameters = [])
    {
        parent::__construct($parameters);
        $this->sourceDS = null;

        if($this->parameters->has('widgetServerid')) {
            $this->sourceDS = $this->getSourceWidget()->getDataSource();
        }

        if(!($this->sourceDS instanceof CoolCrudDataSource))
            throw new \Exception("Could not instantiate a source DS, or source DS not an instance of CoolCrudDataSource");


        $this->setDataSource($this->getTrailDS($this->sourceDS, $parameters['fieldName']));

        $this->getAttributes()->set(self::ATTRIBUTE_READONLY, true);
        $this->getAttributes()->set(self::ATTR_SHOW_TOOLS_COLUMN, false);
    }

    /**
     * @param CoolCrudDataSource $cds
     * @param string $driverFieldName
     * @return DataSourceInterface
     */
    private function getTrailDS(CoolCrudDataSource $cds, $driverFieldName) {
        $p = json_decode($this->parameters->get('widgetParameters'), true);
        //we need to rewrite the record id that comes from the request with the fully built one the source widget may return
        if(isset($p[DataSourceInterface::RECORD_IDENTIFIER]) && method_exists($this->getSourceWidget(), 'getRecordIdForDSR')) {
            $p[DataSourceInterface::RECORD_IDENTIFIER] = $this->getSourceWidget()->getRecordIdForDSR();
        }
        $trailDS = new DSFieldAuditTrailDataSource($cds, $driverFieldName, $p);
        return $trailDS->build();
    }

    public function build() {
        parent::build();

        $this->getColumn(DSFieldAuditTrailDataSource::AUDITED_FIELD)->addColumnStyleCss("background-color:rgba(0,200,0, 0.1);")->setSortOrder(1)->setWidth(200);
        $this->getColumn(AuditSchema::FIELD_VALIDITY_FROM)->setSortOrder(2)->setWidth(100);
        $this->getColumn(AuditSchema::FIELD_COOL_USER_ID)->setSortOrder(3)->setWidth(150);
        $this->getColumn(AuditSchema::FIELD_ACTION)->setSortOrder(4)->setWidth(50);
        $this->getColumn(AuditSchema::FIELD_CHANGED_FIELDS)->setSortOrder(5)->setWidth(150);
        $this->getColumn(AuditSchema::FIELD_VERSION)->setSortOrder(6)->setWidth(50);

        return $this;
    }

    /**
     * @inheritdoc
     */
    public function getId() {
        /**
         * @var DSFieldAuditTrailDataSource $ds
         */
        $ds = $this->getDataSource();
        return 'AUDIT_TRAIL_LISTER_'.$ds->getUid();
    }

    /**
     * @inheritdoc
     */
    public function getTranslatorDomains() {
        return [$this->getSourceWidget()->getId(), 'DS_FIELD_AUDIT_TRAIL'];
    }

    /**
     * @return array
     */
    public function getInitialSort() {
        return [
            AuditSchema::FIELD_VALIDITY_FROM  =>  self::SORT_DESC,
        ];
    }

    /**
     * returns the instance of the widget being edited, configured with its request parameters
     * @return WidgetInterface
     */
    protected function getSourceWidget() {
        if($this->sourceWidget)
            return $this->sourceWidget;
        $p = json_decode($this->parameters->get('widgetParameters'), true);
        $widget = Cool::getInstance()->getFactory()->getWidgetFactory()->getWidget($this->parameters->get('widgetServerid'), $p);
        $widget->build();
        return $this->sourceWidget = $widget;
    }
}