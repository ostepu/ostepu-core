SELECT 
    U.U_id,
    U.U_username,
    U.U_firstName,
    U.U_lastName,
    U.U_email,
    U.U_title,
    U.U_flag,
    U.U_password,
        U.U_salt,
    U.U_failed_logins,
    CS.CS_status,
    C.C_id,
    C.C_name,
    C.C_semester,
    C.C_defaultGroupSize
FROM
`Group` G
join
`Group` G2
on (G.ES_id = G2.ES_id and G.U_id_member = G2.U_id_member)
join
(User U
        left join
    CourseStatus CS ON (U.U_id = CS.U_id)
        left join
    Course C ON (CS.C_id = C.C_id))
on U.U_id = G2.U_id_leader
WHERE
    G.U_id_leader = '$userid' and G.ES_id = '$esid'