/* file generation UUID: 5cc2f85170daf */

--
-- Remove Auditing triggers for async_job
--

DROP FUNCTION if EXISTS async_job_audf() CASCADE;




--
-- Remove Auditing triggers for user_notification
--

DROP FUNCTION if EXISTS user_notification_audf() CASCADE;



