<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Audit;

use Eulogix\Cool\Lib\DataSource\CoolValueMap;
use Eulogix\Cool\Lib\DataSource\DataSourceInterface;
use Eulogix\Cool\Lib\Form\Field\FieldInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class AuditSchema
{

    const FIELD_EVENT_ID = "aud_event_id";
    const FIELD_VALIDITY_RANGE = "aud_validity_range";
    const FIELD_VALIDITY_FROM = "aud_validity_from";
    const FIELD_VALIDITY_TO = "aud_validity_to";
    const FIELD_COOL_USER_ID = "aud_cool_user_id";
    const FIELD_VERSION = "aud_version";
    const FIELD_ACTION = "aud_action";
    const FIELD_CHANGED_FIELDS = "aud_changed_fields";
    const FIELD_TRANSACTION_ID = "aud_transaction_id";

    /**
     * @param DataSourceInterface $ds
     */
    public static function addFieldsToDs($ds)
    {
        $ds->addField(self::FIELD_EVENT_ID)->setType(\PropelTypes::BIGINT);
        $ds->addField(self::FIELD_VALIDITY_FROM)->setType(\PropelTypes::TIMESTAMP)->setControlType(FieldInterface::TYPE_DATETIME);
        $ds->addField(self::FIELD_VALIDITY_TO)->setType(\PropelTypes::TIMESTAMP)->setControlType(FieldInterface::TYPE_DATETIME);
        $ds->addField(self::FIELD_COOL_USER_ID)->setType(\PropelTypes::INTEGER)->setValueMap(CoolValueMap::getValueMapFor('core', 'core.account_setting', 'account_id'));
        $ds->addField(self::FIELD_VERSION)->setType(\PropelTypes::INTEGER);
        $ds->addField(self::FIELD_ACTION)->setType(\PropelTypes::VARCHAR);
        $ds->addField(self::FIELD_CHANGED_FIELDS)->setType(\PropelTypes::LONGVARCHAR);
        $ds->addField(self::FIELD_TRANSACTION_ID)->setType(\PropelTypes::BIGINT);
    }

    /**
     * return string[]
     */
    public static function getAuditFields() {
        return [
            self::FIELD_EVENT_ID,
            self::FIELD_VALIDITY_RANGE,
            self::FIELD_COOL_USER_ID,
            self::FIELD_VERSION,
            self::FIELD_ACTION,
            self::FIELD_CHANGED_FIELDS,
            self::FIELD_TRANSACTION_ID
        ];
    }

    /**
     * return string[]
     */
    public static function getSQLExpressions() {
        return [
            self::FIELD_EVENT_ID,
            self::FIELD_VALIDITY_RANGE,
            'lower('.self::FIELD_VALIDITY_RANGE.') AS '.self::FIELD_VALIDITY_FROM,
            'upper('.self::FIELD_VALIDITY_RANGE.') AS '.self::FIELD_VALIDITY_TO,
            self::FIELD_COOL_USER_ID,
            self::FIELD_VERSION,
            self::FIELD_ACTION,
            self::FIELD_CHANGED_FIELDS,
            self::FIELD_TRANSACTION_ID
        ];
    }

}