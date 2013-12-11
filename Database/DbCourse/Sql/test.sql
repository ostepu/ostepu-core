select *
from (coursestatus CS natural join user U) natural join course C
where exists (select * from coursestatus B
where B.C_id = 2 and B.U_id = U.U_id);