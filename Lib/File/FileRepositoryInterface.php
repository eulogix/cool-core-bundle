<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\File;

use Eulogix\Cool\Lib\File\Exception\ForbiddenException;
use Eulogix\Lib\File\Proxy\FileProxyCollectionInterface;
use Eulogix\Lib\File\Proxy\FileProxyInterface;
use Symfony\Component\HttpFoundation\ParameterBag;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

interface FileRepositoryInterface {

    const QUERY_NAME = 'name';
    const QUERY_EXTENDED = 'extended_query';
    const QUERY_SIZE_FROM = 'size_from';
    const QUERY_SIZE_TO = 'size_to';
    const QUERY_DATE_FROM = 'date_from';
    const QUERY_DATE_TO = 'date_to';

    const COLLISION_STRATEGY_OVERWRITE = 'overwrite';
    const COLLISION_STRATEGY_SKIP = 'skip';
    const COLLISION_STRATEGY_RENAME = 'rename';

    /**
     * @return ParameterBag
     */
    public function getParameters();

    /**
     * @param array $parameters
     * @return $this
     */
    public function setParameters(array $parameters = []);

    /**
     * Base permissions, these deal with file system limitations or other physical constraints.
     * Checks are made in the base get/delete/store methods
     * @return BaseFileRepositoryPermissions
     */
    public function getPermissions();

    /**
     * Permissions related to what the logged user can or can not do.
     * Checks are made in forms and APIs
     * @return BaseFileRepositoryPermissions
     */
    public function getUserPermissions();

    /**
     * returns a unique identifier for the repository, used to differentiate caches..
     * @return string
     */
    public function getUid();

    /**
     * @param string $path
     * @return $this
     */
    public function setWorkingDir($path);

    /**
     * @return string
     */
    public function getWorkingDir();

    /**
     * this function must always return a fully qualified path.
     * @param string $path
     * @return $this
     */
    public function getFQPath($path);

    /**
     * @param string $path
     * @return boolean
     */
    public function exists($path);

    /**
     * @param string $path
     * @return FileProxyInterface
     * @throws ForbiddenException
     * @throws \Exception
     */
    public function get($path);

    /**
     * @param string $path
     * @return $this
     * @throws ForbiddenException
     * @throws \Exception
     */
    public function delete($path);

    /**
     * @param string $path
     * @param string $target
     * @return string The new file path
     * @throws ForbiddenException
     * @throws \Exception
     */
    public function move($path, $target);

    /**
     * @param string $path
     * @param string $newName
     * @return string The new file id
     * @throws ForbiddenException
     * @throws \Exception
     */
    public function rename($path, $newName);

    /**
     * @param string $path
     * @param bool $recursive
     * @param bool $includeHidden
     * @return FileProxyCollectionInterface
     * @throws ForbiddenException
     * @throws \Exception
     */
    public function getChildrenOf($path = null, $recursive = false, $includeHidden = false);

    /**
     * @param FileProxyInterface $file
     * @param string $path
     * @param string $collisionStrategy overwrite|skip|append
     * @return FileProxyInterface a fileProxy representing the inserted file
     * @throws ForbiddenException
     * @throws \Exception
     */
    public function storeFileAt(FileProxyInterface $file, $path = null, $collisionStrategy = self::COLLISION_STRATEGY_OVERWRITE);

    /**
     * @param string $path
     * @param string $folderName
     * @return FileProxyInterface a fileProxy representing the created folder
     * @throws ForbiddenException
     * @throws \Exception
     */
    public function createFolder($path, $folderName = null);

    /**
     * @param string $path
     * @param array $properties
     * @param bool $merge
     * @return $this
     * @throws ForbiddenException
     * @throws \Exception
     */
    public function setFileProperties($path, array $properties, $merge=false);

    /**
     * @param string $path
     * @param string $decodingsPrefix if set, includes all the decodifications of the properties prefixing them
     * @return array
     */
    public function getFileProperties($path, $decodingsPrefix=null);

    /**
     * returns an array with all the properties for a set of files merged.
     * The array will contain a value only if all the files have the same value for any given property.
     * @param string $path
     * @param string $decodingsPrefix if set, includes all the decodifications of the properties prefixing them
     * @return array
     */
    public function getMergedFileProperties($path, $decodingsPrefix=null);

    /**
     * @param string $path
     * @param bool $recursive
     * @return FileProperty[]
     */
    public function getAvailableFileProperties($path, $recursive=false);

    /**
     * @param string $startPath
     * @param array $query
     * @param bool $recursive
     * @param int $offset
     * @param int $limit
     * @return FileProxyCollectionInterface
     */
    public function search($startPath = null, array $query = [], $recursive = true, $offset = 0, $limit = 100);

    /**
     * @param string $queryString
     * @return array
     */
    public function parseExtendedQuery($queryString);
}