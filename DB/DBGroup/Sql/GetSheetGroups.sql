SELECT 
    U2.U_id,
    U2.U_username,
    U2.U_firstName,
    U2.U_lastName,
    U2.U_email,
    U2.U_title,
    U2.U_flag,
    U.U_id as U_id2,
    U.U_username as U_username2,
    U.U_firstName as U_firstName2,
    U.U_lastName as U_lastName2,
    U.U_email as U_email2,
    U.U_title as U_title2,
    U.U_flag as U_flag2,
    G.ES_id
FROM
    ((`Group` G
    join `Group` G2 ON (G.ES_id = G2.ES_id
        and G.U_id_member = G2.U_id_member))
    join User U ON U.U_id = G.U_id_member
        and (G2.U_id_leader <> U.U_id
        or G2.U_id_leader = G.U_id_leader))
        left join
    User U2 ON U2.U_id = G2.U_id_leader
        and U2.U_id <> G.U_id_member
WHERE
    G.U_id_leader = G2.U_id_leader
        and G.ES_id = $esid