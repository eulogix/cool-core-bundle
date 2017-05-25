set schema 'core';

/**
 decodes a value by lookup name
 */
CREATE or REPLACE FUNCTION core.decode_lookup(domain_name text, locale text, value text, schema_filter text, filter text) RETURNS text AS $$

    var sql = "SELECT COALESCE(dec_"+locale+", '*' || dec_en) AS label FROM lookups."+domain_name+" WHERE value = $1";
    sql += " AND ($2 IS NULL OR filter IS NULL OR ($2 = ANY(filter)))";
    sql += " AND ($3 IS NULL OR schema_filter IS NULL OR ($3 = ANY(schema_filter)))";
    sql += " LIMIT 1";

    var plan = plv8.prepare(sql, ['text','text','text']);
    var qret =  plan.execute([value, filter, schema_filter]);

    if(qret.length == 1)
        return qret[0].label;

    return null;

$$ LANGUAGE plv8;


/**
 decodes a value by schema/table/column
 */
CREATE or REPLACE FUNCTION core.decode_table_column(schema_name text, table_name text, column_name text, locale text, value text, schema_filter text, filter text) RETURNS text AS $$

	var plan = plv8.prepare("SELECT core.get_lookup_domain($1, $2, $3) as domain", ['text', 'text', 'text']);
	var qret = plan.execute([schema_name, table_name, column_name]);
	var domain = qret[0].domain;

	if(domain) {
		plan = plv8.prepare("SELECT core.decode_lookup($1, $2, $3, $4, $5) as decoded_value");
		qret = plan.execute([domain, locale, value, schema_filter, filter]);
		if(qret.length == 1)
			return qret[0].decoded_value;
	}

	return value;

$$ LANGUAGE plv8;