<?php
set_time_limit(0);
header('Content-type: text');
include('Assistants/Request.php');


$sum=0;
$anz = 100;
$listOfCalls = array('http://localhost/uebungsplattform/DB/DBUser/user/course/2/status/0','http://localhost/uebungsplattform/logic/LGetSite/markingtool/user/2/course/2/exercisesheet/73/tutor/2','http://localhost/uebungsplattform/logic/LGetSite/uploadhistoryoptions/user/2/course/2','http://localhost/uebungsplattform/logic/LGetSite/tutorassign/user/2/course/2/exercisesheet/73','http://localhost/uebungsplattform/logic/LGetSite/condition/user/2/course/2','http://localhost/uebungsplattform/logic/LGetSite/accountsettings/user/2','http://localhost/uebungsplattform/DB/DBUser/user/course/2/status/0','http://localhost/uebungsplattform/DB/DBUser/user/course/2/status/1','http://localhost/uebungsplattform/DB/DBSubmission/submission/exercisesheet/73/selected','http://localhost/uebungsplattform/DB/DBProcess/processList/process/course/2','http://localhost/uebungsplattform/DB/DBChoice/choice/1','http://localhost/uebungsplattform/DB/DBApprovalCondition/approvalcondition/course/2','http://localhost/uebungsplattform/DB/DBProcess/process/exercisesheet/75','http://localhost/uebungsplattform/DB/DBExercise/exercise/169','http://localhost/uebungsplattform/DB/DBControl/exercisesheet/exercisesheet/75/exercise','http://localhost/uebungsplattform/DB/DBApprovalCondition/approvalcondition/course/2','http://localhost/uebungsplattform/DB/DBCourseStatus/coursestatus/course/2/user/2','http://localhost/uebungsplattform/logic/LGetSite/createsheet/user/2/course/2','http://localhost/uebungsplattform/logic/LController/DB/exercise/169','http://localhost/uebungsplattform/logic/LController/DB/exercisefiletype','http://localhost/uebungsplattform/DB/DBExerciseSheet/exercisesheet/exercisesheet/75/exercise','http://localhost/uebungsplattform/DB/DBForm/form/exercisesheet/75','http://localhost/uebungsplattform/DB/DBProcess/process/exercisesheet/75','http://localhost/uebungsplattform/DB/DBForm/link/exists/course/2','http://localhost/uebungsplattform/DB/DBExerciseType/exercisetype');
for($i=0;$i<$anz;$i++){
$begin = microtime(true);
//'http://localhost/uebungsplattform/logic/LGetSite/admin/user/'.$i.'/course/2'
$list= array(25,26,27);
$answ = Request::get('http://localhost/uebungsplattform/DB/DBExerciseSheet/exercisesheet/course/'.($list[$i%3]).'/exercise',array(),'',false,false);
$sum+=microtime(true) - $begin;
if ($i+1<$anz)
    foreach($listOfCalls as $call)
        $answ = Request::get($call,array(),'',false,false);
}

echo (round(($sum),2)). 's'."\n";
echo (round(($sum/$anz),2)). 's';

?>