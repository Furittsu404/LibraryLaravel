-- Drop the old event if it exists
DROP EVENT IF EXISTS auto_logout_users;

-- Create new event that runs daily at the configured time
-- This event will automatically set logout_time for users who haven't logged out yet



-- Note: This event checks every minute if the current time matches the configured logout time.
-- When it matches, it will automatically log out all users who haven't logged out yet.
--
-- To manually run this event (for testing):
-- CALL the event body directly or wait for the scheduled time.
--
-- To view the event:
-- SHOW EVENTS WHERE Name = 'auto_logout_users';
--
-- To disable the event:
-- ALTER EVENT auto_logout_users DISABLE;
--
-- To enable the event:
-- ALTER EVENT auto_logout_users ENABLE;
--
-- Make sure your MySQL server has event scheduler enabled:
-- SET GLOBAL event_scheduler = ON;
