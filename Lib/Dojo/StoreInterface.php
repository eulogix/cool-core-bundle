<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Dojo;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

interface StoreInterface {

    /**
     * @param StoreRequestInterface $request
     * @return StoreResponse
     */
    public function execute(StoreRequestInterface $request);

    /**
     * @param StoreRequestInterface $dojoRequest
     * @return StoreResponse
     */
    public function executeQuery(StoreRequestInterface $dojoRequest);

    /**
     * @param StoreRequestInterface $dojoRequest
     * @return StoreResponse
     */
    public function executePut(StoreRequestInterface $dojoRequest);

    /**
     * @param StoreRequestInterface $dojoRequest
     * @return StoreResponse
     */
    public function executeRemove(StoreRequestInterface $dojoRequest);
}