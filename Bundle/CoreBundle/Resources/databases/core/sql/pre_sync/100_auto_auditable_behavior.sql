/* file generation UUID: 598c4f2070f02 */

--
-- Remove Auditing triggers for async_job
--

DROP FUNCTION if EXISTS async_job_audf() CASCADE;




--
-- Remove Auditing triggers for user_notification
--

DROP FUNCTION if EXISTS user_notification_audf() CASCADE;



