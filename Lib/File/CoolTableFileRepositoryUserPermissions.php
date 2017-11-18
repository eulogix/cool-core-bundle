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

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Database\Propel\CoolPropelObject;
use Eulogix\Cool\Lib\Database\Schema;
use Eulogix\Cool\Lib\Security\CoolUser;
use Eulogix\Cool\Lib\Translation\Translator;
use Eulogix\Cool\Lib\Translation\TranslatorInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 *
 * base settings are enumerated as constants below:
 *
 * these can be used in any of the forms below, from top to bottom
 * they increase specificity and thus override more generic settings
 * for example:
 *
 * 1) <schema>.files.canBrowse
 * 2) <schema>.<table>.files.canBrowse
   3) <schema>.<table>:<category>.files.canBrowse
 * 4) <schema>:<actualschema>.files.canBrowse
 * 5) <schema>:<actualschema>.<table>.files.canBrowse
 * 6) <schema>:<actualschema>.<table>:<category>.files.canBrowse
 *
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class CoolTableFileRepositoryUserPermissions extends BaseFileRepositoryPermissions {

    const SETTING_CAN_BROWSE = 'canBrowse';
    const SETTING_CAN_CREATE_FILES = 'canCreateFiles';
    const SETTING_CAN_DOWNLOAD_FILES = 'canDownloadFiles';
    const SETTING_CAN_DELETE = 'canDelete';
    const SETTING_CAN_OVERWRITE = 'canOverwrite';
    const SETTING_CAN_MOVE = 'canMove';
    const SETTING_CAN_SET_PROPERTIES = 'canSetProperties';

    /**
     * @param string $path
     * @param string $baseSetting
     * @param CoolUser $coolUser
     *
     * @return boolean
     */
    protected function checkSetting($path, $baseSetting, CoolUser $coolUser = null) {

        $cp = $this->getRepository()->parsePathId($path);
        $table = $cp['table'];
        $category = $cp['category'];

        $user = $coolUser ?? Cool::getInstance()->getLoggedUser();
        $schema = $this->getRepository()->getSchema()->getName();
        $actualSchema = $this->getRepository()->getSchema()->getCurrentSchema();

        $settingsToCheck = [
            1 => "{$schema}.files.{$baseSetting}",
            4 => "{$schema}.{$actualSchema}.files.{$baseSetting}",
        ];

        if($category && $table) {
            $settingsToCheck[ 3 ] = "{$schema}.{$table}:{$category}.files.{$baseSetting}";
            $settingsToCheck[ 6 ] = "{$schema}:{$actualSchema}.{$table}:{$category}.files.{$baseSetting}";
        }
        if($table) {
            $settingsToCheck[ 2 ] = "{$schema}.{$table}.files.{$baseSetting}";
            $settingsToCheck[ 5 ] = "{$schema}:{$actualSchema}.{$table}.files.{$baseSetting}";
        }

        for($i=6; $i>=1; $i--) {
            if(isset($settingsToCheck[$i])) {
                $settingValue = $user->getSetting($settingsToCheck[$i]);
                if($settingValue !== null)
                    return $settingValue == '1';
            }
        }

        return true;
    }

    /**
     * @inheritdoc
     */
    public function canBrowse($path)
    {
        return true;
    }

    /**
     * @inheritdoc
     */
    public function canCreateFileIn($path, $file = null)
    {
        return $this->checkSetting($path, self::SETTING_CAN_CREATE_FILES);
    }

    /**
     * @inheritdoc
     */
    public function canCreateDirIn($path, $dir = null)
    {
        return false;
    }

    /**
     * @inheritdoc
     */
    public function canDownloadFile($path)
    {
        return $this->checkSetting($path, self::SETTING_CAN_DOWNLOAD_FILES);
    }

    /**
     * @inheritdoc
     */
    public function canDelete($path)
    {
        return $this->checkSetting($path, self::SETTING_CAN_DELETE);
    }

    /**
     * @inheritdoc
     */
    public function canOverwrite($path)
    {
        return $this->canCreateFileIn($path) &&
               $this->checkSetting($path, self::SETTING_CAN_OVERWRITE);
    }

    /**
     * @inheritdoc
     */
    public function canMove($path, $targetDir)
    {
        return $this->checkSetting($path, self::SETTING_CAN_MOVE) &&
               $this->canOverwrite($targetDir);
    }

    /**
     * @inheritdoc
     */
    public function canRename($path, $newName = null)
    {
        return $this->canOverwrite($path);
    }

    /**
     * @return CoolTableFileRepository
     */
    public function getRepository() {
        return parent::getRepository();
    }

    /**
     * @inheritdoc
     */
    public function canSetProperties($path)
    {
        return $this->checkSetting($path, self::SETTING_CAN_SET_PROPERTIES);
    }
}