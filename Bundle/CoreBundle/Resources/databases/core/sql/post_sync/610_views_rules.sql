SET lc_messages TO 'en_US.UTF-8';


DROP VIEW IF EXISTS core.widget_rule_calc CASCADE;

CREATE VIEW core.widget_rule_calc AS

    SELECT
        r.widget_rule_id,
        count(subrule.widget_rule_id) AS children_nr

        FROM widget_rule r

        LEFT JOIN widget_rule subrule ON subrule.parent_widget_rule_id = r.widget_rule_id

        GROUP BY r.widget_rule_id;

COMMENT ON VIEW core.widget_rule_calc IS 'calculated fields for widget_rule';