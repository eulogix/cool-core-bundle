SET lc_messages TO 'en_US.UTF-8';

DO $$ BEGIN
    INSERT INTO lookups.core_user_reminder_category (value, dec_en) select distinct category, category from core.user_reminder;
EXCEPTION WHEN OTHERS THEN END; $$;