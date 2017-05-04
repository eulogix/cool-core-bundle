DROP VIEW IF EXISTS core.view_column_usage CASCADE;

CREATE VIEW core.view_column_usage AS
select
	current_database() AS view_catalog,
	n.nspname AS view_schema,
        v.relname       as "view_name",
        current_database() AS table_catalog,
        tn.nspname AS table_schema,
        t.relname       as "table_name",
        at.attname      as "column_name",
        t.relkind IN ('v','m') as "table_is_view"

    from pg_depend dv, pg_class v, pg_namespace nv,
         pg_depend dt, pg_class t, pg_namespace nt, pg_attribute at,
         pg_namespace n, pg_namespace tn
    where     dv.objid = dt.objid
          and dv.refobjid <> dt.refobjid
          and dv.deptype = 'i'

          and v.relkind IN('v','m') -- view or matview
          and t.relkind IN ('r', 'v', 'm') -- view or table

          and v.oid = dv.refobjid
          and t.oid = dt.refobjid

          and t.relnamespace = nt.oid
          and v.relnamespace = nv.oid
          and dv.classid    = dt.classid    and dv.classid    =
'pg_catalog.pg_rewrite'::regclass
          and dv.refclassid = dt.refclassid and dv.refclassid =
'pg_catalog.pg_class'::regclass

          and t.oid = at.attrelid and dt.refobjsubid = at.attnum


		and n.oid = v.relnamespace
		and tn.oid = t.relnamespace

		order by table_name ASC, column_name ASC;





-- flat view of all the account settings, for all users.
-- does not remove duplicates

DROP VIEW IF EXISTS core.account_setting_flat CASCADE;
CREATE VIEW core.account_setting_flat AS

SELECT *
FROM (

	select  profr.account_id, prof.name as profile_name, prof.account_profile_id, COALESCE(profr.sort_order, profr.account_profile_ref_id) as sort_order, sett.name, sett.value
	from account_profile_ref profr
	left join account_profile prof USING(account_profile_id)
	left join account_profile_setting sett USING(account_profile_id)

	UNION

	select account_id, 'base' as profile_name, 0 as account_profile_id, 0 as sort_order, name, value
	from account_setting

) as temp

ORDER by account_id ASC, sort_order ASC, name ASC;


-- this view contains the "valid" field that is calculated so that only one valid setting can exist for any given name.
-- account_setting always has the precedence, followed by profile inherited settings in sort_order order

DROP VIEW IF EXISTS core.account_setting_prioritized CASCADE;
CREATE VIEW core.account_setting_prioritized AS

select a.*, b.account_id IS NULL AS valid, b.account_profile_id AS overridden_by
from account_setting_flat a
left join account_setting_flat b ON b.account_id=a.account_id AND b.sort_order < a.sort_order AND a.name=b.name;


