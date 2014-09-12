<?php
/**
 * @file GetCourseExercises.sql
 * gets all course exercises from %Exercise table
 * @author Till Uhlig
 * @param int \$courseid an %Course identifier
 * @result 
 * - E, the exercise data
 * - F, the submission file
 * - S, the submission data
 * - SS, the selected submission data
 */
?>
 
select 
    E.E_id,
    E.ES_id,
    E.ET_id,
    E.E_maxPoints,
    E.E_bonus,
    E.E_id_link,
    E.E_linkName,
    F.F_id,
    F.F_displayName,
    F.F_address,
    F.F_timeStamp,
    F.F_fileSize,
    F.F_comment,
    F.F_hash,
    EFT_id,
    EFT_text
    <?php if ($sub==1){ ?> 
    ,
    S.U_id as U_id2,
    S.S_id as S_id2,
    S.F_id_file as F_id_file2,
    S.S_comment as S_comment2,
    S.S_date as S_date2,
    SS.S_id_selected as S_selected2,
    S.S_accepted as S_accepted2,
    S.S_leaderId as S_leaderId2,
    S.S_hideFile as S_hideFile2,
    S.E_id as E_id2
    <?php } ?>
from
    Exercise E
        left join
    ExerciseFileType EFT ON E.E_id = EFT.E_id
        left join
    (Attachment A
    natural join File F) ON E.E_id = A.E_id
        left join
    (Submission S
    left join SelectedSubmission SS ON S.S_id = SS.S_id_selected) ON S.E_id = E.E_id
where
    E.C_id = '<?php echo $courseid; ?>'