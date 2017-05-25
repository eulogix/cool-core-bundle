SET lc_messages TO 'en_US.UTF-8';

CREATE or REPLACE FUNCTION {{ currentSchema }}.decode_table_column(table_name text, column_name text, locale text, value text, filter text) RETURNS text AS $$
	SELECT core.decode_table_column('core', table_name, column_name, locale, value, '{{ currentSchema }}', filter);
$$ LANGUAGE SQL IMMUTABLE;
