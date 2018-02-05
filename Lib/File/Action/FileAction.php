<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\File\Action;

use Eulogix\Cool\Lib\Traits\FileRepositoryHolder;
use Eulogix\Cool\Lib\Widget\Menu;
use Eulogix\Lib\File\Proxy\FileProxyInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

abstract class FileAction
{
    use FileRepositoryHolder;

    /**
     * @param FileProxyInterface $file
     * @return boolean
     */
    public abstract function appliesTo(FileProxyInterface $file);

    /**
     * @param Menu $menu
     */
    public abstract function populateContextualMenu(Menu $menu);
}