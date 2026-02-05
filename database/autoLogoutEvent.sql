-- Drop the old event if it exists
DROP EVENT IF EXISTS auto_logout_users;

-- Create new event that runs every minute to check for auto-logout time
-- This event will automatically set logout_time for users who haven't logged out yet
-- and update their user_status to 'outside'

DELIMITER $$

CREATE EVENT auto_logout_users
ON SCHEDULE EVERY 1 MINUTE
DO
BEGIN
    DECLARE logout_time_setting VARCHAR(5);
    DECLARE current_time_str VARCHAR(5);
    DECLARE logout_datetime DATETIME;

    -- Get the configured auto-logout time from settings table
    SELECT value INTO logout_time_setting
    FROM settings
    WHERE `key` = 'auto_logout_time'
    LIMIT 1;

    -- Get current time in HH:MM format
    SET current_time_str = DATE_FORMAT(NOW(), '%H:%i');

    -- Check if current time matches the logout time
    IF current_time_str = logout_time_setting THEN
        -- Build the logout datetime (today's date + configured time)
        SET logout_datetime = CONCAT(CURDATE(), ' ', logout_time_setting);

        -- Update all attendance records that don't have a logout_time yet
        -- Only update if their login_time is today
        UPDATE attendance
        SET logout_time = logout_datetime
        WHERE logout_time IS NULL
        AND DATE(login_time) = CURDATE();

        -- Update user_status to 'outside' for all users who were automatically logged out
        UPDATE users
        SET user_status = 'outside'
        WHERE user_status = 'inside'
        AND id IN (
            SELECT user_id FROM attendance
            WHERE logout_time = logout_datetime
            AND DATE(login_time) = CURDATE()
        );
    END IF;
END$$

DELIMITER ;

-- Note: This event checks every minute if the current time matches the configured logout time.
-- When it matches, it will automatically log out all users who haven't logged out yet
-- and update their user_status to 'outside'.
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

