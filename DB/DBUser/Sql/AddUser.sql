SELECT * 
FROM user 
WHERE EXISTS ( U_username = '".§U_username."' )
PRINT Dieser User existiert bereits -- Fehlercode?
ELSE
insert into user ('".§columns."')
values ( '".§values."')

