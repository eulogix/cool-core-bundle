<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Form\Field;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class HTMLEditor extends Field {

    protected $coolDojoWidget = "cool/controls/HTMLEditor";
    protected $type = self::TYPE_HTML;

    const CK_UPLOAD_REPO_ID = 'ck_upload_repo_id';
    const CK_UPLOAD_REPO_PATH = 'ck_upload_repo_path';

    /**
     * @return $this
     */
    public function useCKEditor() {
        $this->coolDojoWidget = "cool/controls/HTMLEditorCK";
        return $this;
    }

    /**
     * @param string $repoId
     * @param string $path
     */
    public function setUploadRepoId($repoId, $path) {
        $this->getParameters()->set(self::CK_UPLOAD_REPO_ID, $repoId);
        $this->getParameters()->set(self::CK_UPLOAD_REPO_PATH, $path);
    }
}