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

interface TempManagerInterface
{
    /**
     * @param string $uploadedName
     * @param string $content
     * @return string
     */
    public function storeFile($uploadedName, $content);

    /**
     * @param string $id
     * @return string
     */
    public function getFileName($id);

    /**
     * @param string $id
     * @return string
     */
    public function getFileContent($id);

    /**
     * generates a temporary, one-time key that is used to later retrieve the file with a simple URL
     * @param FileProxyInterface $fp
     * @return string
     */
    public function getTempKeyFromFileProxy(FileProxyInterface $fp);

    /**
     * @param $key
     * @return FileProxyInterface|null
     */
    public function getFileProxyFromTempKey($key);

    /**
     * @param FileProxyInterface $fp
     * @return string
     */
    public function getDownloadUrlFromFileProxy(FileProxyInterface $fp);
}