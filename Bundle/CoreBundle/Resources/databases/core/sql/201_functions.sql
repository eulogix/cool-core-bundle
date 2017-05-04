set schema 'core';

/**
formats a timestamp to ISO 8601 format
 */
create or replace function iso_timestamp(timestamp with time zone)
   returns varchar as $$
  select substring(xmlelement(name x, $1)::varchar from 4 for 25)
$$ language sql immutable;

/**
gets a timeframe from a date
 */
create or replace function timeframe(timestamp, integer)
   returns bigint as $$
  select trunc(extract(epoch from $1) / $2)::bigint
$$ language sql immutable;

/**
returns the id of the currently logged user.
The following variables are being set by the CoolUser class upon instantiation

Cool::getInstance()->getCoreSchema()->query("set session \"cool.user.id\" = '" .$Account->getAccountId()."';");
Cool::getInstance()->getCoreSchema()->query("set session \"cool.user.loginName\" = '" .$Account->getLoginName()."';");
Cool::getInstance()->getCoreSchema()->query("set session \"cool.user.firstName\" = '" .$Account->getFirstName()."';");
Cool::getInstance()->getCoreSchema()->query("set session \"cool.user.lastName\" = '" .$Account->getLastName()."';");

 */
CREATE OR REPLACE FUNCTION get_logged_user() RETURNS INTEGER AS $$
   DECLARE
	return_value integer;
   BEGIN
	BEGIN
		return_value = current_setting('cool.user.id')::integer;
	EXCEPTION WHEN OTHERS THEN
		return_value = 0;
	END;
	RETURN return_value;
   END;
$$ LANGUAGE plpgsql;

-----------------------------------------------
----------------  AUDIT STUFF -----------------
-----------------------------------------------

/**
creates a trigger function that updates an audit table
 */
DROP FUNCTION IF EXISTS core.create_audit_trigger_function(text, text, text);
CREATE or REPLACE FUNCTION
    core.create_audit_trigger_function(source_table text, source_schema text, audit_schema text) RETURNS VOID AS $$

var sourceColumnNames = [];

var s = "select * from INFORMATION_SCHEMA.COLUMNS where table_name = '"+source_table+"' AND table_schema='"+source_schema+"'";
var sourceTableColumns = plv8.execute( s );
for(var i=0; i < sourceTableColumns.length; i++) {
    var sourceColumn = sourceTableColumns[i];
    sourceColumnNames.push(sourceColumn.column_name);
}

var pkFieldNames = [];
// used to compare record PK to the OLD pk, in UPDATE operations
var pkComparisonClauses = [];
// used to compare record PK to the NEW pk, in INSERT operations (when a new record is inserted with the same PK of a previously deleted record still in DELETE state)
var newPkComparisonClauses = [];
var tablePkFields = plv8.execute("select core.get_table_pkfields('"+source_schema+"','"+source_table+"') as pk");
for(var i=0; i < tablePkFields.length; i++) {
    pkFieldNames.push(tablePkFields[i].pk);
    pkComparisonClauses.push(tablePkFields[i].pk +' = OLD.'+tablePkFields[i].pk)
    newPkComparisonClauses.push(tablePkFields[i].pk +' = NEW.'+tablePkFields[i].pk)
}

var auditTableQualifier = audit_schema+"."+source_table;

var statements = [

    "CREATE OR REPLACE FUNCTION "+auditTableQualifier+"_audit_trigger_func_row() RETURNS TRIGGER AS $body$",
    "DECLARE",
    "   audit_style text;",
    "	timeframe_seconds integer;",
    "	current_clock_timestamp timestamp;",
    "	last_saved_timestamp timestamp;",
    "	current_timeframe integer;",
    "	new_version integer;",
    "	must_create_new_entry boolean;",
    "   changed_fields hstore;",
    "BEGIN",
    "    audit_style         := TG_ARGV[0];",
    "    timeframe_seconds   := TG_ARGV[1];",
    "    current_clock_timestamp := clock_timestamp();",
    "    current_timeframe := core.timeframe(current_clock_timestamp,  timeframe_seconds);",
    "    must_create_new_entry := FALSE;",

    "    IF (TG_OP = 'INSERT') THEN",
    "        SELECT lower(aud_validity_range), aud_version+1",
    "                INTO last_saved_timestamp, new_version",
    "                FROM "+ auditTableQualifier +"",
    "                WHERE upper_inf(aud_validity_range) AND "+ newPkComparisonClauses.join(' AND ') +";",
    "    ELSE ",
    "        SELECT lower(aud_validity_range), aud_version+1",
    "                INTO last_saved_timestamp, new_version",
    "                FROM "+ auditTableQualifier +"",
    "                WHERE upper_inf(aud_validity_range) AND "+ pkComparisonClauses.join(' AND ') +";",
    "    END IF; ",
    "    IF new_version IS NULL THEN new_version := 1; END IF; -- we are updating a record that has no history, or we are inserting one",

    "    IF (TG_OP = 'UPDATE') THEN",
    "        changed_fields := (hstore(NEW.*) - hstore(OLD.*));",
    "        must_create_new_entry := (last_saved_timestamp IS NULL) OR (current_timeframe != core.timeframe(last_saved_timestamp,  timeframe_seconds) );",
    "    END IF;",
    "	IF ((TG_OP = 'UPDATE' AND must_create_new_entry) OR TG_OP = 'DELETE') THEN",
    "			UPDATE "+ auditTableQualifier +" SET aud_validity_range = aud_validity_range * tsrange('[,' || current_clock_timestamp || ']')",
    "		    WHERE upper_inf(aud_validity_range) AND "+ pkComparisonClauses.join(' AND ') +";",
    "	ELSIF (TG_OP = 'INSERT') THEN",
    "			UPDATE "+ auditTableQualifier +" SET aud_validity_range = aud_validity_range * tsrange('[,' || current_clock_timestamp || ']')",
    "		    WHERE upper_inf(aud_validity_range) AND "+ newPkComparisonClauses.join(' AND ') +";",
    "	END IF;",
    "	IF (TG_OP = 'DELETE') THEN",
    "		  INSERT INTO "+ auditTableQualifier +" (",
    "		    aud_event_id,",
    "            aud_validity_range,",
    "            aud_cool_user_id,",
    "            aud_version,",
    "            aud_action,",
    "            aud_changed_fields,",
    "            aud_transaction_id,",
    '		    '+pkFieldNames.join(",\n		    ")+")",
    "		  VALUES (",
    "		    nextval('"+ auditTableQualifier +"_aud_event_id_seq'),",
    "		    tsrange('(' || current_clock_timestamp || ',)'),",
    "		    core.get_logged_user(),",
    "           -- 2147483647, -- this used to be max int, now changed to new_version as a record with the same pk may be deleted and then inserted again",
    "		    new_version,",
    "		    substring(TG_OP,1,1),",
    "		    changed_fields,",
    "		    txid_current(),",
    '		    OLD.'+pkFieldNames.join(",\n		    OLD.")+");",
    "		RETURN OLD;",
    "	END IF;",
    "	IF ((TG_OP = 'UPDATE' AND must_create_new_entry) OR TG_OP = 'INSERT') THEN",
    "		  INSERT INTO "+ auditTableQualifier +" (",
    "		    aud_event_id,",
    "            aud_validity_range,",
    "            aud_cool_user_id,",
    "            aud_version,",
    "            aud_action,",
    "            aud_changed_fields,",
    "            aud_transaction_id,",
    "            "+sourceColumnNames.join(",\n            ")+")",
    "		  VALUES (",
    "		    nextval('"+ auditTableQualifier +"_aud_event_id_seq'),",
    "		    tsrange('(' || current_clock_timestamp || ',)'),",
    "		    core.get_logged_user(),",
    "		    new_version,",
    "		    substring(TG_OP,1,1),",
    "		    changed_fields,",
    "		    txid_current(),",
    '		    NEW.'+sourceColumnNames.join(",\n		    NEW.")+");",
    "		RETURN NEW;",
    "	END IF;",
    "	IF (TG_OP = 'UPDATE' AND NOT must_create_new_entry) THEN",
    "		  UPDATE "+ auditTableQualifier +"",
    "		    SET (",
    "            aud_cool_user_id,",
    "            aud_changed_fields,",
    "            aud_transaction_id,",
    "            aud_version,",
    "            "+sourceColumnNames.join(",\n            ")+")",
    "                =",
    "            (core.get_logged_user(),",
    "		    aud_changed_fields || changed_fields,",
    "		    txid_current(),",
    "		    new_version,",
    '		    NEW.'+sourceColumnNames.join(",\n		    NEW.")+")",
    "		  WHERE upper_inf(aud_validity_range) AND "+ pkComparisonClauses.join(' AND ') +";",
    "		RETURN NEW;",
    "	END IF;",
    "	RETURN OLD;",
    "END;",
    "$body$",
    "LANGUAGE plpgsql;"
];

plv8.execute(statements.join("\n"));

var statements = [

    "CREATE OR REPLACE FUNCTION "+auditTableQualifier+"_audit_trigger_func_stm() RETURNS TRIGGER AS $body$",
    "BEGIN",
    "	TRUNCATE TABLE "+ auditTableQualifier +";",
    "	RETURN NULL;",
    "END;",
    "$body$",
    "LANGUAGE plpgsql;"
];

plv8.execute(statements.join("\n"));

$$ LANGUAGE plv8;


/**
updates an audit table by adding all the missing fields of the source table, the validity timestamp range
no indexes, fkeys or constraints are added
TODO: consider the case of TYPE change of a field
 */
DROP FUNCTION IF EXISTS core.update_audit_table(text, text, text, text);
CREATE or REPLACE FUNCTION
    core.update_audit_table(source_table text, source_schema text, target_table text, audit_schema text) RETURNS VOID AS $$

	var audit_table = audit_schema+"."+target_table;

	var targetTableExists = plv8.execute("select * from INFORMATION_SCHEMA.TABLES where table_name = '"+target_table+"' AND table_schema='"+audit_schema+"'").length == 1;
	if(!targetTableExists) {
		plv8.execute("CREATE TABLE " + audit_table + [

		"( aud_event_id                 bigserial primary key,",
		"  aud_validity_range           tsrange NOT NULL,",
		"  aud_cool_user_id             integer,",
		"  aud_version                  integer NOT NULL DEFAULT 0,",
		"  aud_action                   CHAR(1) NOT NULL CHECK (aud_action IN ('I', 'D', 'U')),",
		"  aud_changed_fields           hstore,",
		"  aud_transaction_id           bigint );"

		].join("\n"));

	}

	/* compare table structure and create missing columns in target */
	var s = "select * from INFORMATION_SCHEMA.COLUMNS where table_name = '"+source_table+"' AND table_schema='"+source_schema+"' ORDER BY ordinal_position ASC";
	var sourceTableColumns = plv8.execute( s );
	for(var i=0; i < sourceTableColumns.length; i++) {
		var sourceColumn = sourceTableColumns[i];
		var targetColumnExists = plv8.execute("SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE table_name = '"+target_table+"' AND table_schema='"+audit_schema+"' AND column_name='"+sourceColumn.column_name+"'").length == 1;
		if(!targetColumnExists) {
		    var createType = '';
		    switch(sourceColumn.udt_name) {
                case '_text' : createType = 'TEXT[]'; break; //_text means array of text
                default : createType = sourceColumn.data_type;
		    }
		    var createStatement = "ALTER TABLE "+audit_table+" ADD COLUMN "+sourceColumn.column_name+" "+createType;
		    if(sourceColumn.data_type == 'numeric') {
		        createStatement += "("+sourceColumn.numeric_precision+","+sourceColumn.numeric_scale+")";
		    }
			plv8.execute(createStatement);
		}
	}

    /* bulk insert data and default index creation*/
    if(!targetTableExists) {
		plv8.execute([

            "INSERT INTO "+audit_table+" SELECT ",
		    " nextval('"+audit_schema+"."+target_table+"_aud_event_id_seq'), ",
		    " tsrange('[2014-09-01,)'), ",
		    " 0, ",
		    " 1, ",
		    " 'I', ",
		    " NULL, ",
		    " txid_current(), ",
		    " * FROM "+source_schema+"."+source_table+";",

		    "CREATE INDEX " + target_table + "aud_validity_idx ON " + audit_table + " USING gist (aud_validity_range);",
            "CREATE INDEX " + target_table + "aud_tx_idx ON " + audit_table + " (aud_transaction_id);",
            "CREATE INDEX " + target_table + "aud_cuser_idx ON " + audit_table + " (aud_cool_user_id);",
            "CREATE INDEX " + target_table + "aud_action_idx ON " + audit_table + " (aud_action);"

		].join("\n"));
    }

	/* compare indexes and create missing ones in target */
	var sourceIndexes = plv8.execute("select core.get_table_indexes('"+source_schema+"','"+source_table+"') as tbl_index");

	for(var i=0; i < sourceIndexes.length; i++) {
		var sourceIndex = sourceIndexes[i].tbl_index;
		var targetIndexAlreadyExists = plv8.execute("select core.index_exists('"+target_table+"', '"+audit_schema+"', '"+sourceIndex.column_names+"') as ret").pop().ret;
		if(!targetIndexAlreadyExists) {
		    var newIndexName, j=0, indexExists;
		    //an existing index may be there with the same name, so we find a free name before creating it
		    do {
		        newIndexName = "inherited_" + sourceIndex.index_name + ( j++ > 0 ? "_" + j : "");
		        indexExists = plv8.execute("SELECT core.relation_exists('" + newIndexName + "','" + audit_schema + "') as ret").pop().ret;
		    } while(indexExists);
			plv8.execute("CREATE INDEX " + newIndexName + " ON "+audit_table+"("+sourceIndex.column_names+");");
		}
	}

  $$ LANGUAGE plv8;


  /**
  checks whether an index exists or not on a table for a given combination of fields.
  NOTE: the order of the fields is important
   */
  CREATE or REPLACE FUNCTION
  core.index_exists(table_name text, schema_name text, column_names text) RETURNS BOOLEAN AS $$
    var indexes = plv8.execute("select core.get_table_indexes('"+schema_name+"','"+table_name+"') as tbl_index");
    var column_names_a = JSON.stringify(column_names.split(',').sort());
    for(var i=0; i < indexes.length; i++) {
      var index = JSON.stringify(indexes[i].tbl_index.column_names.split(',').sort());
      if(index == column_names_a)
        return true;
    }
    return false;
  $$ LANGUAGE plv8;

/**
checks whether a relation name exists or not in a givn schema
 */
CREATE OR REPLACE FUNCTION core.relation_exists(relation_name text, schema_name text) RETURNS BOOLEAN AS $$

    SELECT EXISTS (
       SELECT 1
       FROM   pg_catalog.pg_class c
       JOIN   pg_catalog.pg_namespace n ON n.oid = c.relnamespace
       WHERE  n.nspname = schema_name
       AND    c.relname = relation_name
    );

$$ LANGUAGE SQL IMMUTABLE;

  /**
  returns the defined indexes for a given table/schma
   */
  CREATE OR REPLACE FUNCTION core.get_table_indexes(schema_name varchar, table_name varchar,
    OUT index_name text,
    OUT column_names text)
    RETURNS SETOF record
   AS
    $$
        select
        i.relname::text as index_name,

        array_to_string(array_agg(a.attname), ',') as column_names
    from
        pg_class t,
        pg_class i,
        pg_index ix,
        pg_attribute a,
        pg_namespace n
    where
        t.oid = ix.indrelid
        and i.oid = ix.indexrelid
        and a.attrelid = t.oid
        and a.attnum = ANY(ix.indkey)
        and t.relkind = 'r'
        and t.relname = $2
        AND n.nspname = $1
        AND n.oid = t.relnamespace
    group by
      N.nspname,
        t.relname,
        i.relname
    order by
        t.relname,
        i.relname;

    $$
  LANGUAGE 'sql' VOLATILE;

    /**
  returns the defined primary keys
   */
  CREATE OR REPLACE FUNCTION core.get_table_pkfields(schema_name varchar, table_name varchar,
    OUT column_name text)
    RETURNS SETOF text
   AS
    $$

	SELECT
	attname::text as column_name
	FROM pg_index, pg_class, pg_attribute, pg_namespace n
	WHERE


	  indrelid = pg_class.oid AND
	  pg_attribute.attrelid = pg_class.oid AND
	  pg_attribute.attnum = any(pg_index.indkey)
	  AND n.nspname = $1
	  AND relname = $2
	  AND indisprimary
          AND n.oid = relnamespace;
    $$
  LANGUAGE 'sql' VOLATILE;


CREATE OR REPLACE FUNCTION core.get_deduplicated_fields(relations_spec json)
  RETURNS text AS
$BODY$
	var selectTokens = [];
	var alreadySelectedFields = [];

	for(var i=0; i < relations_spec.length; i++) {
		var tableName = relations_spec[i].name;
		var schemaName = relations_spec[i].schema;
		var alias = relations_spec[i].alias ? relations_spec[i].alias : tableName;
		var duplicatePrefix = relations_spec[i].duplicate_prefix ? relations_spec[i].duplicate_prefix : tableName+'_';

		var s = "select * from INFORMATION_SCHEMA.COLUMNS where table_name = '"+tableName+"' AND table_schema='"+schemaName+"' ORDER BY ordinal_position ASC";
		var relationColumns = plv8.execute( s );
		for(var c=0; c < relationColumns.length; c++) {
			var columnName = relationColumns[c].column_name;

			//prefix already selected fields with the duplicate prefix, or the table name if not specified
			var viewColumnName = alreadySelectedFields.indexOf( columnName ) == -1 ? columnName : duplicatePrefix + columnName;
			alreadySelectedFields.push( columnName );

			selectTokens.push(alias + "." + columnName + " AS " + viewColumnName);
		}
	}

	return selectTokens.join(',\n');
$BODY$
LANGUAGE plv8 VOLATILE;

/**
returns the DDL for a deduplicated view
 */
CREATE OR REPLACE FUNCTION core.get_deduplicated_view_ddl(view_name text, relations_spec text, query_text text, select_portion text DEFAULT 'SELECT' )
RETURNS text AS
$BODY$
	var viewDDL = 'CREATE OR REPLACE VIEW '+ view_name + ' AS ' + select_portion + ' \n';
	viewDDL = viewDDL + plv8.execute("select core.get_deduplicated_fields('"+relations_spec+"'::json) as ret").pop().ret;
	viewDDL = viewDDL + "\n" + query_text;

	return viewDDL;
$BODY$
LANGUAGE plv8 VOLATILE;

/**
actually creates the deduplicated view
 */
CREATE OR REPLACE FUNCTION core.create_deduplicated_view(view_name text, relations_spec text, query_text text, select_portion text DEFAULT 'SELECT') RETURNS BOOLEAN AS
$BODY$
	var viewDDL = plv8.execute("select core.get_deduplicated_view_ddl($1, $2, $3, $4) as ret", [view_name, relations_spec, query_text, select_portion]).pop().ret;
	plv8.execute(viewDDL);
	return true;
$BODY$
LANGUAGE plv8 VOLATILE;