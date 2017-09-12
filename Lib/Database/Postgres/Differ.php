<?php

/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

namespace Eulogix\Cool\Lib\Database\Postgres;

use Eulogix\Cool\Lib\Cool;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @author Pietro Baricco <pietro@eulogix.com>
 */

class Differ {

    const TEMP_SCHEMA_NAME = 'public';

    /**
    * init params
    * 
    * @var string
    */
    protected $temp_db, $host, $port, $database, $user, $schema, $auditSchema, $apgDiffJar;

    /**
     * the user with wich the application will connect to the database, it is different than $user in that typically
     * it is not a super user
     * @var string
     */
    protected $appUser;

    /**
    * @var array
    */
    protected $sqlFiles=[];

    /**
    * @var InputInterface
    */
    protected $input;

    /**
     * @var boolean
     */
    protected $isMultiTenant;

    /**
     * schemas on which the current schema depends
     * @var string[]
     */
    protected $complementarySchemas = [];

    /**
    * 
    * @var OutputInterface
    */
    protected $output;

    /**
     * @var array
     */
    public $lastErrors=[];

    /**
     * @var bool
     */
    public $hasErrors=false;

    /**
     * @param string $host
     * @param string $port
     * @param string $database
     * @param string $user
     * @param string $appUser
     * @param string $schema
     * @param string $auditSchema
     * @param string $complementarySchemas
     * @param bool $isMultiTenant
     * @param string[] $sqlFiles
     * @param string[] $preSyncSqlFiles
     * @param string[] $postSyncSqlFiles
     * @param InputInterface $input
     * @param OutputInterface $output
     * @throws \Exception
     */
    public function __construct($host, $port, $database, $user, $appUser, $schema, $auditSchema, $complementarySchemas, $isMultiTenant, $sqlFiles, $preSyncSqlFiles, $postSyncSqlFiles, InputInterface $input, OutputInterface $output) {
        $this->host = $host;
        $this->port = $port;
        $this->database = $database;
        $this->user = $user;    
        $this->appUser = $appUser;
        $this->schema = $schema;
        $this->auditSchema = $auditSchema;
        $this->isMultiTenant = $isMultiTenant;
        if($complementarySchemas)
            $this->complementarySchemas = array_unique ( array_merge( $complementarySchemas, [$auditSchema]) );

        $this->setSqlFiles('pre_sync', $preSyncSqlFiles);
        $this->setSqlFiles('post_sync', $postSyncSqlFiles);
        $this->setSqlFiles('sync', $sqlFiles);

        $this->input = $input;    
        $this->output = $output;    
    }

    /**
     * @param string $type
     * @param string[] $sqlFiles
     * @throws \Exception
     */
    public function setSqlFiles($type, $sqlFiles) {
        $this->sqlFiles[$type] = [];
        if(is_array($sqlFiles)) {
            foreach($sqlFiles as $sql) {
                $this->sqlFiles[$type][] = str_replace('/',DIRECTORY_SEPARATOR,$sql);
            }
        } elseif($sqlFiles) {
            $this->sqlFiles[$type][] = str_replace('/',DIRECTORY_SEPARATOR,$sqlFiles);
        } elseif($type=='sync') throw new \Exception("missing sql files");
    }

    /**
     * @param string $schema
     * @param string $sqlFile
     */
    protected function setSearchPathAndSchema($schema, $sqlFile) {
        file_put_contents($sqlFile,
            "SET lc_messages TO 'en_US.UTF-8';
            SET SCHEMA '$schema';
            SET search_path = $schema, pg_catalog;

            GRANT USAGE ON SCHEMA $schema TO {$this->appUser};
            GRANT ALL ON ALL TABLES IN SCHEMA $schema TO {$this->appUser};
            GRANT ALL ON ALL SEQUENCES IN SCHEMA $schema TO {$this->appUser};

            ".
            file_get_contents($sqlFile)
        );
    }

    /**
     * @param string $schema
     * @param string $sqlFile
     */
    protected function setSchemaInSqlFile($schema, $sqlFile) {
        $this->setSearchPathAndSchema($schema, $sqlFile);
        file_put_contents($sqlFile, $this->ChangeSchema(file_get_contents($sqlFile),'public',$schema));
    }

    /**
     * @param string $sqlFile
     */
    protected function addCASCADES($sqlFile) {
        $content = file_get_contents($sqlFile);
        $content = preg_replace('/^DROP ([^ ]+) ([^;]+);/sim', 'DROP $1 IF EXISTS $2 CASCADE;', $content);
        $content = preg_replace('/^[ \t]*DROP CONSTRAINT ([^ ]+);/im', 'DROP CONSTRAINT IF EXISTS $1 CASCADE;', $content);
        file_put_contents($sqlFile, $content);
    }

    /**
     * @param string[] $sqlFiles
     * @param string $db
     * @param string $user
     * @param bool $changeSchema
     * @param bool $changeSearchPath
     * @return bool
     * @throws \Exception
     */
    protected function applySqlFiles($sqlFiles, $db, $user, $changeSchema=null, $changeSearchPath=null) {
        if(!$sqlFiles)
            return true; //not an error

        $tmpFolder = $this->createTempDir();
        $deleteTempDir = true;

        foreach($sqlFiles as $sql) {
            $pathInfo = pathinfo($sql);
            echo $pathInfo['basename']."....";
            $tmp = $tmpFolder.'/'.$pathInfo['basename'];
            copy($sql, $tmp);

            if($changeSchema) {
                $this->setSchemaInSqlFile($changeSchema, $tmp);
            } elseif($changeSearchPath) {
                $this->setSearchPathAndSchema($this->isMultiTenant ? self::TEMP_SCHEMA_NAME : $this->schema, $tmp);
            }

            $this->processAsTwigTemplate($tmp);

            if(!$this->pgExec("psql --host=\"{$this->host}\" --port=\"{$this->port}\" --dbname=\"$db\" --username=$user --file=\"$tmp\"",
                "applying sql scripts")) {
                echo "\n\n ***** \n\n $tmp left for debugging purposes. Delete it manually!\n\n ***** \n\n ";
                $deleteTempDir = false;
            } else @unlink($tmp);

            echo "OK\n";
        }

        if($deleteTempDir)
            @rmdir($tmpFolder);

        return true;
    }

    /**
     * @param string $diffContent
     * @return bool
     */
    public function applyDiff($diffContent) {
        //first of all, we must execute the pre_sync scripts so that any element not compatible with apgdiff is ironed out
        $this->applySqlFiles($this->sqlFiles['pre_sync'], $this->database, $this->user, $this->schema);

        //then we apply the diff
        $diff = tempnam(sys_get_temp_dir(),'SQL');
        file_put_contents($diff, $diffContent);

        $ret = $this->pgExec("psql --host=\"{$this->host}\" --port=\"{$this->port}\" --dbname=\"{$this->database}\" --username={$this->user} --file=\"$diff\"",
                         "applying diff");

        if(!$ret) {
            echo "\n\n ***** \n\n $diff left for debugging purposes. Delete it manually!\n\n ***** \n\n ";
        } else @unlink($diff);

        //finally, the post_sync scripts (applied to the right schema!)
        $ret = $this->applyPostScripts() && $ret;

        return $ret;
    }

    /**
     * @param string $PGOutput
     * @return array
     */
    private function parsePGOutput($PGOutput) {
        preg_match_all('/^.+?ERROR:[ \t]*(.+?)$/im',$PGOutput,$errM,PREG_SET_ORDER);
        preg_match_all('/^.+?NOT(A|E):[ \t]*(.+?)$/im',$PGOutput,$notesM,PREG_SET_ORDER);

        $errors = [];
        if($errM) {
            foreach($errM as $e) {
                $errors[]=$e[0]."\n\n\t";
            }
        }
        
        $notes = [];
        if($errM) {
            foreach($notesM as $e) {
                $notes[]=$e[1]."\n\n\t";
            }
        }
        
        return array(
            'rawOutput'=>$PGOutput,
            'notes'=>$notes,
            'errors'=>$errors,
            'status'=>$notes||$errors?false:true
        );   
    }

    /**
     * @param string $cmd
     * @param string $description
     * @return mixed
     */
    protected function pgExec($cmd, $description) {
        $ret = $this->shellExec($cmd, $description);
        $parsedOutput = $this->parsePGOutput($ret);
        $this->hasErrors = $this->hasErrors || !$parsedOutput['status'];
        if(!$parsedOutput['status']){
            // error
        }
        if($this->hasErrors) {
            $this->lastErrors = array_merge_recursive($this->lastErrors, $parsedOutput);
            $this->lastErrors['errors'] = array_unique($this->lastErrors['errors']);
            $this->lastErrors['notes'] = array_unique($this->lastErrors['notes']);
        }
        return $parsedOutput['status'];
    }

    /**
     * @param string $cmd
     * @param string  $description
     * @return string
     */
    protected function shellExec($cmd, $description) {
        $ret = shell_exec($cmd." 2>&1"); //redirect stderr to stdout
        return $ret;
    }

    /**
     * @param string[] $schemasToDump
     * @return string
     */
    protected function getPgDumpSchemasDefinition($schemasToDump) {
        $clauses = [];
        foreach($schemasToDump as $schema)
            $clauses[] = "--schema=\"$schema\"";
        return implode(' ',$clauses);
    }

    /**
     * @param string[] $schemasToCreate
     * @return string
     */
    protected function getCreateSchemaStatements($schemasToCreate) {
        $clauses = [];
        foreach($schemasToCreate as $schema) {
            $clauses[] = "CREATE SCHEMA IF NOT EXISTS $schema;";
        }
        return implode("\n",$clauses);
    }

    /**
     * dumps the stripped schema of a current (live) database
     *
     * @param $targetFile
     * @return bool
     */
    protected function dumpOldSchema($targetFile) {
        $database = $this->database;
        $user = $this->user;
        $schema = $this->schema;
        $temp_db = uniqid("tmpdb");
        $ret = true;

        $schemasToDump = $this->complementarySchemas;
        if(!in_array($schema, $schemasToDump))
            $schemasToDump[] = $schema;

        $this->shellExec("pg_dump --host=\"{$this->host}\" --port=\"{$this->port}\" -s ".$this->getPgDumpSchemasDefinition($schemasToDump)." --username=$user $database >\"$targetFile\"",
            "dump of the old schema, as is");

        // convert MT schema to public
        if($this->isMultiTenant) {
            file_put_contents($targetFile, $this->ChangeSchema(file_get_contents($targetFile), $schema, self::TEMP_SCHEMA_NAME));
        }

        $this->createTempDb($user, $temp_db);

        if(!$this->pgExec("psql --host=\"{$this->host}\" --port=\"{$this->port}\" --dbname=\"$temp_db\" --username=$user --file=\"$targetFile\"",
            "restore the dumped schema to the temp db, where it can be manipulated")) {
            $ret = false;
        } else {

            //pre sync scripts should contain statements that drop or alter any element not managed by APGDIFF so that
            //the subsequent diff creation does not fail
            if($ret = $this->applySqlFiles($this->sqlFiles['pre_sync'], $temp_db, $user)) {

                $schemaName = $this->isMultiTenant ? self::TEMP_SCHEMA_NAME : $this->schema;
                $this->shellExec("pg_dump --host=\"{$this->host}\" --port=\"{$this->port}\" -s --schema=\"$schemaName\" --username=$user $temp_db >\"$targetFile\"",
                    "final dump of the stripped schema to our target file");
            }
        }

        $this->shellExec("dropdb --host=\"{$this->host}\" --port=\"{$this->port}\" --username=$user $temp_db",
            "final drop of the temporary db $temp_db");

        return $ret;
    }

    /**
     * creates a dump from the defined propel SQL file and the additional SQL files
     * @param string $targetFile
     * @return bool
     */
    protected function dumpCurrentSchema($targetFile) {
        $user = $this->user;
        $temp_db = uniqid("tmpdb");

        $this->createTempDb($user, $temp_db);

        //create schemas
        $schemaName = $this->isMultiTenant ? self::TEMP_SCHEMA_NAME : $this->schema;
        $schemasToCreate = array_merge(
            ['public', $schemaName],
            $this->complementarySchemas
        );
        $temp_file = tempnam(sys_get_temp_dir(),'SQL');
        file_put_contents($temp_file, $this->getCreateSchemaStatements($schemasToCreate));
        array_unshift($this->sqlFiles['sync'], $temp_file);

        //base sync files
        $ret = $this->applySqlFiles($this->sqlFiles['sync'], $temp_db, $user);

        @unlink( $temp_file );

        $this->shellExec("pg_dump --host=\"{$this->host}\" --port=\"{$this->port}\" -s --schema=\"$schemaName\" --username=$user $temp_db >\"$targetFile\"",
            "dump of the updated schema");

        $this->shellExec("dropdb --host=\"{$this->host}\" --port=\"{$this->port}\" --username=$user $temp_db",
            "drop of the temporary db $temp_db");

        return $ret;
    }

    /**
     * @return bool|string
     */
    public function getDiffScript() {
        $old_dump = tempnam(sys_get_temp_dir(),'SQL');
        $new_dump = tempnam(sys_get_temp_dir(),'SQL');

        //grab the old and new dumps
        if($this->dumpOldSchema($old_dump) &&
            $this->dumpCurrentSchema($new_dump)) {

            //diff them
            $diff = tempnam(sys_get_temp_dir(),'SQL');

            $apgDiffCmd = "java -jar \"{$this->getApgDiffJar()}\"";

            $this->shellExec("$apgDiffCmd \"$old_dump\" \"$new_dump\" >\"$diff\"","diff");

            //nothing to do
            if(filesize($diff)==0) {
                $ret = false;
            } else {
                //modify the diff for being applied to the right schema (and not public)
                $schema = $this->schema;
                $this->setSchemaInSqlFile($schema, $diff);
                $this->addCASCADES($diff);
                $ret = file_get_contents($diff);

                //apgdiff failed for some reason!
                if(preg_match('/Exception in .+?java\..+?at .+?cz/sm', $ret)) {
                    $this->hasErrors = true;
                    $this->lastErrors['errors'][] = "APGDIFF FAILED: ".$ret;
                    return false;
                }
            }

            @unlink($diff);

        } else $ret = false;

        @unlink($old_dump);
        @unlink($new_dump);                 

        
        return $ret;
    }

    /**
     * when pg_dump -s creates the dump of a schema, the name of that schema occurs in several places throughout the generated dump file.
     * This function replaces all the occurrences of this old schema (source) with target.
     * This is useful to replace <yourschema> with <public> to later compare it to the master schema
     *
     * @param string $pgdumpBuffer
     * @param string $source
     * @param string $target
     * @return mixed
     */
    protected function ChangeSchema($pgdumpBuffer, $source, $target) {
        //replace whole word occurrences of source with dest
        $pgdumpBuffer = preg_replace('/\b'.$source.'\b/sim', $target, $pgdumpBuffer);
        $pgdumpBuffer = preg_replace('/CREATE SCHEMA ([^ ;]*);/sm', 'CREATE SCHEMA IF NOT EXISTS $1;', $pgdumpBuffer);
        return $pgdumpBuffer;
    }

    /**
     * @param string $user
     * @param string $temp_db
     * @return bool
     */
    protected function createTempDb($user, $temp_db)
    {
        $this->shellExec("dropdb --host=\"{$this->host}\" --port=\"{$this->port}\" --username=$user $temp_db",
            "drop of the temporary db $temp_db");

        $this->shellExec("createdb --host=\"{$this->host}\" --port=\"{$this->port}\" --username=$user --owner=$user \"$temp_db\"",
            "creation of the temporary db $temp_db");

        //extension init is needed here because pg_dump does not manage it
        //TODO: refactor this as a resource script
        $temp_file = tempnam(sys_get_temp_dir(),'SQL');
        file_put_contents($temp_file, "
CREATE EXTENSION IF NOT EXISTS plv8;
CREATE EXTENSION IF NOT EXISTS plcoffee;
CREATE EXTENSION IF NOT EXISTS plpgsql;
CREATE EXTENSION IF NOT EXISTS plls;
CREATE EXTENSION IF NOT EXISTS unaccent;
CREATE EXTENSION IF NOT EXISTS hstore SCHEMA pg_catalog;

--GRANT CONNECT ON DATABASE $temp_db TO {$this->appUser};
--GRANT ALL PRIVILEGES ON DATABASE $temp_db TO {$this->appUser};

--ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON TABLES TO {$this->appUser};
--ALTER DEFAULT PRIVILEGES IN SCHEMA public GRANT ALL ON SEQUENCES TO {$this->appUser};
        ");

        $ret = $this->applySqlFiles([$temp_file], $temp_db, $user);
        @unlink($temp_file);
        return $ret;
    }

    /**
     * @param string $tmp
     * @throws \Exception
     */
    private function processAsTwigTemplate($tmp)
    {
        $twig = Cool::getInstance()->getContainer()->get('twig');
        if($twig instanceof \Twig_Environment) {
            $loader = $twig->getLoader();

            $twigTemplate = $twig->createTemplate(file_get_contents( $tmp ));
            try {
                $buf = $twigTemplate->render( [
                    'currentDatabase' => $this->database,
                    'currentSchema' => $this->schema,
                    'auditSchema' => $this->auditSchema,
                    'appUser'      => $this->appUser,
                    'publicPlaceholder' => 'public' //hack to avoid subsequent variable substitution by ChangeSchema method
                ]);
                file_put_contents($tmp, $buf);
            } catch(\Exception $e) {
                throw $e;
                $processedLayout = "$l TWIG ERROR : {$e->getMessage()}";
            } finally {
                $twig->setLoader($loader);
            }
        }
    }

    /**
     * @return bool
     */
    public function applyPostScripts()
    {
        return $this->applySqlFiles($this->sqlFiles[ 'post_sync' ], $this->database, $this->user, $this->schema);
    }

    /**
     * @return string
     */
    function createTempDir() {
        $tempfile=tempnam(sys_get_temp_dir(),'');
        if (file_exists($tempfile)) { unlink($tempfile); }
        mkdir($tempfile);
        if (is_dir($tempfile)) { return $tempfile; }
    }

    /**
     * @return string
     */
    public function getApgDiffJar()
    {
        return $this->apgDiffJar;
    }

    /**
     * @param string $apgDiffJar
     * @return $this
     */
    public function setApgDiffJar($apgDiffJar)
    {
        $this->apgDiffJar = $apgDiffJar;
        return $this;
    }

}