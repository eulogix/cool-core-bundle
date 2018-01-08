/*
 * This file is part of the Eulogix\Cool package.
 *
 * (c) Eulogix <http://www.eulogix.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
*/

/**

PUBLIC API

-- retrieves the audit settings for a table
select yourschema.get_audit_settings_for_yourtable();

-- sets a table to audit fully with a 5 secs timeframe
SELECT set_audit_strategy('yourtable','full',5);

-- sets strategy to none without discarding anything
SELECT set_audit_strategy('yourtable','none',0);

-- discards audit logs and sets strategy to none
SELECT set_audit_strategy('yourtable','discard',0);

-- applies a strategy to all the tables of the schema
set_audit_strategy_bulk('full', 5);

-- creates the temp views  to a given date
SELECT enter_audit_mode('2015-01-01 00:00:00');

-- destroys the temp views
SELECT exit_audit_mode();

*/

-- create hstore in pg_catalog so that it is available for all the schemas in the db
CREATE EXTENSION IF NOT EXISTS hstore SCHEMA pg_catalog;

-- DROP SCHEMA IF EXISTS {{ auditSchema }} CASCADE;
CREATE SCHEMA IF NOT EXISTS {{ auditSchema }};

GRANT USAGE ON SCHEMA {{ auditSchema }} TO {{ appUser }};
ALTER DEFAULT PRIVILEGES IN SCHEMA {{ auditSchema }} GRANT ALL ON TABLES TO {{ appUser }};
ALTER DEFAULT PRIVILEGES IN SCHEMA {{ auditSchema }} GRANT ALL ON SEQUENCES TO {{ appUser }};
GRANT ALL ON ALL TABLES IN SCHEMA {{ auditSchema }} TO {{ appUser }};
GRANT ALL ON ALL SEQUENCES IN SCHEMA {{ auditSchema }} TO {{ appUser }};

COMMENT ON SCHEMA {{ auditSchema }} IS 'Out-of-table audit/history logging tables and trigger functions for schema {{ currentSchema }}';

CREATE TABLE IF NOT EXISTS {{ auditSchema }}.[[ appLogTableName ]] (
    event_id bigserial primary key,
    root_id INT8,
    father_id INT8,
    transaction_id INT8,
    leaf BOOLEAN,
    tstamp TIMESTAMP NOT NULL,
    category INT2,
    type INT2,
    object_type INT8,
    object_id INTEGER,
    dossier_id INTEGER,
    user_id INTEGER,
    event_data JSON
);

CREATE OR REPLACE FUNCTION {{ currentSchema }}.enter_audit_mode(instant TIMESTAMP) RETURNS VOID AS $$

    /* returns an array of strings containing the views that depend on a given list of relations (tables or other views) */
    function getDependantViews(relations) {
        var relstring = relations.map( function (element) { return "'" + element + "'" } ).join(',');
        var views = plv8.execute("SELECT DISTINCT view_name FROM information_schema.view_table_usage WHERE view_catalog = current_database() AND table_schema='{{ currentSchema }}' AND table_name IN("+relstring+") ORDER BY view_name ASC");
        return views.map( function (element) { return element.view_name } );
    }

    /* fetches the definition of a given view, and regenerates it as a temporary view */
    function regenView(view) {
        var viewDDL = plv8.execute("SELECT DEFINITION FROM pg_views WHERE viewname='"+view+"' AND schemaname='{{ currentSchema }}';").pop()['definition'];
        viewDDL = "CREATE OR REPLACE TEMPORARY VIEW "+view+" AS "+viewDDL.replace(/{{ currentSchema }}\./g,'');
        plv8.execute(viewDDL);
    }

    /* regenerates the views that depend on a given list of relations, including the relations themselves after the first iteration (which comes from the tables, for which the tempviews have already been generated) */
    function regenViews(relations, includeRelations) {
        var allViews = getDependantViews(relations);
        if(includeRelations) {
            var mergedArray = relations.concat(allViews);
            var deduplicatedArray = mergedArray.filter(function (item, pos) {return mergedArray.indexOf(item) == pos});
            allViews = deduplicatedArray;
        }
        var dependentViews = getDependantViews(allViews);

        var viewsToRegenerate = [];
        for(var i=0; i < allViews.length; i++) {
            if(dependentViews.indexOf(allViews[i]) === -1) {
                /* since this view does not depend on other views, it is a leaf and can be safely regenerated*/
                regenView(allViews[i]);
            } else viewsToRegenerate.push(allViews[i]);
        }

        if(viewsToRegenerate.length > 0)
            regenViews(viewsToRegenerate, true);
    }

    /* main loop that generates a tempview for every table in the schema */
	var tableNames = [];
    var s = "select table_name from INFORMATION_SCHEMA.TABLES where table_schema='{{ currentSchema }}' AND table_type='BASE TABLE'";
    var tables = plv8.execute( s );
    for(var i=0; i < tables.length; i++) {
        var plan = plv8.prepare( "SELECT {{ currentSchema }}.create_audit_view('"+tables[i].table_name+"', $1)", ['timestamp'] );
        plan.execute([instant]);
        plan.free();

        tableNames.push(tables[i].table_name);
    }

    /*
        This permanent relation override technique works fine, but has a problem:
        all existing views depending on the overridden relation will still point to the "real" table.
        To overcome this, we must
        - recursively determine which views depend on the tables, and which views depend on them
        - modify their DDL so that the "TEMPORARY" keyword is added
        - rerun all the modified DDLs in the proper order so that all views ultimately point to the audit table views
        Obviously this approach has a cost, so it would ideally work well with connection pooling, instancing a new connection for every combination of schema/instant
    */
    regenViews(tableNames, false);

$$ LANGUAGE plv8;

/**
Drops all the temporary views still in the session (if any, usually there are none)
 */
CREATE OR REPLACE FUNCTION {{ currentSchema }}.exit_audit_mode() RETURNS VOID AS $$

    var s = "select table_name from INFORMATION_SCHEMA.TABLES where table_schema='{{ currentSchema }}' AND table_type='BASE TABLE'";
    var tables = plv8.execute( s );
    for(var i=0; i < tables.length; i++) {
        plv8.execute("DO \$\$ BEGIN DROP VIEW IF EXISTS "+tables[i].table_name+" CASCADE; EXCEPTION WHEN OTHERS THEN END; \$\$;");
    }

$$ LANGUAGE plv8;

/**
calls the proper view generation method for the given table
 */
CREATE OR REPLACE FUNCTION {{ currentSchema }}.create_audit_view(tablename TEXT, instant TIMESTAMP) RETURNS VOID AS $$

	var settings = plv8.execute("SELECT {{ auditSchema }}.get_audit_settings_for_" + tablename + "() AS settings").pop().settings;

	switch(settings.audit_style) {
		case 'none':
			var plan = plv8.prepare('SELECT {{ currentSchema }}._create_audit_view_none($1)', ['text']);
			plan.execute([tablename]);
		break;
		case 'last':
		case 'full':
			var plan = plv8.prepare('SELECT {{ currentSchema }}._create_audit_view_full($1, $2)', ['text', 'timestamp']);
			plan.execute([tablename, instant]);
		break;
	}

    if(plan)
        plan.free();

$$ LANGUAGE plv8;

/**
Creates an audit view that overrides the tablename table
 */
CREATE OR REPLACE FUNCTION {{ currentSchema }}._create_audit_view_full(tablename TEXT, instant TIMESTAMP) RETURNS VOID AS $body$
DECLARE
BEGIN

    EXECUTE format('CREATE OR REPLACE TEMPORARY VIEW %2$I AS
		            SELECT * FROM %1$I.%2$I WHERE %3$L::timestamp <@ aud_validity_range AND aud_action != ''D'' '
                    ,'{{ auditSchema }}', tablename, instant);

END;
$body$
LANGUAGE plpgsql;

/**
creates a fake audit view which works with no audit table at all
 */
CREATE OR REPLACE FUNCTION {{ currentSchema }}._create_audit_view_none(tablename TEXT) RETURNS VOID AS $body$
DECLARE
BEGIN
    EXECUTE format('CREATE OR REPLACE TEMPORARY VIEW %1$I AS
		            SELECT
		            1::bigint AS aud_event_id,
                    tsrange(''[2014-09-01,)'') AS aud_validity_range,
                    0::integer AS aud_cool_user_id,
                    1::integer AS aud_version,
                    ''I''::CHAR(1) AS aud_action,
                    hstore(''foo=>bar'') AS aud_changed_fields,
                    123456::bigint AS aud_transaction_id,

		            * FROM {{ currentSchema }}.%1$I'

		            ,tablename);
END;
$body$
LANGUAGE plpgsql;

/**
    applies an audit strategy to the whole schema
*/
CREATE OR REPLACE FUNCTION {{ currentSchema }}.set_audit_strategy_bulk(audit_style text, timeframe_seconds integer) RETURNS void AS $$

    var s = "select table_name from INFORMATION_SCHEMA.TABLES where table_schema='{{ currentSchema }}' AND table_type='BASE TABLE'";
    var tables = plv8.execute( s );
    for(var i=0; i < tables.length; i++) {
        plv8.execute("SELECT {{ currentSchema }}.set_audit_strategy('"+tables[i].table_name+"', '"+audit_style+"', "+timeframe_seconds+")");
    }

$$ LANGUAGE plv8;


/**
sets audit strategy to a table
 */
CREATE OR REPLACE FUNCTION {{ currentSchema }}.set_audit_strategy(tablename text, audit_style text, timeframe_seconds integer) RETURNS void AS $body$
DECLARE
BEGIN
    EXECUTE 'DROP TRIGGER IF EXISTS audit_trigger_history ON {{ currentSchema }}.' || tableName;

    IF (audit_style = 'discard') THEN

        PERFORM {{ auditSchema }}._set_audit_strategy_settings(tablename, 'none', 0, TRUE);
        EXECUTE 'DROP TABLE IF EXISTS {{ auditSchema }}.' || tablename || ' CASCADE';
        EXECUTE 'DROP TRIGGER IF EXISTS audit_trigger_history_row ON {{ currentSchema }}.' || tablename || ' CASCADE;';
        EXECUTE 'DROP TRIGGER IF EXISTS audit_trigger_history_stm ON {{ currentSchema }}.' || tablename || ' CASCADE;';

    ELSIF (audit_style = 'none') THEN

        PERFORM {{ auditSchema }}._set_audit_strategy_settings(tablename, audit_style, timeframe_seconds, TRUE);
        EXECUTE 'DROP TRIGGER IF EXISTS audit_trigger_history_row ON {{ currentSchema }}.' || tablename || ' CASCADE;';
        EXECUTE 'DROP TRIGGER IF EXISTS audit_trigger_history_stm ON {{ currentSchema }}.' || tablename || ' CASCADE;';

    ELSIF (audit_style = 'full') THEN

        PERFORM {{ auditSchema }}._set_audit_strategy_settings(tablename, audit_style, timeframe_seconds, TRUE);

        -- build or replace trigger function
        PERFORM core.create_audit_trigger_function(tablename,'{{ currentSchema }}','{{ auditSchema }}');

        -- build or update the history table
        PERFORM core.update_audit_table(tablename, '{{ currentSchema }}', tablename, '{{ auditSchema }}');

        EXECUTE format('CREATE TRIGGER audit_trigger_history_row AFTER INSERT OR UPDATE OR DELETE
                    ON {{ currentSchema }}.%3$I
                    FOR EACH ROW EXECUTE PROCEDURE {{ auditSchema }}.' || tablename || '_audit_trigger_func_row(%1$L, %2$L);'
                    ,audit_style, timeframe_seconds, tablename);

        EXECUTE format('CREATE TRIGGER audit_trigger_history_stm AFTER TRUNCATE
                    ON {{ currentSchema }}.%1$I
                    FOR EACH STATEMENT EXECUTE PROCEDURE {{ auditSchema }}.' || tablename || '_audit_trigger_func_stm();'
                    , tablename);
    END IF;

END;
$body$
language 'plpgsql';


CREATE OR REPLACE FUNCTION {{ auditSchema }}._set_audit_strategy_settings(tablename text, audit_style text, timeframe_seconds integer, override boolean) RETURNS void AS $body$
DECLARE
BEGIN
    BEGIN
        -- this is a default function that is always overridden by the set_audit_strategy function
        EXECUTE format('CREATE ' || (CASE WHEN override THEN 'OR REPLACE' ELSE '' END)
                        || ' FUNCTION {{ auditSchema }}.get_audit_settings_for_' || tablename
                        || '(OUT audit_style text, OUT timeframe_seconds integer)' ||
                        'AS $$
                        BEGIN
                           audit_style := %1$L;
                           timeframe_seconds := %2$L;
                        END;
                        $$  LANGUAGE plpgsql;',

                        audit_style, timeframe_seconds);

	EXCEPTION
	    -- the exception occurs whenever the function is already defined, which is to be expected
        WHEN OTHERS THEN
    END;

END;
$body$
language 'plpgsql';



-- returns info about the last time a field has been modified

CREATE OR REPLACE FUNCTION {{ currentSchema }}.get_column_last_modification_info(table_name TEXT, column_name TEXT, record_pk INT
	,OUT user_id INT
	,OUT date TIMESTAMP
	,OUT previous_value TEXT
) AS $$
DECLARE
  source_schema text;
  audit_schema text;
  pkFieldName text;
  tempData record;
BEGIN

source_schema := '{{ currentSchema }}';
audit_schema := '{{ auditSchema }}';

EXECUTE format('SELECT core.get_table_pkfields(''%1$s'',''%2$s'')', source_schema, table_name) INTO pkFieldName;

EXECUTE format( 'SELECT' ||
		' 	lower(mr.aud_validity_range) as aud_date,' ||
		'	pr.%1$I::TEXT as previous_value,' ||
		' 	mr.%1$I::TEXT as current_value,' ||
		' 	mr.aud_cool_user_id' ||
		' FROM %2$I.%3$I mr ' ||
		' LEFT JOIN %2$I.%3$I pr ON pr.%4$I = mr.%4$I AND pr.aud_version = mr.aud_version-1' ||
		' WHERE mr.%4$I = %5$s'  ||
		' AND exist(mr.aud_changed_fields, ''%1$s'')' ||
		' ORDER BY mr.aud_version DESC LIMIT 1',
		column_name, audit_schema, table_name, pkFieldName, record_pk) INTO tempData;

user_id 	:= tempData.aud_cool_user_id;
date 		:= tempData.aud_date;
previous_value 	:= tempData.previous_value;

END;
$$ LANGUAGE plpgsql;