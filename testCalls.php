<?php
set_time_limit(0);
header('Content-type: text/html');
include('Assistants/Request.php');


$list = array('http://localhost/uebungsplattform/logic/LGetSite/student/user/2/course/2','http://localhost/uebungsplattform/logic/LGetSite/tutorassign/user/2/course/2/exercisesheet/14','http://localhost/uebungsplattform/logic/LGetSite/uploadhistoryoptions/user/2/course/2','http://localhost/uebungsplattform/logic/LGetSite/tutorupload/user/2/course/2','http://localhost/uebungsplattform/logic/LGetSite/markingtool/user/2/course/33/exercisesheet/14','http://localhost/uebungsplattform/logic/LGetSite/coursemanagement/user/2/course/33','http://localhost/uebungsplattform/logic/LGetSite/condition/user/2/course/2','http://localhost/uebungsplattform/logic/LGetSite/admin/user/2/course/2');
//$list = array('http://localhost/uebungsplattform/logic/LGetSite/markingtool/user/2/course/2/exercisesheet/14');

$list2 = array_merge(array(),$list);
$type = array('Normal','CacheManager');
$cacheEnabled = array(false,true);        
$time=array();
$anz = 10;
$sum=array();

echo "<html><body><table>";
for ($b=0;$b<2;$b++){
    echo '<th><td colspan=3>'.$type[$b]."</td></th>";
    if (!isset($time[$b])) $time[$b] = array();
    
    for($i=0;$i<$anz;$i++){
        shuffle($list2);
        foreach($list2 as $elem){
            CacheManager::setCacheEnabled($cacheEnabled[$b]);
            $begin = microtime(true);
            $answ = Request::get($elem,array(),'',false,false);
            $t=microtime(true) - $begin;
            if (!isset($time[$b][$elem])) $time[$b][$elem]=0;
            $time[$b][$elem]+=$t;
            if (!isset($sum[$b])) $sum[$b]=0;
            $sum[$b]+=$t;
            CacheManager::reset();
        }
    }

    foreach($list as $elem){
        echo '<tr>';
        echo '<td>'.(round(($time[$b][$elem]/$anz),3)*1000). 'ms</td>';
        
        echo '<td>'.$elem;
        if ($b==1){
            $diff = $time[1][$elem]/$time[0][$elem]-1;
            echo '<td><font color="'.($diff>=0?'red">+':'green">').round($diff*100,0)."%</font></td>";
        } else {
            echo '<td></td>';
        }
        echo "</tr>";
    }

    echo "<tr><td colspan=1>Gesamt:</td><td><font color='blue'><b>".(round(($sum[$b]),3)*1000). "ms</b></font></td>";
    if ($b==1){
        $diff = $sum[1]/$sum[0]-1;
        echo '<td><font color="'.($diff>=0?'red"><b>+':'green"><b>').round($diff*100,0)."%</b></font></td>";
    } else {
        echo '<td></td>';
    }
    echo "</tr>";
    echo "<tr><td colspan=1>Durchschnitt:</td><td><font color='blue'><b>".(round(($sum[$b]/($anz*8)),3)*1000). "ms</b></font></td>";
    echo '<td></td>';
    echo "</tr>";
    echo "<tr><td>&nbsp;</td><td></td><td></td></tr>";
}
echo "</table></body></html>";
?>