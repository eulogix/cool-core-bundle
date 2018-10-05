set schema 'core';


CREATE OR REPLACE FUNCTION core.update_calculated_fields_from_view(schema_name text, table_name text, record_pk integer) RETURNS void AS $$

    /**
    generic function to update calculated fields in a table from a view with the same name:
    update_calculated_fields(schema_name text, table_name text, record_pk integer)
    **/

	var viewName = table_name+'_calc';
	var fqnView = schema_name+"."+viewName;
	var fqnTable = schema_name+"."+table_name;

	var viewExists = plv8.execute("select * from INFORMATION_SCHEMA.TABLES where table_name = '"+viewName+"' AND table_schema='"+schema_name+"'").length == 1;
	if(!viewExists) {
	    plv8.elog(ERROR, 'calc view does not exist:', viewName);
	    return;
	}

	/* find the int pk of the table (which has to exists in the view, too)
	 there's no error checking here */
	var pkField = plv8.execute("SELECT core.get_table_pkfields('"+schema_name+"', '"+table_name+"') as pk;").pop().pk;

	/* fetch all the view columns (except the pk) */
	var viewColumnsQuery = "select column_name, data_type from INFORMATION_SCHEMA.COLUMNS where table_name = '"+viewName+"' AND table_schema='"+schema_name+"' AND column_name!='"+pkField+"'";
	var viewColumns = plv8.execute( viewColumnsQuery );
	/*plv8.elog(NOTICE, 'view columns:', JSON.stringify(viewColumns));*/

	/* fetch the record from view and table */
	var viewRecord 	= plv8.execute( "SELECT * FROM "+fqnView+" WHERE "+pkField+"="+record_pk ).pop();
	var tableRecord = plv8.execute( "SELECT * FROM "+fqnTable+" WHERE "+pkField+"="+record_pk ).pop();

	/* this may happen during a cascading delete: the child record has a trigger to update the parent record, which has already been deleted.*/
	if(!tableRecord)
	    return;

	/*plv8.elog(NOTICE, 'view record:', JSON.stringify(viewRecord));
	plv8.elog(NOTICE, 'table record:', JSON.stringify(tableRecord));*/

	/* determine the delta (changed fields) which has to be applied to table */
	var paramTypes = [];
	var setStatements = [];
	var params = [];
	var paramCounter = 1;
	for(var i=0; i < viewColumns.length; i++) {
	    var field = viewColumns[i].column_name;
	    if(tableRecord.hasOwnProperty(field) && (!viewRecord || viewRecord[field] != tableRecord[field])) {
            setStatements.push(field+' = $'+paramCounter++);
            paramTypes.push( viewColumns[i].data_type );
            params.push(viewRecord ? viewRecord[field] : null); /* no corresponding record in the view means that we have to update the table field with a null */
	    }
	}
	/* for the pk */
	paramTypes.push('integer');
	params.push(record_pk);

	/* apply the delta with an UPDATE statement */
	if(setStatements.length > 0) {

	    var q = "UPDATE "+fqnTable+" a SET " + setStatements.join(',') + " WHERE "+pkField+"=$"+paramCounter++;
	    var plan = plv8.prepare( q, paramTypes );
	    plan.execute(params);
	    plan.free();
	    /*plv8.elog(NOTICE, 'query executed', q);
	    plv8.elog(NOTICE, 'with params', JSON.stringify(params));*/
	}/* else plv8.elog(NOTICE, 'no fields have changed')*/;
$$
LANGUAGE plv8;

CREATE OR REPLACE FUNCTION core.update_calculated_fields_from_view(table_name text, record_pk integer) RETURNS void AS $$
SELECT core.update_calculated_fields_from_view(current_schema(), table_name::text, record_pk);
$$ LANGUAGE SQL;