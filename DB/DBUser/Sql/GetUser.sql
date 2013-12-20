SELECT U_id, U_username, U_firstName, U_lastName, U_email, U_title, U_flag
FROM User
WHERE U_id = $userid or U_username = '$userid'