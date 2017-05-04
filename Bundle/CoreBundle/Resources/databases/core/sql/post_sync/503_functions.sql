/*
CREATE TYPE core.view_field_ddl_source AS (view_column TEXT, ddl_field TEXT);

CREATE OR REPLACE FUNCTION core.get_view_fields_ddl(schema_name TEXT, view_name TEXT) RETURNS SETOF view_field_ddl_source AS $$
	var plan = plv8.prepare("select definition from pg_matviews where schemaname=$1 and matviewname=$2 UNION select definition from pg_views where schemaname=$1 and viewname=$2", ['text','text']);
	var viewDefinitionText = plan.execute([schema_name, view_name]).pop()['definition'];

	var ret = [];

	viewDefinitionText.replace( /SELECT *(DISTINCT|) *(ON *\(.+?\)|) *([\s\S]+?)FROM/im, function(whole, g1, g2, selectPortion){
		var selectColumns = selectPortion.split(',');
		for(var i=0; i<selectColumns.length; i++) {
			selectColumns[i].replace(/(.+?\.| *)([a-z0-9_-]+?)[ \t]+as[ \t]+(.+?)$/im, function(whole2, gg1, sourceColumnName, viewColumnName){
				ret.push({
					"view_column" 	:	 viewColumnName,
					"ddl_field" 	:	 sourceColumnName
				});
			});
		}
	});

	return ret;

$$ LANGUAGE plv8;


 */

/*
* calculates the most likely origin of a view field to a physical table field
SELECT * FROM core.get_view_fields_origin('schemaname', 'viewname')
*/
CREATE TYPE core.view_field_origin AS (view_column TEXT, source_schema TEXT, source_table TEXT, source_column TEXT, data_type TEXT);

CREATE OR REPLACE FUNCTION core.get_view_fields_origin(schema_name TEXT, view_name TEXT) RETURNS SETOF view_field_origin AS $$
	var retObj = {};

	var plan = plv8.prepare("SELECT column_name, data_type FROM information_schema.columns cols WHERE cols.table_schema=$1 and table_name=$2", ['text','text']);
	var allViewCols = plan.execute([schema_name, view_name]);
	plan.free();

	function populateRetFromView(qSchema, qView) {

		var plan = plv8.prepare([   "SELECT cols.column_name AS view_column_name, cols.data_type, usage.* ",
					                "FROM information_schema.columns cols",
					                "LEFT JOIN core.view_column_usage usage ON usage.view_schema = $3 AND usage.view_name=$4 AND usage.column_name = cols.column_name ",
                                    "WHERE	cols.table_schema = $1",
                                    "AND	cols.table_name = $2"].join(" ")
		                        , ['text','text','text','text']);

		var qret =  plan.execute([schema_name, view_name, qSchema, qView]);
		plan.free();

        for(var i=0; i < qret.length; i++) {
            if(!qret[i].table_is_view && qret[i].column_name && (!retObj[qret[i].view_column_name]))
                retObj[qret[i].view_column_name] = {
                    "view_column":qret[i].view_column_name,
                    "source_schema":qret[i].table_schema,
                    "source_table":qret[i].table_name,
                    "source_column":qret[i].column_name,
										"data_type":qret[i].data_type
                };
        }

		var otherViewsToTry = {};
        for(var i=0; i < qret.length; i++) {
            if(qret[i].table_is_view && !retObj[qret[i].view_column_name])
                otherViewsToTry[ qret[i].table_name ] = qret[i].table_schema;
        }

	    for(var ov in otherViewsToTry)
		    populateRetFromView(otherViewsToTry[ov],ov);
	}

	populateRetFromView(schema_name, view_name);

    var ret = [];

		for(var i=0; i < allViewCols.length; i++) {
				var viewColName = allViewCols[i].column_name;
				if(retObj[viewColName])
						ret.push(retObj[viewColName]);
				else ret.push({
                    "view_column":viewColName,
                    "source_schema":null,
                    "source_table":null,
                    "source_column":null,
										"data_type": allViewCols[i].data_type
                });
		}

    return ret;

$$ LANGUAGE plv8;