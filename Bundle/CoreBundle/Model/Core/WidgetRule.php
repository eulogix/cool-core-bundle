<?php

namespace Eulogix\Cool\Bundle\CoreBundle\Model\Core;

use Eulogix\Cool\Bundle\CoreBundle\Model\Core\om\BaseWidgetRule;

class WidgetRule extends BaseWidgetRule
{

    const EVALUATION_TYPE_BEFORE_DEFINITION = "BEFORE_DEFINITION";
    const EVALUATION_TYPE_BEFORE_VALIDATION = "BEFORE_VALIDATION";
    const EVALUATION_TYPE_ON_LOAD = "ON_LOAD";

}
