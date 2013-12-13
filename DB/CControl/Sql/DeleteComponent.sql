DELETE
FROM Link
WHERE CO_id_target = $componentid or CO_id_target = $componentid
---
DELETE
FROM Component
WHERE CO_id = '$componentid' or CO_name = '$componentid'