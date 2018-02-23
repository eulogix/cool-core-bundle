<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Builders;

use Eulogix\Cool\Lib\Builders\Lookups\LookupsBuilder;
use Eulogix\Cool\Lib\Builders\sql\AuditSchemaBuilder;
use Eulogix\Cool\Lib\Builders\sql\FileRepositoriesSchemaBuilder;
use Eulogix\Cool\Lib\Builders\sql\FixSequencesBuilder;
use Eulogix\Cool\Lib\Builders\sql\FTSBuilder;
use Eulogix\Cool\Lib\Builders\sql\SqlSnippetsBuilder;
use Eulogix\Cool\Lib\Builders\sql\ViewsBuilder;
use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Dictionary\Dictionary;
use Eulogix\Cool\Lib\Symfony\Bundle\BundleUtils;
use Eulogix\Lib\XML\XSDParser;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class DictionaryBuilder {

    /**
     * @var string
     */
    var $schemaName, $projectDir, $locatablePjDir;

    /**
     * @var BundleInterface
     */
    var $bundle;

    /**
     * @param BundleInterface $bundle
     * @param $schemaName
     * @param $projectDir
     */
    function __construct(BundleInterface $bundle, $schemaName, $projectDir) {
        $this->bundle = $bundle;
        $this->schemaName = $schemaName;
        $this->projectDir = $projectDir;
        $this->locatablePjDir = BundleUtils::toLocatableBundlePath($projectDir, $this->bundle);
    }

    protected function getTemplate($templateName, $type='php.twig') {
        return "EulogixCoolCoreBundle:DictionaryBuilderClasses:$templateName.$type";
    }

    /**
     * Builds the dictionary classes
     *
     * @param EngineInterface $tplEngine
     * @throws \Exception
     */
    function build(EngineInterface $tplEngine) {

        if($isSchemaAttachedToAnother = Cool::getInstance()->getAttachedToSchemaName($this->schemaName)) {
            throw new \Exception("schema $this->schemaName is attached to schema $isSchemaAttachedToAnother, and can not be built alone.");
        }

        $settings = $this->retrieveSettings();
        $namespace = @$settings['namespace'];
        $package = @$settings['package'];

        if($package) {
            $target_folder = realpath(sprintf('%s/../%s', Cool::getInstance()->getContainer()->getParameter('kernel.root_dir'), str_replace('.', DIRECTORY_SEPARATOR, $package) ));
        } elseif(preg_match('/.+?(Model\b.*?)$/im', $namespace, $m)) {
            $modelTargetRelativeToBundle = $m[1];
            $target_folder = $this->bundle->getPath().DIRECTORY_SEPARATOR.str_replace("\\", DIRECTORY_SEPARATOR, $modelTargetRelativeToBundle);
        }

        $dict = $settings['settings'];

        @mkdir($target_folder);


        //write the base dictionary with the static settings
        file_put_contents( $target_folder."/BaseDictionary.php", $tplEngine->renderResponse(
            $this->getTemplate('BaseDictionary'),
            array(
                'namespace'             =>  $namespace,
                'className'             =>  'BaseDictionary',
                'locatableProjectDir'   =>  $this->locatablePjDir,
                'databaseName'          =>  $this->schemaName,
                'settingsArray'         =>  var_export($dict,1)
            )
        )->getContent());
        
        //write the dictionary that extends the base one        
        $tg = $target_folder."/Dictionary.php";
        if(true || !file_exists($tg))
            file_put_contents($tg , $tplEngine->renderResponse(
                $this->getTemplate('Dictionary'),
                array(
                    'namespace'         =>  $namespace,
                    'className'         =>  'Dictionary',
                )
            )->getContent());        

        //write the schema class
        $tg = $target_folder."/Schema.php";
        if(!file_exists($tg))
            file_put_contents($tg , $tplEngine->renderResponse(
                $this->getTemplate('Schema'),
                array(
                    'nameSpace'         =>  $namespace,
                    'className'         =>  'Schema',
                )
            )->getContent());


        //write the custom sql generated set of triggers and data validators
        $tg = $this->projectDir."/sql/000_auto_cool_lookups_OTLT.sql";
        file_put_contents($tg , $tplEngine->renderResponse(
            $this->getTemplate('sql/lookupTriggers', 'sql.php'),
            array(
                'databaseName'      =>  $this->schemaName,
            )
        )->getContent());


        $tg = $this->projectDir."/sql/001_auto_cool_constraints.sql";
        file_put_contents($tg , $tplEngine->renderResponse(
            $this->getTemplate('sql/constraintsTriggers', 'sql.php'),
            array(
                'databaseName'      =>  $this->schemaName,
            )
        )->getContent());

        //triggers defined in the schema
        $tg = $this->projectDir."/sql/002_auto_cool_schema_triggers.sql";
        file_put_contents($tg , $tplEngine->renderResponse(
            $this->getTemplate('sql/schemaTriggers', 'sql.php'),
            array(
                'databaseName'      =>  $this->schemaName,
            )
        )->getContent());

        //views
        $viewsBuilder = new ViewsBuilder($this->schemaName);
        $viewsBuilder->output($this->projectDir."/sql/post_sync/000_auto_cool_views.sql");
        $viewsBuilder->outputDropScript($this->projectDir."/sql/pre_sync/000_auto_cool_views.sql");

        //lookups
        $lookupsBuilder = new LookupsBuilder($this->schemaName);
        $lookupsBuilder->outputTableScript($this->projectDir."/sql/post_sync/003_auto_cool_lookups.sql");
        $lookupsBuilder->outputEnumScript($this->projectDir."/sql/post_sync/004_auto_cool_lookups_enum.sql");
        $lookupsBuilder->outputLookupFunctions($this->projectDir."/sql/post_sync/004_auto_cool_lookup_functions.sql");
        //the fixture sql has to be executed last as it always triggers errors of duplicate keys, executing it earlier
        //would stop execution of custom scripts
        $lookupsBuilder->outputFixtures($this->projectDir, $this->projectDir."/sql/post_sync/999_auto_cool_lookups_fixtures.sql");

        //snippets
        $snipBuilder = new SqlSnippetsBuilder($this->schemaName);
        $snipBuilder->output($this->projectDir."/sql/003_auto_cool_custom_snippets.sql");

        //file repos
        $repoBuilder = new FileRepositoriesSchemaBuilder($this->schemaName);
        $repoBuilder->outputScript($repoBuilder->getScript(), $this->projectDir."/sql/004_auto_cool_filerepos.sql");

        //auditing
        $auditBuilder = new AuditSchemaBuilder($this->schemaName);
        $auditBuilder->outputScript($auditBuilder->getScript(), $this->projectDir."/sql/post_sync/004_auto_cool_audit.sql");

        //fix sequences
        $fsB = new FixSequencesBuilder($this->schemaName);
        $fsB->outputScript($fsB->getScript(), $this->projectDir."/sql/post_sync/005_auto_fix_sequences.sql");

        //FTS
        $fts = new FTSBuilder($this->schemaName);
        $fts->outputScript($fts->getPreScript(), $this->projectDir."/sql/pre_sync/006_auto_fts.sql");
        $fts->outputScript($fts->getPostScript(), $this->projectDir."/sql/post_sync/006_auto_fts.sql");

    }

    /**
     * @param \SimpleXMLElement $xmlElement
     * @param string $xpath
     */
    protected function removeXmlElementNodeByXpath($xmlElement, $xpath) {
        foreach ($xmlElement->xpath($xpath) as $key => $node) {
            $oNode = dom_import_simplexml($node);
            if(@$oNode) {
                $oNode->parentNode->removeChild($oNode);
            }
        }
    }

    /**
     * @param \SimpleXMLElement $xmlElement
     * @param string $attributeName
     * @return bool|null
     */
    protected function extractAttributeFromSimpleXmlElement(&$xmlElement, $attributeName) {
        $v = null;
        if($tv = $xmlElement->attributes()[$attributeName]) {
            /**
             * @var \SimpleXMLElement $tv
             */
            $v = $tv->__toString();
            switch(strtolower($v)) {
                case 'true':  $v = true; break;
                case 'false': $v = false; break;
            }
            unset($xmlElement->attributes()[$attributeName]);
        }
        return $v;
    }

    /**
     * removes any remaining attribute that starts with _
     * @param \SimpleXMLElement $xmlElement
     */
    protected function discardTempElements(&$xmlElement) {
        $discardList = [];
        foreach($xmlElement->attributes() as $attributeName => $attributeValue) {
            if(preg_match('/^_.+?$/sim', $attributeName))
                $discardList[] = $attributeName;
        }
        foreach($discardList as $attributeNameToDiscard) {
            unset($xmlElement->attributes()[$attributeNameToDiscard]);
        }
    }

    /**
     * @param \SimpleXMLElement $xmlElement
     * @param string $attributeName
     * @return string|null
     */
    protected function getAttributeFrom($xmlElement,$attributeName) {
        $v = null;
        if($tv = $xmlElement->attributes()[$attributeName]) {
            /**
             * @var \SimpleXMLElement $tv
             */
            return $tv->__toString();
        }
        return null;
    }

    /**
     * @param \SimpleXMLElement $xmlElement
     * @param array $allowedElements
     * @param array $allCoolElements
     * @return array
     */
    protected function extractCoolElements(&$xmlElement, $allowedElements, $allCoolElements=null) {
        $arr = ['attributes'=>[], 'choices'=>[]];
        //attributes
        if (isset($allowedElements['attributes'])) {
            foreach($allowedElements['attributes'] as $attributeName => $attributeAttributes) {
                $attrV = $this->extractAttributeFromSimpleXmlElement($xmlElement, $attributeAttributes['name']);
                if($attrV!==null) {
                    $arr['attributes'][$attributeName] = $attrV;
                }
            }
        }
        //choices
        if (isset($allowedElements['choices'])) {
            foreach($allowedElements['choices'] as $choiceName => $choiceSchemaAttributes) {

                $choiceSchemaName = $choiceSchemaAttributes['name'];
                $hasContent = true;
                $choiceAttributes = @$choiceSchemaAttributes['_type']['simpleContent']['extension']['attribute'];
                if(!$choiceAttributes) {
                    $hasContent = false;
                    $choiceAttributes = @$choiceSchemaAttributes['_type']['attribute'];
                }

                foreach($xmlElement->$choiceSchemaName as $choice) {
                    /**
                     * @var \SimpleXMLElement $choice
                     */
                    $choiceArr = [];

                    if($choiceAttributes)
                        foreach($choiceAttributes as $attribute) {
                            if( $attributeName = @$attribute['@attributes']['name'] ) {
                                $attrV = $this->extractAttributeFromSimpleXmlElement($choice, $attributeName);
                                if($attrV!==null) {
                                    $choiceArr[$attributeName] = $attrV;
                                }
                            }
                        }

                    if($hasContent) {
                        $choiceArr['body'] = $choice->__toString();
                    }

                    //recursively merge cool complex types that refer other complex types (see _files)
                    if(@$allCoolElements[$choiceSchemaName]) {
                        $inArr = $this->extractCoolElements($xmlElement->$choiceSchemaName, $allCoolElements[$choiceSchemaName], $allCoolElements);
                        if(count($inArr['choices'])>0) {
                           $choiceArr = array_merge($choiceArr, $inArr['choices'], ['attributes'=>$inArr['attributes']]);
                        }
                    }

                    if($choiceSchemaAttributes['maxOccurs']==1)
                         $arr['choices'][$choiceName] = $choiceArr;
                    else $arr['choices'][$choiceName][] = $choiceArr;
                }

                $this->removeXmlElementNodeByXpath($xmlElement, $choiceSchemaName);
            }
        }
        return $arr;
    }

    public function extractSettingsAndSaveCleanPropelSchema() {

        if($isSchemaAttachedToAnother = Cool::getInstance()->getAttachedToSchemaName($this->schemaName)) {
            throw new \Exception("schema $this->schemaName is attached to schema $isSchemaAttachedToAnother, and can not be built alone.");
        }

        $multipleSettings = [];

        $this->parseAttachedSchemas($multipleSettings);

        $targetCleanFolder = $this->bundle->getPath().'/Resources/config/';

        $settings = [];
        $xml = $this->extractSettingsFromXMLSchema($settings);

        foreach($multipleSettings as $settingsEntry)
            $this->mergeXmlElementsBy($settingsEntry['cleanXml'], $xml);

        $xml->asXML($targetCleanFolder.DIRECTORY_SEPARATOR.$this->schemaName.'_schema.xml');

        $multipleSettings[] = ['settings' => $settings];

        $settings = $this->mergeSettings($multipleSettings);

        file_put_contents($this->projectDir.'/_cool_settings.tmp.json', json_encode($settings, JSON_PRETTY_PRINT));
    }


    /**
     * merges the source schema in target
     * @param \SimpleXMLElement $source
     * @param \SimpleXMLElement $target
     * @param string $attributeName
     */
    protected function mergeXmlElementsBy($source, &$target, $attributeName = 'name') {

        foreach($source->children() as $sourceChild) {
            /**
             * @var \SimpleXMLElement $sourceChild
             */
            $childExistsInTarget = false;
            $sourceChildTagName = $sourceChild->getName();
            foreach($target->children() as $targetChild) {
                /**
                 * @var \SimpleXMLElement $targetChild
                 */
                $targetChildTagName = $targetChild->getName();

                $sourceAttributeValue = $this->getAttributeFrom($sourceChild, $attributeName);
                $targetAttributeValue = $this->getAttributeFrom($targetChild, $attributeName);

                //element exist in both xmls, so have to be merged
                if($targetAttributeValue !== null && $targetChildTagName == $sourceChildTagName && $sourceAttributeValue == $targetAttributeValue ) {
                    $childExistsInTarget = true;
                    $this->mergeXmlAttributes($sourceChild, $targetChild);
                    $this->mergeXmlElementsBy($sourceChild, $targetChild, $attributeName);
                }

            }

            if(!$childExistsInTarget)
                $this->appendXml($target, $sourceChild);
        }
    }

    /**
     * @param \SimpleXMLElement $source
     * @param \SimpleXMLElement $target
     */
    protected function mergeXmlAttributes($source, &$target) {
        foreach($source->attributes() as $name => $value) {
            if(!isset($target->attributes()[$name])) {
                $target->attributes()->addAttribute($name, $value);
            }
        }
    }

    /**
     * @param \SimpleXMLElement $to
     * @param \SimpleXMLElement $from
     */
    function appendXml(\SimpleXMLElement $to, \SimpleXMLElement $from) {
        $toDom = dom_import_simplexml($to);
        $fromDom = dom_import_simplexml($from);
        $toDom->appendChild($toDom->ownerDocument->importNode($fromDom, true));
    }

    /**
     * @param array $settings
     * @return \SimpleXMLElement
     */
    public function extractSettingsFromXMLSchema(&$settings) {

        $coolXMLSchema = $this->projectDir.'/schema.xml';

        $xsd = new XSDParser();
        $coolElements = $xsd->extractCoolElements( Cool::getInstance()->getFactory()->getFileLocator()->locate("@EulogixCoolCoreBundle/Resources/xsd/cool_database.xsd"));

        $xmlContent = file_get_contents($coolXMLSchema);
        //strip comments
        $xmlContent = preg_replace('/<!--.+?-->/sim','',$xmlContent);

        $xml = simplexml_load_string($xmlContent);

        $dbPackage = $this->getAttributeFrom($xml, 'package');
        $dbNameSpace = $this->getAttributeFrom($xml, 'namespace');
        $dbSchema = $this->getAttributeFrom($xml, 'schema');

        $tableSettings = $viewsSettings = [];

        foreach($xml->table as $tbl) {

            $tablePhpName = $this->getAttributeFrom($tbl, 'phpName');

            $tableSchema = $this->getAttributeFrom($tbl, 'schema');
            $tableSchema = $tableSchema ? $tableSchema : $dbSchema;

            $rawTableName = $tbl->attributes()['name']->__toString();
            $tableName = ($tableSchema ? $tableSchema.'.' : '').$rawTableName;

            $tableNamespace = $this->getAttributeFrom($tbl, 'namespace');

            $ce = $this->extractCoolElements($tbl, $coolElements['table'], $coolElements);
            $this->discardTempElements($tbl);

            $tableSettings[$tableName]['attributes'] = array_merge(
                [
                    Dictionary::TBL_ATT_SCHEMA  => $tableSchema,
                    Dictionary::TBL_ATT_RAWNAME  => $rawTableName
                ],
                $ce['attributes']
            );

            if($tableNamespace)
                $tableSettings[$tableName]['attributes'][Dictionary::TBL_ATT_NAMESPACE] = $tableNamespace;

            if($tablePhpName)
                $tableSettings[$tableName]['attributes'][Dictionary::TBL_ATT_PHP_NAME] = $tablePhpName;

            $tableSettings[$tableName] = array_merge_recursive($tableSettings[$tableName], $ce['choices']);

            foreach($tbl->column as $col) {
                $columnName = $col->attributes()['name']->__toString();
                $ce = $this->extractCoolElements($col, $coolElements['column'], $coolElements);
                $this->discardTempElements($col);
                $tableSettings[$tableName]['columns'][$columnName]['attributes'] = $ce['attributes'];
                $tableSettings[$tableName]['columns'][$columnName] = array_merge_recursive($tableSettings[$tableName]['columns'][$columnName], $ce['choices']);
            }

        }

        foreach($xml->_view as $view) {

            $viewName = $view->attributes()['_name']->__toString();

            $ce = $this->extractCoolElements($view, $coolElements['_view'], true);
            $viewsSettings[$viewName]['attributes'] = $ce['attributes'];
            $viewsSettings[$viewName] = array_merge_recursive($viewsSettings[$viewName], $ce['choices']);

        }
        $this->removeXmlElementNodeByXpath($xml, "_view");

        $settings = [
            'settings'         =>  ['tables' => $tableSettings, 'views' => $viewsSettings],
            'database_name'    =>  $xml->attributes()['name']->__toString(),
            'namespace'        =>  $dbNameSpace,
            'package'        =>  $dbPackage
        ];

        return $xml;
    }

    /**
     * @return mixed
     */
    public function retrieveSettings() {
        return json_decode(file_get_contents($this->projectDir.'/_cool_settings.tmp.json'), true);
    }

    /**
     * @param array $multipleSettings
     */
    protected function parseAttachedSchemas(array &$multipleSettings) {
        /**
         * scan all the bundles, finding all the cool schemas defined therein
         * for each of them, check if they are attached to the schema currently processed
         * if yes, merge the settings with priority <attached schemas>, <current schema>
         * so that current schema can override or extend the entities defined in the attached schemas
         */
        $attachedSchemas = Cool::getInstance()->getSchemaNamesAttachedTo($this->schemaName);
        if(!empty($attachedSchemas)) {
            $bundles = Cool::getInstance()->getContainer()->getParameter('kernel.bundles');
            foreach($bundles as $bundleName => $bundleClass) {
                if ($bundle = BundleUtils::getBundle($bundleName)) {
                    if ($dirs = BundleUtils::getCoolProjectDirs($bundle)) {
                        foreach ($dirs as $projectDir) {
                            /**
                             * @var \SplFileInfo $projectDir
                             */
                            $schemaName = $projectDir->getFilename();
                            if (in_array($schemaName, $attachedSchemas)) {
                                $builder = new DictionaryBuilder($bundle, $schemaName, $projectDir->getRealPath());
                                $settings = [];
                                $xml = $builder->extractSettingsFromXMLSchema($settings);
                                $multipleSettings[] = ['cleanXml' => $xml, 'settings' => $settings];
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * @param array $multipleSettings
     * @return array
     */
    protected function mergeSettings(array $multipleSettings)
    {
        $settings = [];
        foreach($multipleSettings as $arr)
            $settings = array_merge_recursive_distinct($settings, $arr['settings']);
        return $settings;
    }


}