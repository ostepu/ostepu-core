/**
 * @file GetAllApprovalConditions.sql
 * gets all approval conditions from %ApprovalCondition table
 * @author  Till Uhlig
 * @result AC_id, C_id, ET_id, AC_percentage
 */

select 
    AC_id, C_id, ET_id, AC_percentage
from
    ApprovalCondition