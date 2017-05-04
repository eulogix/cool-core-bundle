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

use Eulogix\Cool\Lib\Cool;
use Eulogix\Cool\Lib\Builders\Lookups\LookupsBuilder;
use Eulogix\Cool\Lib\Builders\sql\AuditSchemaBuilder;
use Eulogix\Cool\Lib\Builders\sql\FileRepositoriesSchemaBuilder;
use Eulogix\Cool\Lib\Builders\sql\FixSequencesBuilder;
use Eulogix\Cool\Lib\Builders\sql\FTSBuilder;
use Eulogix\Cool\Lib\Builders\sql\SqlSnippetsBuilder;
use Eulogix\Cool\Lib\Dictionary\Dictionary;
use Eulogix\Cool\Lib\Builders\sql\ViewsBuilder;
use Eulogix\Lib\XML\XSDParser;

use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\Config\FileLocatorInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class DictionaryBuilder {

    /**
     * @var string
     */
    var $projectDir, $locatablePjDir;

    /**
     * put your comment there...
     *
     * @param $projectDir
     * @return DictionaryBuilder
     */
    function __construct($projectDir, $locatablePjDir) {
        $this->projectDir = $projectDir;
        $this->locatablePjDir = $locatablePjDir;
    }
    
    protected function getTemplate($templateName, $type='php.twig') {
        return "EulogixCoolCoreBundle:DictionaryBuilderClasses:$templateName.$type";
    }

    /**
     * Converts a database schema name to php object name by Camelization.
     * Removes <code>STD_SEPARATOR_CHAR</code>, capitilizes first letter
     * of name and each letter after the <code>STD_SEPERATOR</code>,
     * converts the rest of the letters to lowercase.
     *
     * This method should be named camelizeMethod() for clarity
     *
     * my_CLASS_name -> MyClassName
     *
     * @param string $schemaName name to be converted.
     *
     * @return string Converted name.
     */
    protected function underscoreMethod($schemaName)
    {
        $name = "";
        $tok = strtok($schemaName, "_");
        while ($tok !== false) {
            $name .= ucfirst(strtolower($tok));
            $tok = strtok("_");
        }

        return $name;
    }
    
    /**
     * Builds the dictionary classes
     *
     * @param $databaseName
     * @param mixed $settings
     * @param mixed $target_folder
     * @param EngineInterface $tplEngine
     */
    function build($databaseName, $settings, $target_folder, EngineInterface $tplEngine) {

        $namespace = $settings['namespace'];

        $dict = $settings['settings'];

        @mkdir($target_folder);
                                                                                        
        //write the base dictionary with the static settings
        file_put_contents( $target_folder."/BaseDictionary.php", $tplEngine->renderResponse(
            $this->getTemplate('BaseDictionary'),
            array(
                'nameSpace'             =>  $namespace,
                'className'             =>  'BaseDictionary',
                'locatableProjectDir'   =>  $this->locatablePjDir,
                'databaseName'          =>  $databaseName,
                'settingsArray'         =>  var_export($dict,1)
            )
        )->getContent());
        
        //write the dictionary that extends the base one        
        $tg = $target_folder."/Dictionary.php";
        if(true || !file_exists($tg))
            file_put_contents($tg , $tplEngine->renderResponse(
                $this->getTemplate('Dictionary'),
                array(
                    'nameSpace'         =>  $namespace,
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
                'databaseName'      =>  $databaseName,
            )
        )->getContent()); 
        
        
        $tg = $this->projectDir."/sql/001_auto_cool_constraints.sql";
        file_put_contents($tg , $tplEngine->renderResponse(
            $this->getTemplate('sql/constraintsTriggers', 'sql.php'),
            array(
                'databaseName'      =>  $databaseName,
            )
        )->getContent());
        
        //triggers defined in the schema
        $tg = $this->projectDir."/sql/002_auto_cool_schema_triggers.sql";
        file_put_contents($tg , $tplEngine->renderResponse(
            $this->getTemplate('sql/schemaTriggers', 'sql.php'),
            array(
                'databaseName'      =>  $databaseName,
            )
        )->getContent());

        //views
        $viewsBuilder = new ViewsBuilder($databaseName);
        $viewsBuilder->output($this->projectDir."/sql/post_sync/000_auto_cool_views.sql");
        $viewsBuilder->outputDropScript($this->projectDir."/sql/pre_sync/000_auto_cool_views.sql");

        //lookups
        $lookupsBuilder = new LookupsBuilder($databaseName);
        $lookupsBuilder->outputTableScript($this->projectDir."/sql/post_sync/003_auto_cool_lookups.sql");
        $lookupsBuilder->outputEnumScript($this->projectDir."/sql/post_sync/004_auto_cool_lookups_enum.sql");
        //the fixture sql has to be executed last as it always triggers errors of duplicate keys, executing it earlier
        //would stop execution of custom scripts
        $lookupsBuilder->outputFixtures($this->projectDir, $this->projectDir."/sql/post_sync/999_auto_cool_lookups_fixtures.sql");

        //snippets
        $snipBuilder = new SqlSnippetsBuilder($databaseName);
        $snipBuilder->output($this->projectDir."/sql/003_auto_cool_custom_snippets.sql");

        //file repos
        $repoBuilder = new FileRepositoriesSchemaBuilder($databaseName);
        $repoBuilder->outputScript($repoBuilder->getScript(), $this->projectDir."/sql/004_auto_cool_filerepos.sql");

        //auditing
        $auditBuilder = new AuditSchemaBuilder($databaseName);
        $auditBuilder->outputScript($auditBuilder->getScript(), $this->projectDir."/sql/post_sync/004_auto_cool_audit.sql");

        //fix sequences
        $fsB = new FixSequencesBuilder($databaseName);
        $fsB->outputScript($fsB->getScript(), $this->projectDir."/sql/post_sync/005_auto_fix_sequences.sql");
        
        //FTS
        $fts = new FTSBuilder($databaseName);
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
    function extractCoolElements(&$xmlElement, $allowedElements, $allCoolElements=null) {
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

    /**
     * @param string $targetCleanSchema
     */
    function extractSettingsFromSchema($targetCleanSchema) {

        $xmlSchemaFilename = $this->projectDir.'/schema.xml';

        $xsd = new XSDParser();
        $coolElements = $xsd->extractCoolElements( Cool::getInstance()->getFactory()->getFileLocator()->locate("@EulogixCoolCoreBundle/Resources/xsd/cool_database.xsd"));

        $settings = array();

        $xmlContent = file_get_contents($xmlSchemaFilename);
        //strip comments
        $xmlContent = preg_replace('/<!--.+?-->/sim','',$xmlContent);

        $xml = simplexml_load_string($xmlContent);

        $dbNameSpace = $this->getAttributeFrom($xml, 'namespace');
        $dbSchema = $this->getAttributeFrom($xml, 'schema');

        foreach($xml->table as $tbl) {

            $tableSchema = $this->getAttributeFrom($tbl, 'schema');
            $tableSchema = $tableSchema ? $tableSchema : $dbSchema;

            $rawTableName = $tbl->attributes()['name']->__toString();
            $tableName = ($tableSchema ? $tableSchema.'.' : '').$rawTableName;

            $tableNamespace = $this->getAttributeFrom($tbl, 'namespace');
            $tableNamespace = $tableNamespace ? $tableNamespace : $dbNameSpace;

            $tablePhpName = $this->getAttributeFrom($tbl, 'phpName');
            $tablePhpName = $tablePhpName ? $tablePhpName : $this->underscoreMethod($rawTableName);

            $ce = $this->extractCoolElements($tbl, $coolElements['table'], $coolElements);
            $this->discardTempElements($tbl);

            $settings[$tableName]['attributes'] = array_merge(
                [
                    Dictionary::TBL_ATT_PROPEL_MODEL_NAMESPACE  =>  $tableNamespace.'\\'.$tablePhpName,
                    Dictionary::TBL_ATT_PROPEL_PEER_NAMESPACE   =>  $tableNamespace.'\\'.$tablePhpName.'Peer',
                    Dictionary::TBL_ATT_PROPEL_QUERY_NAMESPACE  =>  $tableNamespace.'\\'.$tablePhpName.'Query',
                    Dictionary::TBL_ATT_SCHEMA  => $tableSchema,
                    Dictionary::TBL_ATT_RAWNAME  => $rawTableName,

                ],
                $ce['attributes']
            );

            $settings[$tableName] = array_merge_recursive($settings[$tableName], $ce['choices']);

            foreach($tbl->column as $col) {
                $columnName = $col->attributes()['name']->__toString();
                $ce = $this->extractCoolElements($col, $coolElements['column'], $coolElements);
                $this->discardTempElements($col);
                $settings[$tableName]['columns'][$columnName]['attributes'] = $ce['attributes'];
                $settings[$tableName]['columns'][$columnName] = array_merge_recursive($settings[$tableName]['columns'][$columnName], $ce['choices']);
            }

        }

        $viewsSettings =[];
        foreach($xml->_view as $view) {

            $viewName = $view->attributes()['_name']->__toString();

            $ce = $this->extractCoolElements($view, $coolElements['_view'], true);
            $viewsSettings[$viewName]['attributes'] = $ce['attributes'];
            $viewsSettings[$viewName] = array_merge_recursive($viewsSettings[$viewName], $ce['choices']);

        }
        $this->removeXmlElementNodeByXpath($xml, "_view");

        $settings = array('settings'         =>  ['tables'=>$settings,'views'=>$viewsSettings],
            'database_name'    =>  $xml->attributes()['name']->__toString(),
            'namespace'        =>  $dbNameSpace);

        file_put_contents($this->projectDir.'/_cool_settings.tmp.json', json_encode($settings, JSON_PRETTY_PRINT));

        $xml->asXML($targetCleanSchema);
    }

    /**
     * @return mixed
     */
    public function retrieveSettings() {
        return json_decode(file_get_contents($this->projectDir.'/_cool_settings.tmp.json'), true);
    }
    
}