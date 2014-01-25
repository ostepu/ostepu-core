/**
 * @file GetIncreaseUserFailedLogin.sql
 * increases the failed logins counter and returns the user
 * @author Till Uhlig
 * @param int \$userid a %User identifier
 * @result 
 * - U, the user data
 * - CS, the course status data
 * - C, the course data
 */
 
call IncFailedLogins('$userid');