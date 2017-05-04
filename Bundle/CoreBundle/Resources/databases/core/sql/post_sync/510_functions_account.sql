set schema 'core';

DROP FUNCTION IF EXISTS core.account_get_setting(account_id INT, setting_name TEXT, VARIADIC args TEXT[]);

CREATE FUNCTION core.account_get_setting(account_id INT, setting_name TEXT, VARIADIC args TEXT[] DEFAULT NULL) RETURNS TEXT AS $$

	args = args || [];
	for(var i=1; i<args.length+1; i++)
		setting_name = setting_name.replace( '{' + i + '}', args[i-1] );

	var plan = plv8.prepare("SELECT value FROM core.account_setting_prioritized WHERE account_id=$1 AND name=$2 AND valid=TRUE", ['int','text']);
	var qret =  plan.execute([account_id, setting_name]);
	plan.free();

	if(qret.length == 1)
		return qret[0].value;

	return null;

$$ LANGUAGE plv8;

