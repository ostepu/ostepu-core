<?php
#region VeranstaltungenEinrichten
if (!$console || !isset($segmentVeranstaltungenEinrichten)){
    if ($selected_menu === 4 && isset($segmentVeranstaltungenEinrichten)){
        $text='';
        $text .= "<tr><td colspan='2'>".Sprachen::Get('courses','description')."</td></tr>";                       
        $text .= Design::erstelleZeile($console, Sprachen::Get('courses','createTables'), 'e', '', 'v', Design::erstelleSubmitButton('actionInstallCourses'), 'h');
        $text .= Design::erstelleZeile($console, Sprachen::Get('courses','details'), 'e', Design::erstelleAuswahl($console, $data['C']['c_details'], 'data[C][c_details]', 'details', null), 'v');
        
        if ($installCourses){
            foreach ($installCoursesResult as $courseid => $dat){
                $text .= "<tr><td class='e' rowspan='1'>({$dat['course']->getId()}) {$dat['course']->getSemester()}</td><td class='v'>{$dat['course']->getName()}</td><td class='e'><div align ='center'>".((isset($dat['status']) && $dat['status']===201) ? Sprachen::Get('main','ok') : "<font color='red'>".Sprachen::Get('main','fail')." ({$dat['status']})</font>")."</align></td></tr>";
            }
            $text .= Design::erstelleInstallationszeile($console, $installFail, $fail, $errno, $error);
        }

        echo Design::erstelleBlock($console, Sprachen::Get('courses','title'), $text);
    } else {
        $text = '';
        $text .= Design::erstelleVersteckteEingabezeile($console, $data['C']['c_details'], 'data[C][c_details]', null,true);
        echo $text;
    }
}

if ($simple && isset($segmentVeranstaltungenEinrichten)){
    if ($installCourses){
        $text = "<<< ".Sprachen::Get('courses','title')." >>>\n";
        $text .= Design::erstelleZeile($console, Sprachen::Get('courses','countCourses'), 'e', count($installCoursesResult) , 'v');
        $text .= Design::erstelleInstallationszeile($console, $installFail, $fail, $errno, $error);
        $text .= "\n";
        echo $text;
    }
}

$segmentVeranstaltungenEinrichten = true;
#endregion VeranstaltungenEinrichten