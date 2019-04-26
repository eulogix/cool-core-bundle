/* file generation UUID: 5cc2f85170daf */

--
-- Auditing triggers for async_job
--

CREATE OR REPLACE FUNCTION async_job_audf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        IF (TG_OP = 'UPDATE') THEN
            NEW.record_version = COALESCE(NEW.record_version,1)+1;
            NEW.update_date = NOW();
            NEW.update_user_id = core.get_logged_user();
        ELSIF (TG_OP = 'INSERT') THEN
            NEW.record_version = 1;
            NEW.creation_date = COALESCE( NEW.creation_date, NOW() );
            NEW.creation_user_id = COALESCE( NEW.creation_user_id, core.get_logged_user() );
        END IF;
        RETURN NEW;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER async_job_audf_trg BEFORE INSERT OR UPDATE ON async_job
    FOR EACH ROW EXECUTE PROCEDURE async_job_audf();



--
-- Auditing triggers for user_notification
--

CREATE OR REPLACE FUNCTION user_notification_audf() RETURNS TRIGGER AS
$functionBlock$
    BEGIN
        IF (TG_OP = 'UPDATE') THEN
            NEW.record_version = COALESCE(NEW.record_version,1)+1;
            NEW.update_date = NOW();
            NEW.update_user_id = core.get_logged_user();
        ELSIF (TG_OP = 'INSERT') THEN
            NEW.record_version = 1;
            NEW.creation_date = COALESCE( NEW.creation_date, NOW() );
            NEW.creation_user_id = COALESCE( NEW.creation_user_id, core.get_logged_user() );
        END IF;
        RETURN NEW;
    END;
$functionBlock$
LANGUAGE plpgsql;

CREATE TRIGGER user_notification_audf_trg BEFORE INSERT OR UPDATE ON user_notification
    FOR EACH ROW EXECUTE PROCEDURE user_notification_audf();


